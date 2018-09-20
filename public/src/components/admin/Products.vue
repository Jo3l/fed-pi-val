<template>
    <transition name="fade">
	    <div>

			<h1>Productes</h1>
			
			<span class="button-right">
				<ui-button icon="add_circle_outline" icon-position="left" size="big" @click="edit({id:''})">Afegir Producte</ui-button>
			</span>
			
			<div class="vuetableContainer">
				
				<div class="searchFilter">
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

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					
					<template slot="icon1" scope="props">
						<td class="actiu">
							<img :src="props.row.img">
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
					:page-range="2"
    				:margin-pages="0"
				    :prev-text="'Prev'"
				    :next-text="'Next'"
				    :container-class="'pagination'"
				    :page-class="'page-item'">
				</paginate>
  
  
			</div>
			
			<pre>
				<!-- { {templist} } -->
			{{list}}
			</pre>
			
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
	                label: 'Imatge',
	                field: 'actiu',
	                html: false,
	                icon: true
	            },
	            {
	                label: 'Id',
	                field: 'id',
	                html: false,    
	            },
	            {
	                label: 'Nom',
	                field: 'name',
	                html: false,    
	            }
	        ],
		    filterText:'',
		}
	},
	methods: {
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/producte/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/producte/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getData('producte');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getData('producte', pageNum);
	    },
		getData: function(listName, page){
	        var vm = this;
	        
	        var searchFilter = vm.filterText!='' ? '/search/'+vm.filterText : '';
	        var searchPage = page!=null ? '/p/'+ page : '/p/0';
	        vm.$http.get(listName+searchFilter+searchPage, { cache: false })
	        .then(function (response) {

	            var tempList = response.data;
console.log(tempList)

	            tempList.forEach(function(element) {
				  element.name = element.json.content[vm.$i18n.locale].name;
				  element.img = element.json.imagesThumb[0].img;
				});
	            
	            vm.list = tempList;
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		vm.getData('producte');
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