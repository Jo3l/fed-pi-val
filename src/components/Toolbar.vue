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
            <ui-icon-button
                color="black"
                icon="account_circle"
                size="large"
                type="secondary"
                @click="openModal()"
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
	    	<li v-for="menu in $router.options.routes" v-if="menu.lang==$i18n.locale" v-on:click="menuOpen=!menuOpen"><router-link v-bind:to="menu.path">{{ menu.name }}</router-link></li>
	    </ul>
	</div>

	
	<ui-modal ref="login" size="normal" title="Login">
        <div slot="header">
			login
        </div>
	    <button @click="login(user)">Login</button>
		<button @click="register(user)">Register</button>
		<button @click="logout(user)">Logout</button>
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
		user: {
			name:'alfon7',
			email:'alfons@algemesi.info',
			password:'inmersa',
			sexo:'ninja'
		}
	}
	},
	methods: {
		selectLang: function(lang) {
			window.location.replace(lang.url);
		},
		toggleBar: function() {
		this.loadingBar = !this.loadingBar;
		console.log(this.loadingBar);
		},
        openModal: function(day) {
        	this.$refs.login.open();
        },
        closeModal: function(ref) {
            this.$refs.login.close();
        },
		login: function (user) {
		  var vm=this;
	      this.$auth.login(user).then(function () {
	        console.log('autenticat: '+vm.$auth.isAuthenticated());
	      })
	    },
		logout: function (user) {
		  var vm=this;
	      this.$auth.logout(user).then(function () {
	        console.log('autenticat: '+vm.$auth.isAuthenticated());
	        vm.$refs.login.close();
	      })
	    },
	    register: function (user) {
	      var vm=this;
	      this.$auth.register(user).then(function () {
	        console.log('autenticat: '+vm.$auth.isAuthenticated());
	      })
	    }
	
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
	
.menu {
	a {
		text-decoration: none;
		color: #232323;
	&:visited {color: #232323;}
	}
}

</style>