<template>
    <transition name="fade">
	    <div>
	    	
			<club-nou-jugador nofoto="true"></club-nou-jugador>
	  
	        <ui-modal ref="error" title="">
	            {{error.response && error.response.data.error?error.response.data.error:error.response}}
		        <div slot="footer">
		            <ui-button @click="closeModal('error')">Ok</ui-button>
		        </div>
	        </ui-modal>
        
	    </div>
    </transition>
</template>

<script>

import ClubNouJugador from './gestio/ClubNouJugador.vue';


export default {
  	components: { ClubNouJugador },
  	
	data () {
		return {
			error:{},
		}
	},
	methods: {
	},
	mounted: function () {

	},
	created: function() {

	},
	computed: {

	},
}
</script>

<style lang="less" scoped>

@import "../assets/less/defines.less";


</style>
