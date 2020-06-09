<template>
    <transition name="fade">
	    <div>

			<h1>Clubs</h1>
			
			<span class="button-right">
				<ui-button icon="table_chart" icon-position="left" size="big" @click="csv('clubs')">CSV</ui-button>
				<ui-button icon="add_circle_outline" icon-position="left" size="big" @click="edit({id:''})">Afegir Club</ui-button>
			</span>
			
			<div class="vuetableContainer">
				
				<div class="searchFilter">
				 <ui-textbox
				    floating-label
	                autocomplete="off"
	                :label="$t('common.search')"
					type="text"
	                v-model="filterText"
                	@keydown-enter="getData('clubs')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('clubs')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					<template slot="actions" scope="props">
						<td class="actions">
							<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="edit(props.row)">Editar</ui-button>
							<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="confirmAction(remove, props.row)">Borrar</ui-button>
						</td>
					</template>
				</tablerone>
				<paginate
				    :page-count="Math.ceil(list.total / list.per_page)"
					:clickHandler="clickCallback"
					:page-range="2"
    				:margin-pages="0"
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
	name: 'Clubs',
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    list:{},
		    columns:[
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            },
	            {
	                label: 'Població',
	                field: 'poblacio',
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
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/club/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/club/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getData('club');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {

	    	var vm=this;
	    	vm.getData('club', pageNum);
	    },
		getData: function(listName, page){
	        var vm = this;
	        
	        var searchFilter = vm.filterText!='' ? '/search/'+vm.filterText : '';
	        var searchPage = page!=null ? '/p/'+ page : '/p/0';
	        
	        vm.$http.get(listName+searchFilter+searchPage, { cache: false })
	        .then(function (response) {
	            vm.list = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		csv: function(listName) {
	        window.location.href='/api/'+listName+'?csv=true';
		}
	},
	mounted: function () {
		var vm=this;
		vm.getData('club');
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
	}
}
</script>

<style lang="less">



</style>