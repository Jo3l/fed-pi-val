<template>
    <transition name="fade">
					<div class="addToCart">
						<ui-button icon="shopping_cart" color="fedpival" @click="$store.dispatch('addProductToCart', productSelected)" :disabled="!productSelected || productSelected.stock<=0">Afegir a la cistella</ui-button>
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
  methods: {

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