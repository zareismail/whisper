Nova.booting((Vue, router, store) => {
	Vue.component('whisper', require('./components/Whisper.vue'))
	Vue.component('UserIcon', require('./components/UserIcon.vue'))
	Vue.component('ChatIcon', require('./components/ChatIcon.vue')) 
})
