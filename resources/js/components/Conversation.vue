<template> 
  <div 
    class="card chat-container" 
    :class="{
      'active': active
    }"
  >
    <div 
      class="flex bg-20 p-1 justify-between cursor-pointer" 
      :class="{
        'rounded-t-lg' : active,
        'rounded-lg' : ! active
      }"
    >
      <span class="flex w-full" @click="$emit('expand')" :title="conversation.contact.name">
        <UserIcon height=30 />
        <h5 class="p-2 truncate">{{ conversation.contact.name }}</h5> 
      </span>      
      <h6 v-if="active" class="p-2" @click="$emit('collapse')">v</h6>
      <b v-else class="p-2 text-danger absolute close" @click="$emit('close')">x</b>
    </div>
    <div v-show="active" class="flex flex-col p-4 messages" ref="messenger">
      <div 
        class="mt-1 py-1 message-holder"
        v-for="(message, index) in messages" 
        :key="index"
        :title="message" 
        :class="{
          'text-right': ! isOwner(message) 
        }"
      >{{ message.message }}</div> 
    </div>
    <form v-show="active" class="w-full absolute editor" @submit="send">
      <input 
        type="text" 
        name="message" 
        :placeholder="__('Type something')" 
        class="w-full form-control form-input"
        ref="input" 
        v-model="message"
        max="30" 
      >
    </form>
  </div> 
</template>
<script>
import { uuid } from 'vue-uuid';
import { Minimum } from 'laravel-nova'
import MapResources from './MapResources.vue'

export default {
  mixins: [MapResources],

  props:['conversation', 'active'], 

  data: () => ({
    message: '',
    messages: [],
  }),

  mounted() { 
    this.focus()
    this.getMessages()
  },

  methods: {
    async send(e) {
      e.preventDefault()

      if(this.message.length) { 
        let message = _.tap(this.newMessage(), message => {
          this.message = ''
          this.messages.push(message)
        });
        
        await this.$nextTick(() => { 
          return Minimum(
            Nova.request().post('/nova-api/' + Nova.config.whisper.messages, message),
            300
          ).then(({ data : {resource} }) => {
            // this.messages.push(resource) 
          })
        })
      }  
    }, 

    newMessage() {
      return {  
        message: this.message,
        recipient: this.conversation.contact.id,  
        auth_id: Nova.config.userId,
        uuid: uuid.v4(),
      }
    },

    focus() { 
      this.$refs.input.focus()
    },

    isOwner(message) {
      return Nova.config.userId == message.auth_id
    },
    /**
     * Get the resources based on the current page, search, filters, etc.
     */
    getMessages() {
      this.loading = true

      this.$nextTick(() => { 
        return Minimum(
          Nova.request().get('/nova-api/' + Nova.config.whisper.messages, {
            params: {
              messenger: true, 
              viaResourceId: this.conversation.contact.id, 
            },
          }),
          300
        ).then(({ data }) => {
          this.messages = []

          this.resourceResponse = data
          this.messages = this.mapResources(data.resources).reverse()  
          // this.perPage = data.per_page 

          // this.loading = false

          Nova.$emit('resources-loaded')
        })
      })
    },

    push(message) {
      this.messages.push(message)  

      this.active || Nova.success(this.conversation.contact.name +': '+ message.message) 
      
      setTimeout(() => {
        this.$refs.messenger.scrollTop = this.$refs.messenger.scrollHeight + this.$refs.messenger.clientHeight
      }, 1) 
    }
  },

  computed: {
    uuid() {
      console.log()
      return this.uuid.v4()
    }
  },

  watch: {
    active: function() {
      this.focus()
    },
    messages: function() { 
      setTimeout(() => {
        this.$refs.messenger.scrollTop = this.$refs.messenger.scrollHeight + this.$refs.messenger.clientHeight
      }, 1) 
    }
  }
};
</script>
<style> 
.whisper-container .chat-container { 
  width: 100px;
  position: relative;
}
.whisper-container .chat-container.active, .whisper-container .chat-container:hover {
  width: 250px !important; 
}
.whisper-container .chat-container.active { 
  height: 250px;
  padding-bottom: 41px;
}
.whisper-container .chat-container .messages { 
  height: 160px;
  overflow-y: scroll;
}
.whisper-container .chat-container .editor { 
  bottom: 0;
}
.whisper-container .chat-container .editor input {  
  height: 40px; 
} 
.whisper-container .chat-container .editor input:focus {  
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
}
.whisper-container .close.absolute {  
  right: 0;
}
</style>