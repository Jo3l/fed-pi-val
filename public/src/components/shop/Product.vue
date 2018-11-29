<template>
    <transition name="fade">
		<div class="shop full" v-if="loaded">
			<br>
			<div class="product">
				<aside :class="product.images.length>1?'images':'images single'" v-if="product.images.length>0">	
				    <swiper :options="swiperOptionThumbs" class="gallery-thumbs" ref="swiperThumbs" v-if="product.images.length>1">
				        <swiper-slide v-for="image in product.images" :style="'background-image:url('+ image.thumb +');'"></swiper-slide>
				    </swiper>
					<swiper :options="swiperOptionTop" class="gallery-top" ref="swiperTop">
						<swiper-slide class="swipe" v-for="image in product.images" :style="'background-image:url('+ image.img +');'"></swiper-slide>
					  	<ui-icon-button icon="chevron_left" type="primary" class="swiper-button-prev prod" slot="button-prev"></ui-icon-button>
						<ui-icon-button icon="chevron_right" type="primary" class="swiper-button-next prod" slot="button-next"></ui-icon-button>
				    </swiper>
				    
				    <div class="addProductAnimation" :style="cartCss"></div>
				    
				</aside>
				
				<article class="content">
					<h1>{{product.content[$i18n.locale].name}}</h1>
					<div class="price-shipping">
							{{$i18n.t('cart.price')}}: <div class="price">{{ finalPrice }} €</div>
					    	<div class="oldPrice" v-if="oldPrice">{{$i18n.t('cart.before')}}: <span>{{ oldPrice }} €</span></div>
		             </div>
		            <br> 
                  	<div class="shortDescription" v-if="product.content[$i18n.locale].shortDescription">
                    	{{product.content[$i18n.locale].shortDescription}}
                    </div>
		             
			        <div class="swatches" v-bind:style="{ 'visibility': product.types.length>1?'':'hidden' }">
	                    <div class="swatch">
	                      <div class="header">{{$i18n.t('cart.options')}}</div>
	                      <div class="swatch-element plain" v-for="(variation,index) in getVariations(product)">
	                        <input type="radio" :id="'swatch-'+index+'-variation'" name="variation" :value="variation" @click="function(){typeSelected=variation;productSelected = selectProduct( product, typeSelected);}"/>
	                        <label :for="'swatch-'+index+'-variation'">
	                        {{variation}}
	                        <img class="crossed-out" src="/static/img/shop/soldout.png" />
	                        </label>
	                      </div>
	                    </div>
	                  </div>
					
					<add-to-cart-button :productSelected="productSelected"></add-to-cart-button> 
					
					<br style="clear:both;">
					
				</article>

			</div>

            <div class="productData">
            	
				<ui-tabs class="infos" v-if="false">
	                <ui-tab title="Descripción del producto" v-html="product.content[$i18n.locale].description"></ui-tab>
	                <ui-tab title="Datos técnicos" v-html="product.content[$i18n.locale].details"></ui-tab>
	            </ui-tabs>
	            
		        <em class="social">
					<vue-goodshare-facebook 
						title_social="Facebook"
					    has_counter
					    has_icon 
					></vue-goodshare-facebook>
					<vue-goodshare-twitter 
						title_social="Twitter"
					    has_icon 
					></vue-goodshare-twitter>
					<vue-goodshare-whatsapp 
						v-if="isMobileDevice()"
						title_social="WhatsApp"
					    has_icon 
					></vue-goodshare-whatsapp>
					<vue-goodshare-telegram 
						v-if="isMobileDevice()"
						title_social="Telegram"
					    has_icon 
					></vue-goodshare-telegram>
				</em>
	        </div>   
		</div>

    </transition>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

import AddToCartButton from './AddToCartButton.vue'

import VueGoodshareFacebook from 'vue-goodshare/src/providers/Facebook.vue'
import VueGoodshareTwitter from 'vue-goodshare/src/providers/Twitter.vue'
import VueGoodshareWhatsapp from 'vue-goodshare/src/providers/WhatsApp.vue'
import VueGoodshareTelegram from 'vue-goodshare/src/providers/Telegram.vue'

