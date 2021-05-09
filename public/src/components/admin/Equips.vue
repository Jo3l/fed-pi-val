<template>
    <transition name="fade">
	    <div>

			<h1>Equips</h1>
			
			<span class="button-right">
				<ui-button icon="table_chart" icon-position="left" size="big" @click="csv('equip')">CSV</ui-button>
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
	            <ui-icon-button color="default" icon="search" type="secondary" @click="getData('equip')"></ui-icon-button>
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					<template slot="actions" scope="props">
						<td class="actions">
							<ui-button color="default" icon="visibility" icon-position="left" size="small" type="secondary" @click="mou(props.row)">Jugadors</ui-button>
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
	                field: 'nomclub',
	                html: false,    
	            },
	            {
	                label: 'Competició',
	                field: 'nomcompeticio',
	                html: false,    
	            },
	        ],
		    filterText:'',
		}
	},
	methods: {
		mou:function(row) {
	    	this.$router.push({ path: `/admin/jugadors/equip/`+row.id });
	    },
		clickCallback: function(pageNum) {

	    	var vm=this;
	    	vm.getData('equip', pageNum);
	    },
		getData: function(listName, page){
	        var vm = this;
	        
	        var searchFilter = vm.filterText!='' ? '/search/'+vm.filterText : '';
	        var searchPage = page!=null ? '/p/'+ page : '/p/0/o/creacio-';
	        
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
		vm.getData('equip');
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