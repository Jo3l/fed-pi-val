//import promise from 'es6-promise' //this is needed for make axios work on ie11
//promise.polyfill();  //this is needed for make axios work on ie11

import Vue from 'vue'
import VueI18n from 'vue-i18n'
import VueRouter from 'vue-router'
import KeenUI from 'keen-ui'
import VueAuthenticate from 'vue-authenticate'
import axios from 'axios'
import { routes } from './routes'
import es_ES from './lang/es-ES.js'
import val_ES from './lang/val-ES.js'
//import Date from 'datejs'
import VueProgressiveImage from 'vue-progressive-image'

import css from './assets/less/app.less'

import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas


Vue.use(VueI18n)
Vue.use(VueRouter)
Vue.use(KeenUI)
Vue.use(VueProgressiveImage)

axios.defaults.withCredentials = true;
//axios.defaults.crossDomain = true;

axios.defaults.headers.post['Content-Type'] = 'application/json';

if(window.location.hostname=="fedpival2.indiza.com") {
	axios.defaults.baseURL = '/old_api/index.php';
}
else {
	axios.defaults.baseURL = '/api';
}

Vue.use(VueAuthenticate, {
  tokenName: 'access_token',
  baseUrl: '',
})

Vue.prototype.$http = axios;
//Vue.http.options.emulateJSON = true; 


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
  components: { Toolbar },
  data: {
        store: {
        	newsTeaser: '',
        	newsCarousel: '',
    		news: '',
    	},
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
		console.log('start-autenticat: '+this.$auth.isAuthenticated() + ' - '  +  this.$auth.getToken());
  },
	computed: {
          isAuthenticated: function () {
            console.log(this.$auth.isAuthenticated());
            return this.$auth.isAuthenticated();
          }
    },
})
