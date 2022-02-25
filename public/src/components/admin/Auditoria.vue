<template>
    <transition name="fade">
	    <div>

			<h1>Auditoria</h1>
			
			<!--span class="button-right">
		        <ui-button icon="table_chart" icon-position="left" size="big" @click="csv('comanda')">CSV</ui-button>
			</span-->
			
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
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('auditoria')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="auditoria" :tableColumns="columns">
					<th slot="headActions"></th>
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

	    </div>
    </transition>
</template>

<script>

import Table from '../custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
	name: 'Auditoria',
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    auditoria:{},
		    visibleOrder:{},
			// {"timestamp":"2021-07-07 08:58:07","taula":"jugador","operacio":"update","id":"0","camp":"dni","abans":"0000000T","despres":"12345678Z","autor":null}
		    columns:[
	            {
	                label: 'Moment',
	                field: 'timestamp',
	                html: false,    
	            },
	            {
	                label: 'Taula',
	                field: 'taula',
	                html: false,    
	            },
	            {
	                label: 'Operació',
	                field: 'operacio',
	                html: false,    
	            },
	            {
	                label: 'Num. Soci',
	                field: 'numsoci',
	                html: false,    
	            },
	            {
	                label: 'Camp',
	                field: 'camp',
	                html: false,    
	            },
	            {
	                label: 'Abans',
	                field: 'abans',
	                html: false,    
	            },
	            {
	                label: 'Després',
	                field: 'despres',
	                html: false,    
	            }/*,
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
	                //field: 'isNaN(resultat)?"<span style=color:red>"+resultat+"</span>":"<span style=color:green>"+resultat+"</span>"',
	                field: 'resultat',
	                html: true,    
	            },*/
	        ],
		    filterText:'',
		}
	},
	methods: {
		confirmAction: function(func, item) {

			if(confirm( this.$i18n.t('common.confirm'))) {
				func(item);
			}

		},
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('auditoria', pageNum);
	    },
		diaMesAny: function(time) {
			if (!time) return '';
			return time.substring(6,8)+'/'+time.substring(4,6)+'/'+time.substring(2,4);
		},	    
	  	getData: function(apiUrl, page) {
	        var vm = this;
	        const nomtipus= { "insert": 'Afegit', "update": 'Canvi', "delete": 'Esborrat' };
	        var page = page!=null ? '/p/'+ (parseInt(page) - 1) : '/p/0';
	        page+= '/o/timestamp-';
	        this.$http.get(apiUrl+page, { cache: false }
			).then(function (response) {
				response.data.forEach( (a)=>{ 
					console.log(a);
					a.operacio= nomtipus[a.operacio]; 
					//a.data= vm.diaMesAny( a.data ); 
				} );
	            vm.auditoria = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
		estat: function(que) {
			var vm= this;
			vm.visibleOrder.estat= que;
			console.log(vm.visibleOrder.json,vm.visibleOrder.data)
			//console.log(JSON.parse(vm.visibleOrder.json).data)
			this.$http.post('/comanda/'+vm.visibleOrder.id, {"id":vm.visibleOrder.id,"data":vm.visibleOrder.data,"estat":que })
			.catch( (e)=>console.log('error',e) );
			vm.orders.forEach( (o)=> { if (o.id==vm.visibleOrder.id) { 
				o.estat= que;
				o.estatdesc= vm.estats[que];
			} } );
		}
	  },
	  mounted: function() {
			this.getData('auditoria');
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