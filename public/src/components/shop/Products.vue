<template>
    <transition name="fade">
    	
		<div class="shop" v-if=" type=='slider' ">
			<h1><ui-icon>shopping_cart</ui-icon> {{ $t('cart.highlightProducts') }}</h1>
			
		  <swiper :options="swiperOption" class="products">
	
		  	<swiper-slide v-for="product,i in products">
			    <div class="item" >
			    	
		    	<item-container :product="product">
		    		<template slot="categoria">
		    			<p>{{product.json.content[$i18n.locale].category}}</p>
		    		</template>
		    	</item-container>
			    	
			    </div>
		  	</swiper-slide>
		  	
	  	    <div class="swiper-pagination curt" slot="pagination"></div>
	  		<ui-icon-button icon="chevron_left" type="primary" class="swiper-button-prev curt" slot="button-prev"></ui-icon-button>
			<ui-icon-button icon="chevron_right" type="primary" class="swiper-button-next curt" slot="button-next"></ui-icon-button>

		    
		  </swiper>
		</div>
		
		<div class="shop full" v-else="v-else">
			<h3 v-if="categoria" @click="setCategoria('all')">{{$i18n.t('cart.viewAllProducts')}}</h3>
			<div class="products">
				<div v-for="product in products" class="item">
					
					<item-container :product="product">
						<template slot="categoria">
							
							<p @click="setCategoria(product.json.content[$i18n.locale].category)">{{product.json.content[$i18n.locale].category}}</p>
							
						</template>
					</item-container>
					
				</div>
			</div>
		  	<h3 v-if="categoria" @click="setCategoria('all')">{{$i18n.t('cart.viewAllProducts')}}</h3>
		</div>

    </transition>
</template>

<script>

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'
import { mapGetters, mapActions } from 'vuex'

import itemContainer from './ItemContainer.vue';

export default {
  name: 'ProductSlider',
  components: { swiper, swiperSlide, itemContainer },
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
    	categoria:'',
    	allProducts:[],
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
  	setCategoria:function(categoria){
  		var vm=this;
  		
  		if(categoria=='all') {
  			vm.products = vm.allProducts;
  			vm.categoria = '';
  			
  		} else {
	  		vm.categoria = categoria;
	  		vm.products = vm.products.json.content[$i18n.locale].filter(function (el) {
			  return el.category == categoria
			});
  		}
  		

  	},
  	getData: function(apiUrl) {
        var vm = this;
        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
        this.$http.get(apiUrl, { cache: auth })
        .then(function (response) {
			
            vm.products = response.data;
            vm.allProducts = response.data;
            
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

<style lang="less" scoped>

@import "../../assets/less/defines.less";
	
.shop {
	h3{
		margin:10px;
		cursor:pointer;
		color:@fedcolor;
	}
	.products {
		.item {
		  width:initial;
		  text-align: left;
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