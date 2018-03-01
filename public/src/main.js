//import promise from 'es6-promise' //this is needed for make axios work on ie11
//promise.polyfill();  //this is needed for make axios work on ie11

import Vue from 'vue'
import Vuex from 'vuex'
import VueI18n from 'vue-i18n'
import VueRouter from 'vue-router'
import KeenUI from 'keen-ui'
import VueAuthenticate from 'vue-authenticate'
import axios from 'axios'
import { cacheAdapterEnhancer, throttleAdapterEnhancer } from 'axios-extensions';
import { routes } from './routes'
import es_ES from './lang/es-ES.js'
import val_ES from './lang/val-ES.js'
//import Date from 'datejs'
import jwt_decode from "jwt-decode"
import VueProgressiveImage from 'vue-progressive-image'
import css from './assets/less/app.less'
import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas

Vue.use(Vuex)
Vue.use(VueI18n)
Vue.use(VueRouter)
Vue.use(KeenUI)
Vue.use(VueProgressiveImage)
Vue.use(VueAuthenticate)

Vue.prototype.$http = axios.create({
	withCredentials : true,
	baseURL: window.location.hostname=="fedpival2.indiza.com" ? '/old_api/index.php' : '/api',
	headers: { 'Cache-Control': 'no-cache', 'Content-Type': 'application/json' },
	adapter: cacheAdapterEnhancer(axios.defaults.adapter, true)
});


var vueAuth = VueAuthenticate.factory(Vue.prototype.$http, {
  baseUrl: '',
  tokenName: 'access_token',
})


const store = new Vuex.Store({
  
  state: {
    isAuthenticated: vueAuth.isAuthenticated()
  },

  getters: {
    isAuthenticated: function () {
      return store.state.isAuthenticated;
    },
    isAuthenticatedWithRole: function (state) {
    	return function (n) {
			return state.isAuthenticated && jwt_decode(vueAuth.getToken() ).data.rol <= n;
    	}
    },
    role: function(state) {
    	if(state.isAuthenticated) return jwt_decode(vueAuth.getToken() ).data.rol;
    },
    userMail: function(state) {
    	if(state.isAuthenticated)  return jwt_decode(vueAuth.getToken() ).data.email;
    }
  },

  mutations: {
    isAuthenticated: function (state, payload) {
      state.isAuthenticated = payload.isAuthenticated;
    }
  },

  actions: {
    login: function (context, payload) {
      vueAuth.login(payload.user, payload.requestOptions).then(
      	
      	function (response) {
      		
	        return context.commit('isAuthenticated', {
	          isAuthenticated: vueAuth.isAuthenticated()
	        });
	
    	}
      )

    },
    logout: function (context, payload) {
      vueAuth.logout().then(
      	function () {return context.commit('isAuthenticated', {isAuthenticated: vueAuth.isAuthenticated()});}
      );
    }
  }
})


const router = new VueRouter({
  mode: 'history',
  base: __dirname,
  routes: routes
})


const i18n = new VueI18n({
  locale: 'val',
  fallback: 'es',
  messages: {
	  "es": es_ES,
	  "val": val_ES
	}
})

new Vue({
  el: '#app',
  i18n,
  router,
  store,
  components: { Toolbar },
  data: {
 
  },
  methods: {

  },
  beforeCreate: function () {
  	
  	var language = this.$route.fullPath.split("/")[1]; //forzamos el idioma dependiendo de la url
  	
  	if(this.$route.fullPath=='/') {
  		//meter aqui deteccion idioma o cookie o localstorage
  	}
  	else if(this.$route.fullPath=='/404') {
  		
  	}
  	else if(typeof language !== 'undefined' && language in this.$i18n.messages) {
  		this.$i18n.locale = language;
  	} else {
  	//	window.location.href = "/404";
  	}
  },
	mounted: function () {
		var vm=this;
  },
	computed: {

    },
    watch: {

    },
    created: function() {

    }
})
