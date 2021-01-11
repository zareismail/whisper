<template>  
  <div>
    <span class="cursor-pointer" v-show="closed" @click="handleOpen"><ChatIcon /></span>
    <div class="messenger flex flex-col card" v-show="! closed">
    <div class="header flex rounded-t-lg bg-20 justify-between p-2 pr-4">
      <b class="text-danger p-2 cursor-pointer" @click="handleClose">X</b>
     <!-- <input type="text" name="search" :placeholder="__('Search contact')"> -->
    </div>
    <div class="contacts flex flex-col">
      <div  
        class="flex p-1 border-b border-solid border-20 cursor-pointer" 
        v-for="contact in contacts"
        :key="contact.id"
        @click="newConversation(contact)"
      >
        <img v-if="contact.avatar" class="mx-2 mt-2" src="contact.avatar" />
        <span v-else class="mx-2 mt-2"><UserIcon /></span>
        <div class="contact pt-2">
          <h5>{{ contact.name }}</h5>
          <h6 class="text-80" v-html="contact.lastMessage" />
        </div>
      </div>
    </div> 
    </div>
  </div> 
</template>

<script> 
  import { 
    Minimum, 
  } from 'laravel-nova'

  export default {  
    props: ['openedConversation'],

    data: () => ({
      resources: [], 
      closed: true,
    }),

    async mounted() { 
      await this.getResources()

      Echo.private(`Whisper.Created`)
        .listen('.whisper.created', (message) => {
          if(message.recipient_id == Nova.config.userId) {
            this.$emit('update', {
              contact: this.getMessageOwner(message),
              message: message
            })
          }
        });
    }, 

    methods: { 
      getMessageOwner(message) {
        return this.contacts.find(contact => contact.id == message.auth_id)
      },
      
      /**
       * Get the resources based on the current page, search, filters, etc.
       */
      getResources() {
        this.loading = true

        this.$nextTick(() => { 
          return Minimum(
            Nova.request().get('/nova-api/' + Nova.config.whisper.users, {
              // params: this.resourceRequestQueryString,
            }),
            300
          ).then(({ data }) => {
            this.resources = []

            this.resourceResponse = data
            this.resources = data.resources 
            // this.perPage = data.per_page 

            // this.loading = false

            Nova.$emit('resources-loaded')
          })
        })
      },
      handleClose() {
        this.closed = true
      },  

      handleOpen() { 
        this.closed = false
        this.$emit('opening') 
      },

      newConversation(contact) {
        this.handleClose()

        this.$emit('conversation', contact)
      }
    },

    computed: {
      contacts() { 
        return this.resources.map(resource => {
          return _.tap({}, contact => {
            resource.fields.forEach(field => {
              contact[field.attribute] = field.value
            }) 
          }) 
        }).filter(resource => resource.id != Nova.config.userId)
      }
    }
  };
</script> 