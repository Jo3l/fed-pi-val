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
import routes from './routes'
import css from './assets/less/app.less'

Vue.use(KeenUI)
Vue.use(VueProgressiveImage)

Vue.prototype.$http = axios;

import Toolbar from './components/Toolbar.vue' //este se carga aqui ya que esta fuera de las rutas

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
	mounted: function () {

	}
})
