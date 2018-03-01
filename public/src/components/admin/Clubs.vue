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
                	@keydown-enter="getData('clubs')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('clubs')"></ui-icon-button>
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
	            },
	            {
	                label: 'Codi Postal',
	                field: 'cp',
	                html: false,    
	            },
	            {
	                label: 'Telèfon',
	                field: 'telefon',
	                html: false,    
	            }
	            
	        ],
		    filterText:'',
		}
	},
	methods: {
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/clubs/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/clubs/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getData('clubs');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('clubs', pageNum);
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
		vm.getData('clubs');
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