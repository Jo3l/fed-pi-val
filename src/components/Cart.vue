<template>
    <transition name="fade">
    	
		<div class="shop" v-if=" type=='slider' ">
			<div class="cart">
			  <h1>{{ $t('cart.basket') }}</h1>
			  <h2>{{count}} / {{total}} €</h2>
			  <div class="cart_item" v-for="item,i in cart" v-if="item.qty &gt; 0"><b>{{ item.qty }}</b> &times; {{ item.name }} {{ item.price }}
			    <button @click="item.qty += 1">+1</button>
			    <button v-if="item.qty &gt; 1" @click="(item.qty &gt; 1) ? item.qty -= 1 : ''">-1</button>
			    <button @click="del(i, item.id)">X</button>
			  </div>
			</div>
		  <carousel :perPageCustom="[[0, 1],[480, 2],[768, 3],[992, 4], [1200, 5]]" :minSwipeDistance=30 class="products">
		  	<slide v-for="product in products">
			    <div class="item" ><img :src="'//zupra.github.io/t-shirt_shop/img/test-2/'+product.id+'.png'"/>
			      <div class="name">{{ product.name }}</div>
			      <div class="price">{{ product.price }} €</div>
			      <button class="btn" :disabled="product.qty" @click="addToCart(product)"> 
			        <template v-if="!product.qty">{{ $t('cart.addToBasket') }}</template>
			        <template v-else="v-else">✔ <b>{{product.qty}}</b> {{ $t('cart.added') }}</template>
			      </button>
			    </div>
		  	</slide>
		  </carousel>
		</div>
		
		<div class="shop full" v-else="v-else">
			<div class="cart">
			  <h1>{{ $t('cart.basket') }}</h1>
			  <h2>{{count}} / {{total}} €</h2>
			  <div class="cart_item" v-for="item,i in cart" v-if="item.qty &gt; 0"><b>{{ item.qty }}</b> &times; {{ item.name }} {{ item.price }}
			    <button @click="item.qty += 1">+1</button>
			    <button v-if="item.qty &gt; 1" @click="(item.qty &gt; 1) ? item.qty -= 1 : ''">-1</button>
			    <button @click="del(i, item.id)">X</button>
			  </div>
			</div>
		  <div class="products">
		    <div v-for="product in products" class="item" ><img :src="'//zupra.github.io/t-shirt_shop/img/test-2/'+product.id+'.png'"/>
		      <div class="name">{{ product.name }}</div>
		      <div class="price">{{ product.price }} €</div>
		      <button class="btn" :disabled="product.qty" @click="addToCart(product)"> 
		        <template v-if="!product.qty">{{ $t('cart.addToBasket') }}</template>
		        <template v-else="v-else">✔ <b>{{product.qty}}</b> {{ $t('cart.added') }}</template>
		      </button>
		    </div>
		  </div>
		</div>
		
    </transition>
</template>

<script>

export default {
  name: 'Cart',
  components: {},
  props: {
        type: {
            type: String,
            default: 'fullPage'
        }
  },
  data () {
    return {
	    products: [{
	      id: 1,
	      name: "black",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 2,
	      name: "dark-blue",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 3,
	      name: "green",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 4,
	      name: "grey",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 5,
	      name: "light-blue",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 6,
	      name: "pink",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 7,
	      name: "purple",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 8,
	      name: "red",
	      category: "pilotes",
	      price: 99
	    }, {
	      id: 9,
	      name: "white",
	      category: "pilotes",
	      price: 99
	    }],
	    cart: []
    }
  },
  computed: {
    count: function count() {
      return this.cart.reduce(function (n, cart) {
        return cart.qty + n;
      }, 0);
    },
    total: function total() {
      return this.cart.reduce(function (n, cart) {
        return cart.price * cart.qty + n;
      }, 0).toFixed(2);
    }
  },
  methods: {
    addToCart: function addToCart(product) {
      this.$set(product, 'qty', +1);
      this.cart.push(product);
    },
    unblock: function unblock(id) {
      for (var i = 0; i < this.products.length; i++) {
        if (this.products[i].id === id) {
          delete this.products[i].qty;
          break;
        }
      }
    },
    del: function del(index, id) {
      this.cart.splice(index, 1);
      this.unblock(id);
    }
  }
}
</script>

<style lang="less">

@import "../assets/less/defines.less";
	

.shop {
	.products {
		.item {
		  text-align: center;
		  padding:10px 0;
		  img {
		  	width:60%;
		  }
		}
		.name {
		  margin-bottom: 0.5em;
		}
		.price {
		  display: inline-block;
		  vertical-align: middle;
		}
	}
	
	.cart {
	  margin: 1em 0 0;
	  width: 270px;
	}
	&.full {
		padding:20px;
		@media(max-width:@screenMobile) {
			padding:0;
		}
		.products {
			  display: flex;
			  flex-flow: row wrap;
			  justify-content: space-between;
			  padding: 0 20px;
			  &:after {
			  content: "";
			  flex: auto;
			  }
			.item {
				width: 25%;
				padding: 20px 0;

				@media(max-width:@screenTablet) {
					width: 33%;
				}

				@media(max-width:@screenMobile) {
					width: 50%;
				}
			
				img {
					width:75%;
				}
			}
		}
	}
}


	
	
</style>