<template>
    <transition name="fade">
					<div class="shopping-cart">
						
					    <div class="shopping-cart-header">
					      <ui-icon icon="shopping_cart"></ui-icon><span class="badge">{{countCart}}</span>
					      <div class="shopping-cart-total">
					        <span class="lighter-text">Total:</span>
					        <span class="main-color-text">{{cartTotalPrice}}€</span>
					      </div>
					    </div> <!--end shopping-cart-header -->
				    	<swiper class="shopping-cart-items" :options="swiperOptionThumbs">
				    		<div v-if="cart.length==0" class="swiper-slide empty">{{$t('cart.emptyCart')}}</div>
						    <swiper-slide v-for="item in cart" v-if="item.fullProduct">
						      	<router-link :to="{ path: '/'+$i18n.locale+'/'+$t('cart.shop')+'/'+item.fullProduct.content[$i18n.locale].slug }">
						        <picture :style="'background-image:url('+ getProductImage(item) +');'"><span>{{getProductSize(item)}}</span></picture>
						        <span class="item-name">{{item.fullProduct.content[$i18n.locale].name}}</span>
						        <span class="item-price">{{getProductPrice(item)}}€</span>
						        <span class="item-quantity">{{$t('cart.quantity')}}: {{item.quantity}}</span>
						        </router-link>
						        <div class="modifier">
						        	<ui-icon-button color="fedpival" icon="add" size="small" type="secondary" @click="$store.dispatch('increaseProductToCart', item)"></ui-icon-button>
						        	<ui-icon-button color="fedpival" icon="remove" size="small" type="secondary" @click="$store.dispatch('removeProductToCart', item)"></ui-icon-button>
						        	<ui-icon-button color="fedpival" icon="clear" size="small" type="secondary" @click="$store.dispatch('deleteProductToCart', item)"></ui-icon-button>
						        </div>
						    </swiper-slide>
					    	<ui-icon-button icon="expand_less" type="primary" class="swiper-button-prev cart" slot="button-prev" v-if="cart.length>0"></ui-icon-button>
							<ui-icon-button icon="expand_more" type="primary" class="swiper-button-next cart" slot="button-next" v-if="cart.length>0"></ui-icon-button>
						</swiper>

						<ui-button icon="shopping_cart" :class="cart.length>0?'checkout':'checkout disabled'" color="fedpival" :disabled="cart.length<=0" @click="openModal('buyModal')">Comprar</ui-button>
					    <!--<button :class="cart.length>0?'checkout':'checkout disabled'" :disabled="cart.length>0"><span>Comprar</span></button>-->
					    
				        <ui-modal size="large" ref="buyModal" title="Datos del cliente">
							<h3>Información:</h3>
							<p>Rellene este formulario y recibirá por correo electrónico las instrucciones para el pago del contenido del carrito via transferencia. Para cualquier otra pregunta puede dirigirse a nosotros al teléfono 6969696969 o al correo electronico <a href="mailto:tenda@fedpival.es">tenda@fedpival.es</a></p>
							<div class="clientData">
								<div class="form">
						            <ui-textbox
						                floating-label
						                label="Nom"
						                placeholder="Pose el seu nom"
						                v-model="order.name"
						            ></ui-textbox>
		
						            <ui-textbox
						                floating-label
						                label="Adreça"
						                placeholder="Pose l'adreça on es va a enviar"
						                v-model="order.address"
						                multiLine
						            ></ui-textbox>
		
						            <ui-textbox
						                floating-label
						                label="Codi postal"
						                placeholder="Pose el seu codi postal"
						                type="number"
						                v-model="order.cp"
						            ></ui-textbox>
		
						            <ui-textbox
						            	floating-label
						                icon-position="right"
						                icon="phone"
						                label="Telèfon"
						                type="number"
						                placeholder="Pose el seu nº de telèfon"
						                v-model="order.tel"
						            ></ui-textbox>
						            
						            <ui-textbox
						            	floating-label
						                help="Rebrà per correu electrònic les instruccions per a fer el pagament, pel que cal assegurar-se que siga correcta"
						                icon-position="right"
						                icon="mail"
						                label="Email"
						                placeholder="Pose la seua adreça de correu electrònic"
						                type="email"
						                v-model="order.email"
						            ></ui-textbox>
								</div>
								<div class="list">
									
							    	<div class="shopping-cart-items final">
									    <div v-for="item in cart" class="swiper-slide">
									        <span class="item-name">{{item.fullProduct.content[$i18n.locale].name}}</span>
									        <span class="item-price">{{getProductPrice(item)}}€</span>
									        <span class="item-quantity">{{$t('cart.quantity')}}: {{item.quantity}}</span>
									    </div>

									</div>
									<span class="finalPrice">Total {{cartTotalPrice}}€</span>
								</div>
				            </div>
							<div slot="footer">
				                <ui-button @click="" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
				                <ui-button @click="closeModal('buyModal')">{{$i18n.t('modal.cancel')}}</ui-button>
				            </div>
				        </ui-modal>
					    
					 </div>
    </transition>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
  components: { swiper, swiperSlide },
  data () {
    return {
    	order:{
    		name:'',
    		address:'',
    		cp:'',
    		tel:'',
    		email:''
    	},
        swiperOptionThumbs: {
          direction: 'vertical',
          spaceBetween: 10,
          slidesPerView: 3,
          touchRatio: 0.2,
          navigation: {
            nextEl: '.swiper-button-next.cart',
            prevEl: '.swiper-button-prev.cart'
          }
        },
    }
  },

  computed: {
    ...mapGetters({
    	countCart: 'countCart',
    	cart: 'cart',
    	checkoutStatus: 'checkoutStatus',
    	cartTotalPrice :'cartTotalPrice',
    })
  },
  methods: {
    openModal: function(ref) {
    	var vm = this;
		vm.order.cart = vm.cart;
        vm.$refs[ref].open();
    },
    closeModal: function(ref) {
    	var vm = this;
    	vm.order={name:'', address:'', cp:'', tel:'', email:''};
        vm.$refs[ref].close();
    },
    buy: function(cart){

		var vm = this;
		vm.order.cart = vm.cart;

		vm.$http.post('/comprar/', vm.order)
		.then(function (response) {
				alert(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });
	        

    },
  	getType:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].typeId==item.typeId) {
		  	var type = item.fullProduct.types[i];
		  	type.fullProduct = JSON.parse( JSON.stringify( item ) );
		  	return type;
		  }
		}
		return false;
  	},
	getProductImage:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].typeId==item.typeId) {
		  	for (var j = 0, len = item.fullProduct.images.length; j < len; j++) {
		  		if(item.fullProduct.images[j].tag == item.fullProduct.types[i].imgTag){
		  			return item.fullProduct.images[j].img;
		  		}
		  	}
		  }
		}
		return false;
	},
	getProductPrice:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].typeId==item.typeId) {
		  	
		  	return item.fullProduct.types[i].price.amount;
		  	
		  }
		}
		return false;
	},
	getProductSize:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].typeId==item.typeId) {
		  	
		  	return item.fullProduct.types[i].size;
		  	
		  }
		}
		return false;
	}
  },
  watch: {

  },
  mounted: function() {


  }
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.clientData{
	display:flex;
	.form{width:50%;}
	.list{width:45%;margin-left:5%;border-left: 1px solid #e0e0e0;}
}
.finalPrice{
	text-align: right;
    width: 100%;
    display: block;
    padding: 30px;
}
.shopping-cart {
    margin: 20px;
    position: absolute;
    background: white;
    width: 320px;
    border-radius: 3px;
    padding: 20px 0 0 0;
    z-index: 10;
    box-shadow: 0 10px 45px rgba(0, 0, 0, 0.2);
    right: 22px;
    top: 66px;
    max-height: 400px;

	.checkout{
	    width: 100%;
	    border-radius: 0px 0px 3px 3px;
		span{
			display: block;
		    padding: 15px;
		    font-size: 125%;
		    text-transform: uppercase;
		}
	}
    .swiper-button-next.cart{
		bottom: 10px;
    	top: auto;
    	right: 0px!important;
    	left:initial!important;
    }
    .swiper-button-prev.cart{
    	top:30px;
    	bottom:auto;
    	right: 0px;
    	left:initial!important;
    }
    
	.shopping-cart-header {
		border-bottom: 1px solid #E8E8E8;
		padding: 0 20px 15px 20px;
		.shopping-cart-total {
		  float: right;
		}
	}
  
	.shopping-cart-items {
	    padding-top: 20px;
	    padding-left: 0;
		list-style: none;
	    //overflow: auto;
	    max-height: 280px;
	    //min-height: 275px;
	    position: relative;
		border-bottom: 1px solid #E8E8E8;
		&.final{
			border-bottom:none;
			overflow:auto;
			max-height: 360px;
		}
		

	    
	    .swiper-slide {
	    	padding:0 30px 0 20px;
	    	min-height: 74px;
	    	&.empty{
	    		 min-height:12px;
	    	}
	    	&:hover{
	    		picture{
	    			box-shadow: 0px 3px 0px #6d111d;
	    		}
	    	}
	    	picture{
				width: 70px;
			    height: 70px;
			    background-size: cover;
			    background-position: center;
			    display: inline-block;
			    float: left;
			    margin-right: 10px;
			    border-radius: 5px;
			    border: 1px dashed #6d111d;
			    box-sizing: content-box;
			    position:relative;
			    span{
			    	color: white;
				    text-shadow: 1px 1px 1px black;
				    font-weight: bolder;
				    position:absolute;
				    bottom:0;
				    right:3px;
			    }
	    	}
	    }
	    img {
	      float: left;
	      margin-right: 12px;
	    }
	    .item-name {
			padding-top: 3px;
		    font-size: 16px;
		    white-space: nowrap;
		    overflow: hidden;
		    display: block;
		    text-overflow: ellipsis;
	    }
	    .item-price {
	      color: @fedcolor;
	      margin-right: 8px;
	    }
	    .item-quantity {
	      color: @fedcolor;
	      text-transform:capitalize;
	    }
	}

    &:after {
		bottom: 100%;
		left: 89%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
		border-bottom-color: white;
		border-width: 8px;
		margin-left: -8px;
	}
	.badge {
	    background-color: @fedcolor;
	    border-radius: 10px;
	    color: white;
	    display: inline-block;
	    font-size: 12px;
	    line-height: 1;
	    padding: 4px 6px 2px 6px;
	    text-align: center;
	    vertical-align: middle;
	    white-space: nowrap;
	}
}

</style>