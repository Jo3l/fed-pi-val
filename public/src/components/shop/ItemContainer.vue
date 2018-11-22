<template>

    	<div class="itemContainer">
    		<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop')+'/'+product.json.content[$i18n.locale].slug }">
	    		<em v-if="product.destacada">{{$i18n.t('cart.highlight')}}</em>
		    	<div class="img" :style="'background:url('+product.json.images[0].img+') no-repeat center / cover;'"></div>
	    	</router-link>
	    	<div class="productText">
	    		<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop')+'/'+product.json.content[$i18n.locale].slug }">
				    <div class="name">{{ product.json.content[$i18n.locale].name }}</div>
			    	{{$i18n.t('cart.price')}}: <div class="price">{{ product.json.types[0].price.amount }} €</div>
			    	<div class="oldPrice" v-if="product.json.types[0].price.amount<product.json.types[0].price.oldPrice">{{$i18n.t('cart.before')}}: <span>{{ product.json.types[0].price.oldPrice }} €</span></div>
		    	</router-link>
		    	<slot name="categoria"></slot>
	    	</div>
    	</div>


</template>

<script>


export default {
  props: ['product'],
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";

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
		p{
			cursor:pointer;
			&:hover{
				text-decoration:underline;
			}
		}
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

</style>