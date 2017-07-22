import Vue from 'vue'
import VueStash from 'vue-stash'
import VueI18n from 'vue-i18n'
import VueRouter from 'vue-router'
import VueCarousel from 'vue-carousel'
import KeenUI from 'keen-ui'
import axios from 'axios'
import { routes } from './routes'
import es_ES from './lang/es-ES.js'
import val_ES from './lang/val-ES.js'

//import Date from 'datejs'

import css from './assets/less/app.less'

import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas

Vue.use(VueStash)
Vue.use(VueI18n)
Vue.use(VueRouter)
Vue.use(KeenUI)
Vue.use(VueCarousel)

axios.defaults.headers.post['Content-Type'] = 'application/json';
axios.defaults.baseURL = '/api/index.php';  //set the baseurl from api

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
  VueStash,
  components: { Toolbar, 'carousel': VueCarousel.Carousel, 'slide': VueCarousel.Slide },
  data: {
        store: {
    		news: null,
    	}
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
})
