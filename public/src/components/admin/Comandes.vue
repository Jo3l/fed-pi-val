<template>
    <transition name="fade">
	    <div>

			<h1>Comandes</h1>
			
			<span class="button-right">
		        <ui-button icon="table_chart" icon-position="left" size="big" @click="csv('comanda')">CSV</ui-button>
			</span>
			
			<div class="vuetableContainer">
				
				<div class="searchFilter" style="visibility:hidden;">
				 <ui-textbox
				    floating-label
	                autocomplete="off"
	                :label="$t('common.search')"
					type="text"
	                v-model="filterText"
                	@keydown-enter="getData('orders')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('orders')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="orders" :tableColumns="columns">
					<th slot="headActions"></th>
					
					<template slot="actions" scope="props">
						<td class="actions">
							<ui-button color="default" icon="description" icon-position="left" size="small" type="secondary" @click="view(props.row)">Detalls</ui-button>
							<!--ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="confirmAction(remove, props.row)">Borrar</ui-button-->
						</td>
					</template>
				</tablerone>
				<paginate
				    :page-count="NaN"
					:clickHandler="clickCallback"
    				:margin-pages=0
				    :prev-text="$i18n.t('common.prev')"
				    :next-text="$i18n.t('common.next')"
				    :container-class="'pagination'"
				    :page-class="'page-item'">
				</paginate>
  
			</div>
			<ui-modal ref="modal" title="">
	            
	            <div class="list">
					<dl>
					  <dt><strong>Pagament:</strong> {{visibleOrder.payment}}</dt>
					  <dt><strong>{{ $i18n.t('cart.orderNumber') }}:</strong> {{visibleOrder.codi}}</dt>
					  <dt><strong>{{ $i18n.t('cart.name') }}:</strong> {{visibleOrder.name}}</dt>
					  <dt><strong>Email:</strong> {{visibleOrder.email}}</dt>
					  <dt><strong>{{ $i18n.t('cart.adr') }}:</strong> {{visibleOrder.address}}</dt>
					  <dt><strong>{{ $i18n.t('cart.cp') }}:</strong> {{visibleOrder.cp}}</dt>
					  <dt><strong>{{ $i18n.t('cart.tlf') }}:</strong> {{visibleOrder.tel}}</dt>
					</dl>
					<strong>Articles:</strong>
			    	<div class="shopping-cart-items final">
					    <div v-for="item in visibleOrder.cart">
					        <span class="item-name">{{item.fullProduct.content[$i18n.locale].name}} [{{getProductType(item)}}]</span>
					        <span class="item-price">{{getProductPrice(item)}}€</span>
					        <span class="item-quantity">{{$t('cart.quantity')}}: {{item.quantity}}</span>
					    </div>
					</div>
					<br>
					<p><strong>Total:</strong> {{visibleOrder.total}}€</p>
				</div>
	           
	        </ui-modal>
	    </div>
    </transition>
</template>

<script>

import Table from '../custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
	name: 'Clubs',
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    orders:{},
		    visibleOrder:{},
		    columns:[
	            {
	                label: 'Id',
	                field: 'id',
	                html: false,    
	            },
	            {
	                label: 'Codi',
	                field: 'codi',
	                html: false,    
	            },
	            {
	                label: 'Data',
	                field: 'data',
	                html: false,    
	            },
	            {
	                label: 'Import',
	                field: 'quantitat',
	                html: false,    
	            },
	            {
	                label: 'Tipus',
	                //field: vm.getTipus,
	                //field: this.json && this.json.substr(0,0)=='{'?console.log(JSON.parse(this.json)):null,
	                //field: 'JSON.parse(json).payment',
	                field: 'payment',
	                html: false,    
	            },
	            {
	                label: 'Codi resultat',
	                field: 'resultat',
	                html: false,    
	            }
	        ],
		    filterText:'',
		}
	},
	methods: {
		getProductType:function(item){
	  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
			  if(item.fullProduct.types[i].name==item.name) {
			  
			  	return item.fullProduct.types[i].name;
			  	
			  }
			}
			return false;
		},
		getProductPrice:function(item){
	  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
			  if(item.fullProduct.types[i].name==item.name && item.fullProduct.types[i].price) {
			  	
			  	return item.fullProduct.types[i].price.amount;
			  	
			  }
			}
			return false;
		},
		/*getTipus: function(row) { return JSON.parse(row.json).payment; },*/
		confirmAction: function(func, item) {

			if(confirm( this.$i18n.t('common.confirm') )) {
				func(item);
			}

		},
	  	view:function(row) {
	  		var vm=this;
	  		vm.visibleOrder = JSON.parse(row.json);
	  		vm.visibleOrder.codi = row.codi;
	  		vm.visibleOrder.total = row.quantitat;
	  		vm.$refs.modal.open();
	    },
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('comanda', pageNum);
	    },
		diaMesAny: function(time) {
			if (!time) return '';
			return time.substring(6,8)+'/'+time.substring(4,6)+'/'+time.substring(2,4);
		},	    
	  	getData: function(apiUrl, page) {
	        var vm = this;
	        const nomtipus= { "online-pay": 'targeta', "cash-on-delivery": 'contra-reemborsament', "bank-transfer": 'transferencia' };
	        var page = page!=null ? '/p/'+ (parseInt(page) - 1) : '/p/0';
	        page+= '/o/codi-';
	        this.$http.get(apiUrl+page, { cache: false }
			).then(function (response) {
				response.data.forEach( (a)=>{ 
					a.payment= nomtipus[(JSON.parse(a.json)).payment]; 
					if(!a.resultat && (JSON.parse(a.json)).payment=='online-pay') a.resultat = '!!! NO-COMPLETAT';
					a.data= vm.diaMesAny( (JSON.parse(a.json)).data ); 
				} );
	            vm.orders = response.data;
	            vm.allOrders = response.data;
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
		csv: function(listName) {
	        window.location.href='/api/'+listName+'?csv=true';
		}
	  },
	  mounted: function() {
			this.getData('comanda');
	  },
	  created: function() {
		    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
		      this.$router.push({ path: `/` });
		    }
	  }
}
</script>

<style lang="less">

td img {
	max-height:32px;
	position: absolute;
    margin-top: -5px;
}

</style>