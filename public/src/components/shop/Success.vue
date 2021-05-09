<template>
    <transition name="fade">
		<div class="success">
			<br>
			<section v-if="datos">
			<h2>{{ $i18n.t('cart.thanks') }}</h2>
				<br>
				<dl>
				  <dt>{{ $i18n.t('cart.orderNumber') }}: {{datos.comanda}}</dt>
				  <dt>{{ $i18n.t('cart.name') }}: {{datos.name}}</dt>
				  <dt>{{ $i18n.t('cart.adr') }}: {{datos.address}}</dt>
				  <dt>{{ $i18n.t('cart.cp') }}: {{datos.cp}}</dt>
				  <dt>{{ $i18n.t('cart.tlf') }}: {{datos.tel}}</dt>
				</dl>
				<br>
				<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop') }">
					<ui-button raised size="normal">{{ $i18n.t('cart.continueBuying') }}</ui-button>
				</router-link>
			</section>

			
			<ui-modal ref="error" title="">
	            {{error}}
		        <div slot="footer">
		            <router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop') }">
						<ui-button size="normal">OK</ui-button>
					</router-link>
		        </div>
	        </ui-modal>
        
		</div>

    </transition>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Success',
  components: { },
  data () {
    return {
		datos:null,
		error:''
    }
  },
  methods: {

  },
  computed: {
    ...mapGetters({
    	countCart: 'countCart',
    	cart: 'cart',
    	checkoutStatus: 'checkoutStatus',
    	cartTotalPrice :'cartTotalPrice',
    	validate:'validate'
    })
  },
  mounted: function() {
	  	var vm=this;
	  	var res= location.href.split('/').pop();
	  	
	  	if (res=='bank-transfer' || res=='cash-on-delivery') {
	        vm.error= 'Comanda finalitzada amb èxit. ';
	        if (res=='bank-transfer') vm.error+= 'Rebrà instruccions per email per a la transferència.';
	        if (res=='cash-on-delivery') vm.error+= 'Haurà de fer el pagament al rebre la comanda.';
	        this.$store.dispatch('deleteCart');
			vm.$refs.error.open();
	  		return;
	  	}
	  	
		/*
		// abans reviem control de passarel.la per ací. ara va directe a /api/pagat aixi que nomes he de mostrar resultat indicat
		
		vm.$http.post('/pagat', vm.$route.query)
        .then(function (response) {
        	
        	vm.datos=response.data;
            this.$store.dispatch('deleteCart');
            
        } )
        .catch(function (error) {
	        vm.error=error.response.data.error;
			vm.$refs.error.open();
        });
        */
        vm.datos= vm.$route.query;
        if(res=='OK') {
    	    vm.error= 'Compra realizada con éxito';
        	this.$store.dispatch('deleteCart');
        } else {
    	    vm.error= 'Problema al procesar el pago';
        }
	    vm.$refs.error.open();

  }

}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.success {
	padding: 1em 2em;
}
</style>