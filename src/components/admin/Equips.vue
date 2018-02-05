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
					type="text"
	                v-model="filterText"
                	@keydown-enter="getData('equips')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('equips')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="list.data" :tableColumns="columns">
					<th slot="headActions"></th></th>
					<template slot="actions" scope="props">
						<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="edit(props.row)">Editar</ui-button>
						<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="remove(props.row)">Borrar</ui-button>
					</template>
				</tablerone>
				<paginate
				    :page-count="Math.ceil(list.total / list.per_page)"
					:clickHandler="clickCallback"
					:page-range="2"
    				:margin-pages="0"
				    :prev-text="'Prev'"
				    :next-text="'Next'"
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
	name: 'equips',
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
	                label: 'Club',
	                field: 'club',
	                html: false,    
	            }
	            
	        ],
		    filterText:'',
		}
	},
	methods: {
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/equips/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/equips/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getData('equips');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('equips', pageNum);
	    },
		getData: function(listName, page){
	        var vm = this;
	        
	        var searchFilter = vm.filterText!='' ? '/search/nom/'+vm.filterText : '';
	        var searchPage = page!=null ? '/p/'+ page : '';
	        
	        vm.$http.get(listName+searchFilter+searchPage)
	        .then(function (response) {
	            vm.list = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		vm.getData('equips');
	}
}
</script>

<style lang="less">



</style>