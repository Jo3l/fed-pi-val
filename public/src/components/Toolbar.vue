<template>

<div id="toolbarContainer">
	
	<ui-toolbar
        brand="Federació de Pilota Valenciana"
        title=""
        type="clear"
        :raised="false"
    >
		<div slot="icon">
			<img src="/static/logo.png">
		</div>
	    <div slot="actions">
	    	<ui-button has-dropdown v-if="$store.getters.isAuthenticated" icon="account_circle" size="small">{{$store.getters.userMail}}
	                <ui-menu
	                    contain-focus
	                    has-icons
	                    slot="dropdown"
	                    :options="userOptions"
	                    @select="selectUserOptions"
	                    @close="$refs.localeSelector.closeDropdown()"
	                ></ui-menu>
	    	</ui-button>
	    	
            <ui-icon-button
            	v-else
                color="black"
                icon="account_circle"
                size="large"
                type="secondary"
                @click="goLogin()"
            ></ui-icon-button>
            <ui-icon-button
            	has-dropdown
                color="black"
                icon="custom"
                size="large"
                type="secondary"
                ref="localeSelector"
                :class="$i18n.locale"
            >
	                <ui-menu
	                    contain-focus
	                    has-icons
	                    slot="dropdown"
	                    :options="localesArray"
	                    @select="selectLang"
	                    @close="$refs.localeSelector.closeDropdown()"
	                ></ui-menu>
            </ui-icon-button>
            <ui-icon-button
                color="black"
                icon="search"
                size="large"
                type="secondary"
                @click="toggleBar()"
            ></ui-icon-button>
                        
	    </div>
	    
	</ui-toolbar>

	<div class="mainMenu">
	    <input id="menu-toggle" type="checkbox" v-model="menuOpen">
	    <label class="menu-button-container" for="menu-toggle">
	    	<div class="menu-button"></div>
	  	</label>
	    <ul class="menu">
	    	<li v-for="menu in $router.options.routes" v-if="menu.lang==$i18n.locale" v-on:click="menuOpen=!menuOpen">
	    		<router-link v-bind:to="menu.path">{{ menu.name }}</router-link>
	    	</li>
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/jugadors' }">Jugadors</router-link></li>
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/clubs' }">Clubs</router-link></li>
	    </ul>
	</div>


	
	<ui-modal ref="login" size="normal" title="Login">
        <div slot="header">
			login
        </div>
	    <button @click="login(user)">Login</button>
		<button @click="register(user)">Register</button>
    </ui-modal>
	
</div>
</template>

<script>


export default {
	name: 'Toolbar',
	data () {
	return {
		loadingBar: false,
		menuOpen: false,
		localesArray: [],
		logged:false,
		userOptions: [{
					label: 'logout',
					action: 'logout',
					icon: 'visibility off',
		   		}],
	}
	},
	methods: {
		goLogin: function() {
			this.$router.push({ path: `/login` });
		},
		selectUserOptions:function(selected){
			if(selected.action=='logout') this.logout();
		},
		selectLang: function(selected) {
			window.location.replace(selected.url);
		},
		toggleBar: function() {
		this.loadingBar = !this.loadingBar;
		console.log(this.loadingBar);
		},
        openModal: function() {
        	this.$refs.login.open();
        },
        closeModal: function() {
            this.$refs.login.close();
        },
		logout: function (user) {
		  var vm=this;
		  this.$store.dispatch('logout').then(function(){
				vm.closeModal();
		  });
	    },
	    register: function (user) {
	      var vm=this;
	      console.log(vm.$auth);
	      /*
	      this.$auth.register(user).then(function () {
	        console.log('autenticat: '+vm.$auth.isAuthenticated());
	      })
	      */
	    }
	
	},
	mounted: function() {

	},
	beforeMount: function() {
		//esta funcio es per a automatitzar el selector de llenguatges a partir del objecte $i18n
		var vm = this;
		var messages = vm.$i18n.messages;
		
		for (var key in messages) {
		   if (messages.hasOwnProperty(key)) {
		   		vm.localesArray.push({
					label: messages[key].common.label,
					url: messages[key].common.url,
					icon: messages[key].common.short,
		   		});
		   }
		}
		
	},
	computed:{
		isAuthenticated: function(){
			return this.$store.getters.isAuthenticated;
		}
	},
	created: function(){
		var vm = this;
		vm.$parent.$on('loadingBar', function(data){
			vm.loadingBar = data;
		});
		
	},
    watch: {

    }
}
</script>

<style lang="less">

#toolbarContainer {
	.menu {
		a {
			text-decoration: none;
			color: #232323;
		&:hover {text-decoration:underline;text-shadow:inherit;}
		&:visited {color: #232323;}
		}
	}
}


</style>