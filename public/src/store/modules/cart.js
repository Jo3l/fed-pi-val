
const state = {
  added: [],
  checkoutStatus: null
}

// getters
const getters = {
  checkoutStatus: function(state){return state.checkoutStatus},
  countCart: function(state) {
		return state.added.length;
  },
  cart: function(state) {
  	return state.added;
  },
  cartTotalPrice: function(state, getters) {
    return function() {
      //return total + product.price * product.quantity
    };
  }
}

// actions
const actions = {
	
  addProductToCart ({ state, commit }, product) {
    commit('setCheckoutStatus', null)
    if (product.inventory > 0) {
      const cartItem = state.added.find(function(item) {return item.id === product.id})
      if (!cartItem) {
        commit('pushProductToCart', { id: product.id })
      } else {
        commit('incrementItemQuantity', cartItem)
      }
      // remove 1 item from stock
      commit('decrementProductInventory', { id: product.id })
    }
  }
}

// mutations
const mutations = {
  pushProductToCart (state, { id }) {
    state.added.push({
      id,
      quantity: 1
    })
  },

  incrementItemQuantity (state, { id }) {
    const cartItem = state.added.find(item => item.id === id)
    cartItem.quantity++
  },

  setCartItems (state, { items }) {
    state.added = items
  },

  setCheckoutStatus (state, status) {
    state.checkoutStatus = status
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}