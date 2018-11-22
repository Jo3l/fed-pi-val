import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import cart from './modules/cart'
import validator from './modules/validator'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
	auth,
	cart, 
	validator
  }
})