<template>
  <div class="whisper-container flex flex-col"> 
    <conversation 
      class="ml-auto"
      v-for="conversation in conversations" 
      :conversation="conversation" 
      :key="conversation.id"
      :active="isOpen(conversation)"
      @collapse="closeConversations()"
      @expand="openConversation(conversation)"
      @close="closeConversation(conversation)"
      :ref="`conversation`+conversation.id"
    />

    <contacts 
      class="ml-auto"  
      @opening="closeConversations"
      @conversation="handleNewConversation($event)"
      @update="handleUpdateConversation($event)" 
    />
  </div>
</template>

<script> 
import Contacts from './Contacts.vue'
import Conversation from './Conversation.vue'

export default {
  components: {
    Conversation, Contacts
  },

  data: () => ({ 
    conversations: [],
    openedConversation: null,
  }),

  mounted() { 
  },

  methods: { 
    async handleUpdateConversation(e) {   
      await this.createConversation(e.contact)  

      this.$refs['conversation' + e.contact.id][0].push(e.message)
    },

    handleNewConversation(contact) {  
      this.createConversation(contact)
      this.openConversation(contact)
    },

    createConversation(contact) { 
      if(! this.hasConversation(contact)) {
        this.conversations.push({
          id: contact.id,
          contact: contact,
        })
      } 

      return this.conversations.find(conversation => conversation.id == contact.id)
    },

    hasConversation(contact) {
      return this.conversations.find(conversation => conversation.id == contact.id)
    },

    openConversation(contact) {
      this.openedConversation = contact.id
    },

    closeConversations() {
      this.openedConversation = null
    },

    isOpen(conversation) {
      return conversation.id == this.openedConversation 
    },

    closeConversation(conversation) {
      this.conversations = this.conversations.filter(item => conversation.id !== item.id)
    }
  }
};
</script>

<style>
.whisper-container { 
  position: fixed;
  bottom: 0;
  right: .5%;
  z-index: 1000000;
}
.whisper-container .messenger {
  width: 320px;
  max-height: 50vh;
  height: 500px;
}
.whisper-container .contacts {
  overflow: scroll;
  overflow-x: hidden;
}
</style>
