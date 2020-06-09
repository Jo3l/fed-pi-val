import Vue from 'vue'
import axios from '../../axios'
import VueAuthenticate from 'vue-authenticate'
import jwt_decode from 'jwt-decode'

Vue.use(VueAuthenticate)

Vue.prototype.$http = axios;

var vueAuth = VueAuthenticate.factory(Vue.prototype.$http, {
  //baseUrl: '',
  tokenName: 'access_token',
})

const state = {
  isAuthenticated: vueAuth.isAuthenticated(),
  error:{}
}

const getters = {
    isAuthenticated: function (state) {
      return state.isAuthenticated;
    },
    isAuthenticatedWithRole: function (state) {
    	return function (n) {
			return state.isAuthenticated && jwt_decode(vueAuth.getToken() ).data.rol <= n;
    	}
    },
    isAuthenticatedWithExactRole: function (state) {
    	return function (n) {
			return state.isAuthenticated && jwt_decode(vueAuth.getToken() ).data.rol == n;
    	}
    },
    role: function(state) {
    	if(state.isAuthenticated) return jwt_decode(vueAuth.getToken() ).data.rol;
    },
    userMail: function(state) {
    	if(state.isAuthenticated)  return jwt_decode(vueAuth.getToken() ).data.email;
    },
    userId: function(state) {
    	if(state.isAuthenticated)  return jwt_decode(vueAuth.getToken() ).data.id;
    }, 
    error: function(state){
    	return state.error;
    }
}


const mutations = {
    isAuthenticated: function (state, payload) {
      state.isAuthenticated = payload.isAuthenticated;
    }, 
    error: function (state, payload) {
      state.error = payload.error;
    }, 
}

const actions = {
    login: function (context, payload) {
      vueAuth.login(payload.user, payload.requestOptions).then(
      	
      	function (response) {
	        return context.commit('isAuthenticated', {
	          isAuthenticated: vueAuth.isAuthenticated()
	        });
	
    	}
      )
      .catch(function (error) {
      	
	        return context.commit('error', {
	          error: error
	        });
	  });

    },
    vuexLogin:function(context,payload){
        return context.commit('isAuthenticated', {
          isAuthenticated: vueAuth.isAuthenticated()
        });
    },
    removeError: function(context, payload) {
    	return context.commit('error',{error:null});	
    },
    logout: function (context, payload) {
      vueAuth.logout().then(
      	function () {return context.commit('isAuthenticated', {isAuthenticated: vueAuth.isAuthenticated()});}
      );
      window.location.href='/';
    }
}

export default {
  state,
  getters,
  mutations,
  actions
}
