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

	console.log(product.stock);
	
    if (product.stock > 0) {
    	
      const cartItem = state.added.find(function(item) {return item.id === product.id && item.typeId === product.typeId})
      if (!cartItem) {
        commit('pushProductToCart', { id: product.id, typeId: product.typeId });
        saveState(state);
      } else {
        commit('incrementItemQuantity', cartItem);
        saveState(state);
      }
      
      
      
    }
    
    console.log(state);
  }
}

// mutations
const mutations = {
  pushProductToCart (state, { id, typeId }) {
    state.added.push({
      id,
      typeId,
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