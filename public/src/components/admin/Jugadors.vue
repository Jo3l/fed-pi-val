<template>
    <transition name="fade">
	    <div>

			<h1>Jugadors</h1>
			
			<span class="button-right">
				<ui-button icon="add_circle_outline" icon-position="left" size="big" @click="edit({id:''})">Afegir Jugador</ui-button>
			</span>
			
			<div class="vuetableContainer">
				
				<div class="searchFilter">
				 <ui-textbox
				    floating-label
	                autocomplete="off"
	                :label="$t('common.search')"
					type="search"
	                v-model="filterText"
                	@keydown-enter="getData('jugador')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('jugador')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText='';getData('jugador')"></ui-icon-button>
				</div>

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					
					<template slot="icon1" scope="props">
						<td class="actiu">
							<!--ui-icon icon="lens" class="fedcolor" v-if="props.row.actiu==1"></ui-icon> 
							<ui-icon icon="trip_origin" class="fedcolor" v-else ></ui-icon-->
							<ui-checkbox v-model="props.row.actiu" @change="saveChanges(props.row)">actiu</ui-checkbox>
						</td>
					</template>
					
					<template slot="icon2" scope="props">
						<td class="actiu">
							<!--ui-icon icon="lens" class="fedcolor" v-if="props.row.segur==1"></ui-icon>
							<ui-icon icon="trip_origin" class="fedcolor" v-else ></ui-icon-->
							<ui-checkbox v-model="props.row.segur" @change="saveChanges(props.row)">actiu</ui-checkbox>
						</td>
					</template>
					
					<template slot="actions" scope="props">
						<td class="actions">
							<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="edit(props.row)">Editar</ui-button>
							<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="remove(props.row)">Borrar</ui-button>
						</td>
					</template>
					
				</tablerone>
				<paginate
				    :page-count="Math.ceil(list.total / list.per_page)"
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
	name: 'Jugadors',
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    list:{},
		    columns:[
	            {
	                label: 'Actiu',
	                field: 'actiu',
	                html: false,
	                icon: true
	            },
	            {
	                label: 'Assegurat',
	                field: 'segur',
	                html: false,
	                icon: true
	            },
	            {
	                label: 'Num. Soci',
	                field: 'numsoci',
	                html: false,    
	            },
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            },
	            {
	                label: 'Cognoms',
	                field: 'cognoms',
	                html: false,    
	            },
	            {
	                label: 'Club',
	                field: 'clubs',
	                html: false,    
	            }
	            
	        ],
		    filterText:'',
		}
	},
	methods: {
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/jugador/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.delete('/jugador/'+row.id)
	        .then(function (response) {
	        	
	            vm.getData('jugador');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {
	    	var vm=this;
	    	vm.getData('jugador', pageNum);
	    },
		getData: function(listName, page=1){
	        var vm = this;
	        var searchFilter = vm.filterText!='' ? '/search/'+vm.filterText : '';
	        var searchPage = '/p/' + (parseInt(page) - 1);
	        
	        vm.$http.get(listName+searchFilter+searchPage, { cache: false })
	        .then(function (response) {
	            vm.list = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		saveChanges: function(row) {
			var vm= this;
	        vm.$http.post('/jugador/'+ row.id , row)
	        .then(function (response) {
	        	console.log(response)
				vm.getData('jugador');
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		}
	},
	mounted: function () {
		var vm=this;
		vm.getData('jugador');
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(1)) {
	      this.$router.push({ path: `/` });
	    }
	}
}
</script>

<style lang="less">

</style>