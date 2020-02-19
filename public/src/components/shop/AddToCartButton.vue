<template>
    <transition name="fade">
					<div class="addToCart">
						<span class="quantity-label">{{$t('cart.quantity')}}: <input class="item-quantity" type="number" v-model="quantity"> </span>
						<ui-button icon="shopping_cart" color="fedpival" @click="addProductToCart" :disabled="!productSelected || productSelected.stock<=0">{{$i18n.t('cart.addToBasket')}}</ui-button>
					</div>
    </transition>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Product',
  components: { },
  props: ['productSelected'],
  data () {
    return {
		quantity:1,
    }
  },
  methods: {
		addProductToCart: function() {
			for(var i=0;i<this.quantity;i++){
				this.$store.dispatch('addProductToCart', this.productSelected);
			}
		},
  },
  watch: {

  },
  mounted: function() {
  		var vm=this;
  }
}
</script>

<style lang="less" scoped>
.quantity-label{
	text-transform: capitalize;
}
.item-quantity{
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    font-size: 0.875rem;
    font-weight: 600;
    height: 2.25rem;
    text-align: center;
    letter-spacing: 0.02em;
    line-height: 1;
    border: 1px dashed #232323;
    border-right:none;
    width: 4em;
    padding-left: 1em;
}
.addToCart{
	height: 100%;
    display: flex;
    align-items: flex-end;
    margin-bottom: 15px;
}

</style>