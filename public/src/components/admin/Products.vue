<template>
    <transition name="fade">
	    <div>

			<h1>Productes</h1>
			
			<span class="button-right">
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
							<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="remove(props.row)">Borrar</ui-button>
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
	name: 'Clubs',
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
	                field: 'nom',
	                html: false,    
	            }
	        ],
		    filterText:'',
		}
	},
	methods: {
		newProduct:function(row) {
	    	this.$router.push({ path: `/admin/producte/` });
	    },
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/producte/`+row.slug });
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