<template>
    <transition name="fade">
    	
		<div class="shop" v-if=" type=='slider' ">
			<h1><ui-icon>shopping_cart</ui-icon> {{ $t('cart.online_shop') }}</h1>
			
			<div class="cart">
			  <h2>{{count}} / {{total}} €</h2>
			  <div class="cart_item" v-for="item,i in cart" v-if="item.qty > 0"><b>{{ item.qty }}</b> &times; {{ item.name }} {{ item.price }}
			    <button @click="item.qty += 1">+1</button>
			    <button v-if="item.qty > 1" @click="(item.qty > 1) ? item.qty -= 1 : ''">-1</button>
			    <button @click="del(item.id)">X</button>
			  </div>
			</div>
			
			
		  <swiper :options="swiperOption" class="products">
	
		  	<swiper-slide v-for="product,i in products">
			    <div class="item" >
			    	<div class="itemContainer">
				    	<img :src="'//zupra.github.io/t-shirt_shop/img/test-2/'+product.id+'.png'"/>
				    	<div class="name">{{ product.name }}</div>
				    	<div class="price">{{ product.price }} €</div>
				    	
				    	<ui-button :icon="!product.qty ? 'add' : 'done'" size="small" :disabled="product.qty>0" @click="addToCart(product)" v-if="!product.qty">
				        	<template v-if="!product.qty">{{ $t('cart.addToBasket') }}</template>
				        	<template v-else="v-else">{{product.qty}} {{ $t('cart.added') }}</template>
				    	</ui-button>
				    	
				    	<ui-button icon="add" size="small" @click="product.qty += 1" v-if="product.qty>0">
							1
				    	</ui-button>
				    	
				    	<ui-button size="small"  :icon="product.qty==1?'remove_shopping_cart':'remove'" v-if="product.qty > 0" @click="(product.qty > 1) ? product.qty -= 1 : del(product.id)">
				    		<template v-if="product.qty == 1"></template>
				    		<template v-else>1</template>
				    	</ui-button>

			    	</div>
			    </div>
		  	</swiper-slide>
		  	
	  	    <div class="swiper-pagination curt" slot="pagination"></div>
	  		<ui-icon-button icon="chevron_left" type="primary" class="swiper-button-prev curt" slot="button-prev"></ui-icon-button>
			<ui-icon-button icon="chevron_right" type="primary" class="swiper-button-next curt" slot="button-next"></ui-icon-button>

		    
		  </swiper>
		</div>
		
		<div class="shop full" v-else="v-else">
			<pre>{{carto}}</pre>
			<pre>{{cart}}</pre>
			<h1><ui-icon>shopping_cart</ui-icon> {{ $t('cart.online_shop') }}</h1>
			
			<div class="cart">
			  <h2>{{count}} / {{total}} €</h2>
			  <div class="cart_item" v-for="item,i in cart" v-if="item.qty > 0"><b>{{ item.qty }}</b> &times; {{ item.name }} {{ item.price }}
			    <button @click="item.qty += 1">+1</button>
			    <button v-if="item.qty > 1" @click="(item.qty > 1) ? item.qty -= 1 : ''">-1</button>
			    <button @click="del(i, item.id)">X</button>
			  </div>
			</div>
		  <div class="products">
		    <div v-for="product in products" class="item" >
		    	<div class="itemContainer">
			    	<img :src="'//zupra.github.io/t-shirt_shop/img/test-2/'+product.id+'.png'"/>
			    	<div class="name">{{ product.name }}</div>
			    	<div class="price">{{ product.price }} €</div>
			    	<ui-button :icon="!product.qty ? 'add' : 'done'" size="small" :disabled="product.qty>0" @click="addToCart(product)" v-if="!product.qty">
			        	<template v-if="!product.qty">{{ $t('cart.addToBasket') }}</template>
			        	<template v-else="v-else">{{product.qty}} {{ $t('cart.added') }}</template>
			    	</ui-button>
			    	<ui-button icon="add" size="small" @click="product.qty += 1" v-if="product.qty>0">
						1
			    	</ui-button>
			    	<ui-button size="small"  :icon="product.qty==1?'remove_shopping_cart':'remove'" v-if="product.qty > 0" @click="(product.qty > 1) ? product.qty -= 1 : del(product.id)">
			    		<template v-if="product.qty == 1"></template>
			    		<template v-else>1</template>
			    	</ui-button>
		    	</div>
		    </div>
		  </div>
		</div>

    </transition>
</template>

<script>

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Cart',
  components: { swiper, swiperSlide },
  props: {
        type: {
            type: String,
            default: 'fullPage'
        }
  },
  head : function() {
	return this.$route.meta;
  },
  data () {
    return {
	    swiperOption: {
	        slidesPerView: 4,
	        slidesPerColumn: 1,
	        spaceBetween: 0,
	        navigation: {
	          nextEl: '.swiper-button-next.curt',
	          prevEl: '.swiper-button-prev.curt'
	        },
	        pagination: {
	          el: '.swiper-pagination.curt',
	          type: 'bullets',
	          clickable: true
	        },
	        breakpoints: {
	          768: {
	            slidesPerView: 3,
	            spaceBetween: 10
	          },
	          480: {
	            slidesPerView: 2,
	            spaceBetween: 10
	          },
	          320: {
	            slidesPerView: 1,
	            spaceBetween: 10
	          }
	        }
	    },
	    products: [{
	      id: 1,
	      name: "black",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 2,
	      name: "dark-blue",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 3,
	      name: "green",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 4,
	      name: "grey",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 5,
	      name: "light-blue",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 6,
	      name: "pink",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 7,
	      name: "purple",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 8,
	      name: "red",
	      category: "pilotes",
	      price: 99,
	      inventory:6
	    }, {
	      id: 9,
	      name: "white",
	      category: "pilotes",
	      price: 99,
	      inventory:6
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
    },
    ...mapGetters({
    	counto: 'countCart',
    	carto: 'cart',
    	checkoutStatus: 'checkoutStatus',
    })
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
    del: function del(id) {
    	
	    for (var i = 0; i < this.cart.length; i++) {
	        if (this.cart[i].id === id) {
	          this.cart.splice(i, 1);
	          break;
	        }
	    }
    	this.unblock(id);
    }
  },
  mounted: function() {

  }
}
</script>

<style lang="less">

@import "../assets/less/defines.less";
	

.shop {
	.products {
		.item {
		  text-align: center;
		  //padding:10px 0;
		
			.itemContainer {
				margin:10px;
				padding:10px 0;
				border-radius: .25rem;
	    		border: 1px solid rgba(0,0,0,.125);
		    	img {
				  	width:60%;
				}
				.name{
					font-weight:bolder;
					text-transform:capitalize;
				}
				
				.price {
					display:block;
					font-size:1.2em;
				}
			}	

		}

	}
	
	.cart {
	  margin: 1em 0 0 3em;
	  width: 270px;
	}
	&.full {
		.products {
			  display: flex;
			  flex-flow: row wrap;
			  justify-content: space-between;
			  &:after {
			  content: "";
			  flex: auto;
			  }
			.item {
				width: 25%;
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