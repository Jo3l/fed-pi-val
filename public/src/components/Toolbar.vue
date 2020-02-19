<template>

<div id="toolbarContainer">
	
	<ui-toolbar
        brand="Federació de Pilota Valenciana"
        title=""
        type="clear"
        :raised="false"
    >
        <ui-icon-button has-dropdown
        	v-if="isMobileDevice()&&$store.getters.isAuthenticated"
	        color="black"
	        icon="account_circle"
	        size="large"
	        type="secondary"
	    >
	        <ui-menu
	            contain-focus
	            has-icons
	            slot="dropdown"
	            :options="userOptions"
	            @select="selectUserOptions"
	            @close="$refs.localeSelector.closeDropdown()"
	        ></ui-menu>
        </ui-icon-button>
	    
        <ui-icon-button
    		v-if="isMobileDevice()"
        	class="cartBasket mobile"
            color="black"
            icon="shopping_cart"
            size="large"
            type="secondary"
            @click="visibleCart=!visibleCart"
        >
        	<span class="ui-icon material-icons shopping_cart">shopping_cart<div class="amount" v-if="countCart>0">{{countCart}}</div></span>
        </ui-icon-button>
		            	
		<div slot="icon">
			<img src="/static/logo.png">
		</div>
	    <div slot="actions">

            <ui-icon-button
                color="black"
                icon="search"
                size="large"
                type="secondary"
                @click="$router.push({ path: '/'+$i18n.locale+'/'+$i18n.t('common.searcher') } )"
            ></ui-icon-button>            
	    	
	    	<ui-button class="actionDesktop" has-dropdown v-if="$store.getters.isAuthenticated" icon="account_circle" size="small">{{$store.getters.userMail}}
	                <ui-menu
	                    contain-focus
	                    has-icons
	                    slot="dropdown"
	                    :options="userOptions"
	                    @select="selectUserOptions"
	                    @close="$refs.localeSelector.closeDropdown()"
	                ></ui-menu>
	    	</ui-button>
	    	
	    	<ui-button class="actionMobile" has-dropdown v-if="$store.getters.isAuthenticated" icon="account_circle" size="small">
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
                @click="$router.push({ path: `/login` })"
            ></ui-icon-button>
            
            <ui-icon-button
            	class="cartBasket"
                color="black"
                icon="shopping_cart"
                size="large"
                type="secondary"
                @click="toggleCart"
            >
            	<span class="ui-icon material-icons shopping_cart">shopping_cart<div class="amount" v-if="countCart>0">{{countCart}}</div></span>
            </ui-icon-button>
            
            <ui-icon-button
            	has-dropdown
                color="black"
                icon="custom"
                size="large"
                type="secondary"
                style="margin-right:1em;"
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
	    	<li v-if="!$store.getters.isAuthenticated" class="show-on-mobile" @click="$router.push({ path: `/login` })">{{$t('common.login')}}</li>
	    	<li v-if="!$store.getters.isAuthenticated" class="show-on-mobile" @click="$router.push({ path: '/'+$i18n.locale+'/'+$i18n.t('common.searcher') } )">{{$t('common.search')}}</li>
	    	<li v-if="!$store.getters.isAuthenticated"><social></social></li>
	    </ul>
	    <ul class="menu" v-if="$store.getters.isAuthenticated">
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/jugadors' }">Jugadors</router-link></li>
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/clubs' }">Clubs</router-link></li>
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/productes' }">Productes</router-link></li>
			<li v-if="$store.getters.isAuthenticatedWithRole(0)"><router-link :to="{ path: '/admin/comandes' }">Comandes</router-link></li>
			<li v-if="$store.getters.isAuthenticatedWithExactRole(10)"><router-link :to="{ path: '/gestio/club' }">Club</router-link></li>
	    </ul>
	</div>

	
	<div class="closeCart" v-if="visibleCart" @click="toggleCart()"></div>
	<cart-list v-if="visibleCart"></cart-list>
	
</div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

import CartList from './shop/CartList.vue'
import Social from './Social.vue'

export default {
	name: 'Toolbar',
	components: { CartList, Social },
	data () {
	return {
		loadingBar: false,
		menuOpen: false,
		localesArray: [],
		logged:false,
		visibleCart:false,
		userOptions: [{
					label: 'logout',
					action: 'logout',
					icon: 'visibility off',
		   		}],
	}
	},
	methods: {
		toggleCart: function() {
			var vm= this;
			vm.visibleCart=!vm.visibleCart;
			console.log(vm.visibleCart)
			window.document.querySelector('#intergramRoot').style.display= vm.visibleCart?'block':'none';
		},
		isMobileDevice: function() {
		    return document.body.clientWidth<480;
		},
		selectUserOptions:function(selected){
			if(selected.action=='logout') this.logout();
		},
		selectLang: function(selected) {
			window.location.replace(selected.url);
		},
		toggleBar: function() {
		this.loadingBar = !this.loadingBar;
		},
		logout: function (user) {
		  var vm=this;
		  this.$store.dispatch('logout').then(function(){
				//vm.closeModal();
		  });
	    },
	    register: function (user) {
	      var vm=this;
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
	computed: {
	    ...mapGetters({
	    	countCart: 'countCart',
	    	carto: 'cart',
	    	checkoutStatus: 'checkoutStatus',
	    })
	},
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

#toolbarContainer {
	width:100%;
	@media print {    
    	display: none !important;
	}
	.closeCart{
		position: fixed;
	    width: 100%;
	    top: 0;
	    left: 0;
	    height: 100%;
	    cursor:pointer;
	    z-index:9;
		background-color:rgba(255,255,255,0.2);
	}
	.menu {
		&+.menu{
			margin:20px 0;
		}
		.show-on-mobile{
			cursor:pointer;
			@media(min-width:@screenMobile) {
				display:none;
			}
		}
		.separator {
			@media(max-width:@screenMobile) {
				display:none;
			}
		}
		li:first-child { 
			@media(min-width:@screenMobile) {
				margin-left:57px !important;
			}
		}
		a {
			text-decoration: none;
			color: #232323;
			&:hover {
				text-decoration:none;
				text-shadow:inherit;
				border-bottom: 3px solid @fedcolor;
			}
			&:visited {color: #232323;}
			&.router-link-active {
				border-bottom: 3px solid @fedcolor;
			}
		}
	}
	
	.cartBasket.mobile {
		display:none;
		@media(max-width:@screenMobile) {
			display:block;
		}
	}
	
	.amount{
	    position: absolute;
	    left: 57%;
	    top: -24%;
	    z-index: 10;
	    color: white;
	    font-size: 11px;
	    width: 15px;
	    font-family: 'Yantramanav', monospace;
	    line-height: 16px;
	    height: 15px;
	    border-radius: 50px;
	    background-color: @fedcolor;
	    opacity:.75;
	}
	.actionDesktop {
		@media(max-width:@screenTablet) {
			display:none;
		}
	}
	.actionMobile {
		@media(min-width:@screenTablet) {
			display:none;
		}
	}
}

.ui-button { min-width:auto; }
</style>