export default {
  name: 'Product',
  components: { VueGoodshareFacebook,VueGoodshareTwitter,VueGoodshareWhatsapp,VueGoodshareTelegram,swiper, swiperSlide, AddToCartButton },
  props: {
        type: {
            type: String,
            default: 'fullPage'
        }
  },
  data () {
    return {
    	loaded: false,
    	cartCss:'',
    	finalPrice:'...',
    	oldPrice:'',
    	delivery:'',
    	productSelected:{},
    	productTitle:'',
    	typeSelected:'',
    	shortDescription:null,
		swiperOptionTop: {
          spaceBetween: 5,
          //loop: true,
          //loopedSlides: 5, //looped slides should be the same
          slideToClickedSlide: true,
          navigation: {
            nextEl: '.swiper-button-next.prod',
            prevEl: '.swiper-button-prev.prod'
          }
        },
        swiperOptionThumbs: {
          direction: 'vertical',
          spaceBetween: 5,
          slidesPerView: 4,
          touchRatio: 0.2,
          //loop: true,
          //loopedSlides: 5, //looped slides should be the same
          slideToClickedSlide: true
        },
        product: ''
    }
  },
  head : function() {
  		var vm=this;
		return {...vm.$route.meta,
	    	...{
		    	title: {
				  inner: vm.productTitle
				},
		    }
		}    
  },
  methods: {
  	getData: function(apiUrl) {

        var vm = this;
        
        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	    	
        this.$http.get(apiUrl, { cache: auth })
        .then(function (response) {
        	vm.product = response.data[0].json;
            console.log(vm.product);
            vm.loaded=true;
            vm.productTitle=vm.product.content[vm.$i18n.locale].name
            vm.$emit('updateHead')

			if(vm.product.images.length>1) {
				vm.$nextTick(() => {
		        const swiperTop = vm.$refs.swiperTop.swiper
		        const swiperThumbs = vm.$refs.swiperThumbs.swiper
		        swiperTop.controller.control = swiperThumbs
		        swiperThumbs.controller.control = swiperTop
		      })
			}
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
    },
  	getOffset: function( selector ) {
  		if(document.querySelector(selector)) var el = document.querySelector(selector);
  		else return { top: 0, left: 0, width:0, height: 0 };
  		
	    var x = 0;
	    var y = 0;
	    var elo = el;
	    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
	        x += el.offsetLeft - el.scrollLeft;
	        y += el.offsetTop - el.scrollTop;
	        el = el.offsetParent;
	    }
	    return { top: y - window.scrollY, left: x - window.scrollX, width:elo.offsetWidth, height: elo.offsetHeight };
	},
  	simulateClick: function (elem) {
		var evt = new MouseEvent('click', {
			bubbles: true,
			cancelable: true,
			view: window
		});
		var canceled = !elem.dispatchEvent(evt);
	},
  	selectProduct: function(product,variation){
  		var vm=this;
  		for (var i = 0, len = product.types.length; i < len; i++) {
		  if(product.types[i].name==variation) {
		  	vm.finalPrice = product.types[i].price.amount;
		  	if(product.types[i].price.oldPrice>product.types[i].price.amount) vm.oldPrice = product.types[i].price.oldPrice;
		  	return product.types[i];
		  }
		}
		return false;
  	},
  	getVariations: function(product){
  		var variation = [];
  		for (var i = 0, len = product.types.length; i < len; i++) {
		  variation.push(product.types[i].name);
		}
  		return Array.from(new Set(variation));
  	},
	setPrice: function(price){
		this.finalPrice = price;
	},
  	isMobileDevice: function() {
	    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
	},
  	soldout:function(item){
  		return item==false||item.stock==0 ? 'soldout' : '';
  	},
    toSlide:function(i) {
       this.$refs.swiperTop.swiper.slideToLoop(i, 500)
    },
    cartAnimation:function(selector){
    	var vm=this;
    	return 'background-image: url('+vm.product.images[0].img+');left:'+vm.getOffset(selector).left+'px;top:'+vm.getOffset(selector).top+'px;width:'+vm.getOffset(selector).width+'px;height:'+vm.getOffset(selector).height+'px;';
    }
  },
  watch: {
  	countCart: function(){
  		
  		var vm=this;
  		if(!document.querySelector('.shopping-cart')) {
	  		vm.cartCss = vm.cartAnimation('.swipe');
	  		setTimeout(function(){ 
	  			document.querySelector('.addProductAnimation').classList.add("animated");
	  			vm.cartCss = vm.cartAnimation('.amount');
		  		setTimeout(function(){ 
		  			document.querySelector('.addProductAnimation').classList.remove("animated");
		  			vm.cartCss = vm.cartAnimation('.swipe');
		  		}, 1000);
	  		}, 10);
  		}

  	},
  	typeSelected: function(){
  		var vm=this;
  		if(vm.selectProduct( vm.product, vm.typeSelected)){
	  		vm.setPrice( vm.selectProduct( vm.product, vm.typeSelected).price.amount );
  		}
  	},
  	productSelected: function(newValue, oldValue){
  		var vm=this;
		if(vm.product && vm.productSelected)vm.productSelected.fullProduct = JSON.parse( JSON.stringify( vm.product ) );
		
  	}, 
  	
  },
	computed: {
		...mapGetters({
			countCart: 'countCart',
			checkoutStatus: 'checkoutStatus',
		}),
	},
	beforeMount() {
			if(this.$route.params.slug) {
				this.getData('producte/slug/'+this.$route.params.slug);
			}
	},
	mounted: function() {
	  		var vm=this;
	  		
	  		//vm.simulateClick( document.querySelector('#swatch-0-variation') );

	},
	beforeRouteUpdate (to, from, next) {
    	this.getData('producte/slug/'+to.params.slug);
    	next();
	}
	
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

@keyframes basket {
    0% {opacity:0;}
    10% {opacity:.5;}
    90% {opacity:.5;}
    100% {opacity:0;}
}

.productData{
	margin-top:5px;
	display:block;
	padding: 0 15px;
}

.product{
	padding: 0 15px;
	display:flex;
	flex-wrap:wrap;
	
	.addProductAnimation{
		position:fixed;
		z-index:-1;
		width:100px;
		height:100px;	
		background-size:cover;
		background-position:center;
		opacity:0;
		border-radius:500px;
		border:0px solid @fedcolor;
		
		&.animated {
			z-index:10;
			animation: basket 1s ease forwards;
			transition: all 1s ease;
			border:10px solid @fedcolor;
		}
	}
	
	article.content{
		padding: 0 15px;
		width: 50%;
		@media(max-width:@screenDesktop) {
			padding: 15px;
			width: 100%;
			order:1;
		}
		position:relative;
		display:flex;
		flex-direction:column;
		h1{
			margin:0;
			text-transform:initial;
			&:after{
				display:none;
			}
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
	aside.images{
		width:50%;
		@media(max-width:@screenDesktop) {
			width:100%!important;
			order:2;
		}
		height:450px;
		display:flex;
		padding: 0;
		&.single{
			width:40%;
		}
	    margin: 0;
	    position: relative;
	    
	    .swiper-container-vertical {
	    	@media(max-width:@screenMobile) {
				display:none;
			}
	    }

		.swiper-slide {
		    background-size: cover;
		    background-position: center;
		}
		.gallery-top {
			height: 100%;
		    width: 100%;
		    padding: 0!important;
		}
		.gallery-thumbs {
		    height:450px;
		    width: 25%;
		    box-sizing: border-box;
		    padding: 0!important;
		    margin-right: 5px!important;
		    cursor:pointer;
		}
		.gallery-thumbs .swiper-slide {
		    width: 100%;
		    height: 100%;
		    opacity: 1;
		}
		.gallery-thumbs .swiper-slide-active {
		    opacity: 1;
		    border: 1px solid @fedcolor;
		}
	    
		.thumbs {
	        width: 10%;
		    min-width: 50px;
		    margin-right: 15px;
		    a {
			    transition: all 0.3s ease-in-out;
			    border-radius: 1px;
			    border: 1px solid #e2e2e3;
			    display: block;
			    margin-bottom: 8px;
			    position: relative;
			    width: 100%;
			    &.active {
					border-color: @fedcolor;
			    }
			}
			img {
			    display: block;
			    width: 100%;
			}
		}
		.big {
		    width: 100%;
		    .img {
			    transition: all 600ms ease-out 0s;
			    transform: translateY(0px);
			    border-radius: 1px;
			    background: transparent no-repeat center center;
			    background-size: cover;
			    border: 1px solid #e2e2e3;
			    display: block;
			    height: 0;
			    opacity: 1;
			    padding-bottom: 100%;
			}
		}
	}
}

.swatches {
    margin: 17px 0 25px;
}
.selector-wrapper,
#productSelect {
    display: none
}
.swatch {
    padding-right:30px;
    padding-top:10px;
    display: inline-table;
}
.swatch:nth-last-child(2) {
    margin-right: 0
}
.swatch .header {
    font-weight:bolder;
}
.swatch input {
    display: none
}
.swatch .swatch-element {
    display: inline-block;
    margin: 5px 8px 0 0;
    position: relative
}
.swatch .color label {
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
    border: 1px solid;
    cursor: pointer;
    display: block;
    height: 42px;
    padding: 7px 0 0 7px;
    width: 42px
}
.swatch .color label span {
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
    display: block;
    height: 26px;
    position: relative;
    width: 26px
}
.swatch .color label span:after {
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
    background: transparent url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjEycHgiIGhlaWdodD0iOXB4IiB2aWV3Qm94PSIwIDAgMTIgOSIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWxuczpza2V0Y2g9Imh0dHA6Ly93d3cuYm9oZW1pYW5jb2RpbmcuY29tL3NrZXRjaC9ucyI+CiAgICA8ZyBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMTIzMS4wMDAwMDAsIC0xMzAyLjAwMDAwMCkiIGZpbGw9IiNGRkZGRkYiPgogICAgICAgICAgICA8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMy4wMDAwMDAsIDEyNDYuMDAwMDAwKSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTIzNS45MzgzNyw1OC40NTA1ODYxIEwxMjM0LjUyMTE2LDU5LjM5NTUzMDcgTDEyMzcuNTQ4NDgsNjMuOTM2NzE1OCBMMTI0NS45MjIyNSw1OC4zNTM5MTk4IEwxMjQ0Ljk3NzczLDU2LjkzNjcxNTggTDEyMzguMDIxMTYsNjEuNTc0NTY3MSBMMTIzNS45MzgzNyw1OC40NTA1ODYxIEwxMjM1LjkzODM3LDU4LjQ1MDU4NjEgWiIgaWQ9ImZhamZrYSIgc2tldGNoOnR5cGU9Ik1TU2hhcGVHcm91cCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTI0MC4yMjE3MDYsIDYwLjQzNjcxNikgcm90YXRlKC0xMC4wMDAwMDApIHRyYW5zbGF0ZSgtMTI0MC4yMjE3MDYsIC02MC40MzY3MTYpIj48L3BhdGg+CiAgICAgICAgICAgIDwvZz4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==') no-repeat center center;
    bottom: 0;
    content: "";
    display: block;
    height: 100%;
    left: 0;
    opacity: 0;
    position: absolute;
    top: 0;
    width: 100%
}
.swatch .plain label {
	transition: all 0.3s ease-in-out;
    border-radius: 35px;
    font-family: "montserratbold", sans-serif;
    border: 1px solid #87212e;
    color: #87212e;
    cursor: pointer;
    display: block;
    height: 42px;
    padding-top: 12px;
    text-align: center;
    padding: 12px 15px;
}
.swatch .color input:checked+label span:after {
    opacity: 1
}
.swatch input:not(:checked)+label {
    border-color: #edeff2 !important
}
.swatch input:not(:checked)+label:hover {
    border-color: #b5b6bd !important
}
.swatch .plain input:not(:checked)+label {
    color: #16161a !important
}
.crossed-out {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    z-index: 10;
    pointer-events:none;
}
.swatch .swatch-element .crossed-out {
    display: none
}
.swatch .swatch-element.soldout .crossed-out {
    display: block
}
.swatch .swatch-element.soldout label {
    opacity: 0.6
}
.swatch .tooltip {
    border-radius: 2px;
    text-align: center;
    background-color: rgba(22, 22, 26, 0.93);
    color: #fff;
    bottom: 100%;
    padding: 10px;
    display: block;
    position: absolute;
    width: 100px;
    left: -23px;
    margin-bottom: 15px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all .25s ease-out;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
    z-index: 10000;
    box-sizing: border-box
}
.swatch .tooltip:before {
    bottom: -20px;
    content: " ";
    display: block;
    height: 20px;
    left: 0;
    position: absolute;
    width: 100%
}
.swatch .tooltip:after {
    border-left: solid transparent 10px;
    border-right: solid transparent 10px;
    border-top: solid rgba(22, 22, 26, 0.93) 10px;
    bottom: -10px;
    content: " ";
    height: 0;
    left: 50%;
    margin-left: -13px;
    position: absolute;
    width: 0
}
.swatch .swatch-element:hover .tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateY(0px)
}
.swatch.error {
    background-color: #E8D2D2 !important;
    color: #333 !important;
    padding: 1em;
    border-radius: 5px
}
.swatch.error p {
    margin: 0.7em 0
}
.swatch.error p:first-child {
    margin-top: 0
}
.swatch.error p:last-child {
    margin-bottom: 0
}
.swatch.error code {
    font-family: monospace
}

</style>