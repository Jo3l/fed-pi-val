<template>
    <transition name="fade">
					<div class="addToCart">
						<ui-button icon="shopping_cart" color="fedpival" @click="addToCart" :disabled="!productSelected || productSelected.stock<=0">Afegir a la cistella</ui-button>
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
	addToCart:function(){
		var vm=this;
		vm.$store.dispatch('addProductToCart', vm.productSelected);
		console.log(this.counto);
	}
  },
  watch: {

  },
  mounted: function() {
  		var vm=this;
  }
}
</script>

<style lang="less" scoped>

.addToCart{
	height: 100%;
    display: flex;
    align-items: flex-end;
    margin-bottom: 15px;
}

</style>