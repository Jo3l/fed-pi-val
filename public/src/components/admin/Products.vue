<template>
    <transition name="fade">
	    <div>

			<h1>Productes</h1>
			
			<span class="button-right">
		        <ui-button icon="table_chart" icon-position="left" size="big" @click="csv('productes')">CSV</ui-button>
				<ui-button icon="add_circle_outline" icon-position="left" size="big" @click="newProduct">Afegir Producte</ui-button>
			</span>
			
			<div class="vuetableContainer">
				
				<div class="searchFilter" style="visibility:hidden;">
				 <ui-textbox
				    floating-label
	                autocomplete="off"
	                :label="$t('common.search')"
					type="text"
	                v-model="filterText"
                	@keydown-enter="getData('productes')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('productes')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="products" :tableColumns="columns">
					<th slot="headActions"></th>
					
					<template slot="actions" scope="props">
						<td class="actions">
							<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="edit(props.row)">Editar</ui-button>
							<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="confirmAction(remove, props.row)">Borrar</ui-button>
						</td>
					</template>
				</tablerone>
  
  
			</div>
			
	    </div>
    </transition>
</template>

<script>

import Table from '../custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    products:{},
		    columns:[
	            {
	                label: 'Id',
	                field: 'id',
	                html: false,    
	            },
	            {
	                label: 'Nom',
	                field: 'json.content.val.name',
	                html: false,    
	            }
	        ],
		    filterText:'',
		}
	},
	methods: {
		confirmAction: function(func, item) {

			if(confirm( this.$i18n.t('common.confirm') )) {
				func(item);
			}

		},
		newProduct:function(row) {
	    	this.$router.push({ path: `/admin/producte/` });
	    },
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/producte/`+row.json.content.es.slug });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.delete('/producte/'+row.id)
	        .then(function (response) {
	        	
	            vm.getData('/productes');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	  	getData: function(apiUrl) {
	        var vm = this;
	        this.$http.get(apiUrl, { cache: false }
	        /*
	        , {
			    // use before callback
			    before(request) {
			      // abort previous request, if exists
			      if (this.previousRequest) {
			        this.previousRequest.abort();
			      }
			      // set previous request on Vue instance
			      this.previousRequest = request;
			    }
			}
			*/
			).then(function (response) {
				
	            vm.products = response.data;
	            vm.allProducts = response.data;
	            
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
			this.getData('/productes');
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