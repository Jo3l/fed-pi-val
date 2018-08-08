//import promise from 'es6-promise' //this is needed for make axios work on ie11
//promise.polyfill();  //this is needed for make axios work on ie11

import Vue from 'vue'
import KeenUI from 'keen-ui'
import VueProgressiveImage from 'vue-progressive-image'
import Date from '../vendor/datejs'
import axios from './axios'
import store from './store'
import router from './routes'
import i18n from './lang'
import css from './assets/less/app.less'


Vue.use(KeenUI)
Vue.use(VueProgressiveImage)

Vue.prototype.$http = axios;

import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas
import Cookie from './components/custom/Cookie.vue' //este se carga aqui ya que esta fuera de las rutas

Vue.prototype.$eventHub = new Vue(); // Global event bus

new Vue({
	el: '#app',
	i18n,
	router,
	store,
	components: { Toolbar, Cookie },
	data: {
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
	methods: {
	},
	mounted: function () {
	},
	created: function () {
	}
})
