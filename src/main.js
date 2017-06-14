import Vue from 'vue'
import VueRouter from 'vue-router'
import VueCarousel from 'vue-carousel'
import KeenUI from 'keen-ui'
import axios from 'axios'

import css from './assets/less/app.less'

import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas

import { routes } from './routes';

Vue.use(VueRouter)
Vue.use(KeenUI)
Vue.use(VueCarousel)


axios.defaults.headers.post['Content-Type'] = 'application/json';
axios.defaults.baseURL = 'api/';  //set the baseurl from api

Vue.prototype.$http = axios;
//Vue.http.options.emulateJSON = true; 


const router = new VueRouter({
  mode: 'history',
  base: __dirname,
  routes: routes
})


new Vue({
  el: '#app',
  components: { Toolbar, 'carousel': VueCarousel.Carousel, 'slide': VueCarousel.Slide },
  router,
  data: {
        showModal: true,
  }
})
