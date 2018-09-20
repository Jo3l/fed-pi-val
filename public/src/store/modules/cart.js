import axios from '../../axios'

const state = loadState();

function loadState() {
  try {
    var serializedState = localStorage.getItem('fedpival_cart');
    if (serializedState === null) {
      return { added: [], checkoutStatus: null }
    }
    return JSON.parse(serializedState);
  } catch (err) {
    return undefined;
  }
};

function saveState(state) {
  try {
    var serializedState = JSON.stringify(state);
    localStorage.setItem('fedpival_cart', serializedState);
  } catch (err) {
    console.error('Error: ' + err);
  }
};



// getters
const getters = {
  checkoutStatus: function(state){return state.checkoutStatus},
  countCart: function(state) {
  		var number=0;
		state.added.forEach(function(element) {
		  number+=element.quantity;
		});
		return number;
  },
  cart: function(state) {
  	return state.added;
  },
  cartTotalPrice: function(state) {
  		var total=0;
		state.added.forEach(function(element) {
			var item = element.fullProduct.types.find(function(item) {return item.id === element.id && item.typeId === element.typeId})
			total+= (item.price.amount * element.quantity)
		});
		return total.toFixed(2);
  }
}

// actions
const actions = {
	
  addProductToCart ({ state, commit }, product) {
	  commit('setCheckoutStatus', null)
      const cartItem = state.added.find(function(item) {return item.id === product.id && item.typeId === product.typeId})
      if (!cartItem) {
        commit('pushProductToCart', { id: product.id, typeId: product.typeId, fullProduct: product.fullProduct });
        saveState(state);
      } else {
        commit('incrementItemQuantity', cartItem);
        saveState(state);
      }

  },
  increaseProductToCart ({ state, commit }, product) {
      const cartItem = state.added.find(function(item) {return item.id === product.id && item.typeId === product.typeId})
      commit('incrementItemQuantity', cartItem);
      saveState(state);

  },
  removeProductToCart ({ state, commit }, product) {
      const cartItem = state.added.find(function(item) {return item.id === product.id && item.typeId === product.typeId})
      if (cartItem.quantity>1) {
        commit('decrementItemQuantity', cartItem);
        saveState(state);
      } else {
        commit('removeItem', cartItem);
        saveState(state);
      }
  },
  deleteProductToCart ({ state, commit }, product) {
      const cartItem = state.added.find(function(item) {return item.id === product.id && item.typeId === product.typeId})
        commit('removeItem', cartItem);
        saveState(state);
  },
  setCheckStatus({ state, commit }, status) {
  		commit('setCheckoutStatus', status);
  }
}

// mutations
const mutations = {
  pushProductToCart (state, { id, typeId, fullProduct }) {
    state.added.push({
      id,
      typeId,
      fullProduct,
      quantity: 1
    })
  },

  incrementItemQuantity (state, { id, typeId }) {
    const cartItem = state.added.find(item => item.id === id && item.typeId === typeId)
    cartItem.quantity++
  },
  
  decrementItemQuantity (state, { id, typeId }) {
    const cartItem = state.added.find(item => item.id === id && item.typeId === typeId)
    cartItem.quantity--
  },
  
  removeItem (state, { id, typeId }) {
    const cartItem = state.added.findIndex(item => item.id === id && item.typeId === typeId)
    state.added.splice(cartItem, 1)
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