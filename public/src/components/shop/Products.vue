<template>
    <transition name="fade">
    	
		<div class="shop" v-if=" type=='slider' ">
			<h1><ui-icon>shopping_cart</ui-icon> {{ $t('cart.online_shop') }}</h1>
			
		  <swiper :options="swiperOption" class="products">
	
		  	<swiper-slide v-for="product,i in products">
			    <div class="item" >
			    	<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop')+'/'+product.json.content[$i18n.locale].slug }">
				    	<div class="itemContainer">
				    		<em v-if="product.destacada">{{$i18n.t('cart.highlight')}}</em>
					    	<div class="img" :style="'background:url('+product.json.images[0].img+') no-repeat center / cover;'"></div>
					    	<div class="productText">
							    <div class="name">{{ product.json.content[$i18n.locale].name }}</div>
						    	{{$i18n.t('cart.price')}}: <div class="price">{{ product.json.types[0].price.amount }} €</div>
						    	<div class="oldPrice" v-if="product.json.types[0].price.amount<product.json.types[0].price.oldPrice">{{$i18n.t('cart.before')}}: <span>{{ product.json.types[0].price.oldPrice }} €</span></div>
					    	</div>
				    	</div>
			    	</router-link>
			    </div>
		  	</swiper-slide>
		  	
	  	    <div class="swiper-pagination curt" slot="pagination"></div>
	  		<ui-icon-button icon="chevron_left" type="primary" class="swiper-button-prev curt" slot="button-prev"></ui-icon-button>
			<ui-icon-button icon="chevron_right" type="primary" class="swiper-button-next curt" slot="button-next"></ui-icon-button>

		    
		  </swiper>
		</div>
		
		<div class="shop full" v-else="v-else">
		  <div class="products">
		    <div v-for="product in products" class="item" >
			    	<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop')+'/'+product.json.content[$i18n.locale].slug }">
				    	<div class="itemContainer">
				    		<em v-if="product.destacada">{{$i18n.t('cart.highlight')}}</em>
					    	<div class="img" :style="'background:url('+product.json.images[0].img+') no-repeat center / cover;'"></div>
					    	<div class="productText">
							    <div class="name">{{ product.json.content[$i18n.locale].name }}</div>
						    	{{$i18n.t('cart.price')}}: <div class="price">{{ product.json.types[0].price.amount }} €</div>
						    	<div class="oldPrice" v-if="product.json.types[0].price.amount<product.json.types[0].price.oldPrice">{{$i18n.t('cart.before')}}: <span>{{ product.json.types[0].price.oldPrice }} €</span></div>
					    	</div>
				    	</div>
			    	</router-link>
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
  name: 'ProductSlider',
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
	    products: []
    }
  },

  computed: {
    ...mapGetters({
    	counto: 'countCart',
    	carto: 'cart',
    	checkoutStatus: 'checkoutStatus',
    })
  },
  methods: {
  	
  	getData: function(apiUrl) {
        var vm = this;
        this.$http.get(apiUrl, {
		    // use before callback
		    before(request) {
		      // abort previous request, if exists
		      if (this.previousRequest) {
		        this.previousRequest.abort();
		      }
		      // set previous request on Vue instance
		      this.previousRequest = request;
		    }
		}).then(function (response) {
			
            vm.products = response.data;
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
    }
  },
  mounted: function() {
		if(this.type == 'slider') {
			this.getData('/productes/destacada');
		} else {
			this.getData('/productes');
		}
  }
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";
	
.shop {
	.products {
		.item {
		  text-align: left;
		  //padding:10px 0;
		
			.itemContainer {
				margin:10px;
				padding:5px 0;
				border-radius: .25rem;
	    		border: 1px solid rgba(0,0,0,.125);
		    	.img {
				  	padding-bottom:85%;
				  	border-radius: .25rem;
				  	margin: 1vw 1vw 0 1vw;
				}
				em {
				    background-color: red;
				    color: white;
				    padding: 4px 12px;
				    font-size: 0.7em;
				    position: absolute;
				    margin: 7px;
				    border-radius: 3px 41px 0 36px;
				}
				.productText{
					margin-left:15px;
					.name{
						font-weight:bolder;
						text-transform:capitalize;
						font-size:1.2em;
						margin-top:5px;
					}
					
					.price {
						display:inline;
						font-size:1.4em;
						font-weight:bolder;
						color:@fedcolor;
					}
					.oldPrice {
						display:inline;
						font-size:1em;
						margin-left:10px;
						span{
							text-decoration:line-through;
						}
					}	
				}

			}	

		}

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