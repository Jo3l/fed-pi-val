<template>
    <transition name="fade">
	    <div class="login">

		<h1>Identificació</h1>
				
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 504.61" height="538.25" width="426.667"><path d="M0 0v324.914l.01-.001c.05-45.462 14.337-89.793 40.595-126.904V0zm79.74 0v155.752a220.13 220.13 0 0 1 40.606-26.96V0zm73.004 0v115.686a220.126 220.126 0 0 1 40.604-8.97V0zm73.002 0v105.055a220.13 220.13 0 0 1 40.603 4.705V0zm-5.443 145.215a179.698 179.698 0 0 0-159.654 97.59c22.103 44.315 83.278 97.622 178.443 117.523 23.006-51.25 34.754-126.626 21.127-210.455a179.698 179.698 0 0 0-39.916-4.658zm53.164 8.047c12.433 82.633.95 157.48-21.773 209.476 42.072 7.253 90.354 8.022 144.444-1.037A179.698 179.698 0 0 0 400 324.912a179.698 179.698 0 0 0-126.533-171.65zM54.053 256.956a179.698 179.698 0 0 0-13.448 67.956 179.698 179.698 0 0 0 89.461 155.195l-.115-.237c33.724-16.415 75.32-51.95 103.583-107.892-91.119-19.827-152.681-69.456-179.481-115.022zm192.194 117.553c-27.346 55.78-67.603 93.087-102.9 112.545a179.698 179.698 0 0 0 76.956 17.556 179.698 179.698 0 0 0 172.446-129.634c-54.63 8.555-103.623 7.314-146.502-.467z" opacity=".97" fill="#87212e"/></svg>
				
				<div class="formulari">

						<ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Correu Usuari"
							type="text"
				            v-model="user.email"
				        ></ui-textbox>

						<ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Password"
							type="password"
				            v-model="user.clau"
				            @keyup.enter.native="login(user)"
				        ></ui-textbox>
				        
				        
				    	<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="login(user)">Accedir</ui-button>

				</div>
			
	    </div>
    </transition>
</template>

<script>

export default {
	name: 'Login',
	data () {
		return {
			user: {
				email:'',
				clau:'',
			}
		}
	},
	methods: {
		login: function (user) {
			var vm=this;
			vm.$store.dispatch('login', { user }).then(function(){
				vm.$router.push({ path: `/` });
			});
	    },
	},
	mounted: function () {
		var vm=this;

	},
	created: function() {
	    if (this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
	}
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";

.login {
	svg {
		position: absolute;
	    left: 5%;
	    opacity: .1;
	    max-width: 60%;
	}
	.formulari {
		width:256px;
		margin: 3em auto;
		input{
			border-bottom: 1px dashed #ccc!important;
			background-color:white;
		}
	}
}


</style>