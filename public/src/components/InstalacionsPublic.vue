<template>
    <transition name="fade">
	    <div>
	    	
			<div style="position: absolute;right: 20px;margin-top: -15px;"><ui-fab color="normal" icon="keyboard_backspace" @click="goBack()" size="small"></ui-fab></div>
			
			<h1>{{$i18n.t('common.sportPlace')}}</h1>

			<div class="vuetableContainer">
				
				<div class="searchFilter">
				 <ui-textbox
				    floating-label
	                autocomplete="off"
	                :label="$t('common.search')"
					type="text"
	                v-model="filterText"
                	@keydown-enter="getData('trinquet')"
	            ></ui-textbox>
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('trinquet')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					<template slot="actions" scope="props">
						<td class="actions">
							<router-link :to="{ path: current+'/'+props.row.id }"><ui-button color="default" icon="visibility" icon-position="left" size="small" type="secondary">Info</ui-button></router-link>
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

import Table from './custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
  	components: {'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    list:{},
		    current:window.location.pathname.replace(/\/$/, ""),
		    columns:[
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            },
	            {
	                label: 'Població',
	                field: 'pob',
	                html: false,    
	            }
	            
	        ],
		    filterText:'',
		}
	},
	methods: {
		goBack:function(){
    		window.history.back();
		},
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/trinquet/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/trinquet/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getData('trinquet');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('trinquet', pageNum);
	    },
		getData: function(listName, page){
	        var vm = this;
	        
	        var searchFilter = vm.filterText!='' ? '/search/'+vm.filterText : '';
	        var searchPage = page!=null ? '/p/'+ page : '/p/0';
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get(listName+searchFilter+searchPage, { cache: auth })
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
		vm.getData('trinquet');
	},
	created: function() {

	}
}
</script>

<style lang="less">



</style>