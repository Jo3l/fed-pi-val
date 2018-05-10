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
	            <ui-icon-button color="default" icon="clear" type="secondary" @click="filterText=''"></ui-icon-button>
				</div>

				<tablerone :tableList="list" :tableColumns="columns">
					<th slot="headActions"></th>
					
					<template slot="icon1" scope="props">
						<td class="actiu">
							<ui-icon icon="lens" class="green" v-if="props.row.actiu"></ui-icon>
							<ui-icon icon="trip_origin" class="red" v-else ></ui-icon>
						</td>
					</template>
					
					<template slot="icon2" scope="props">
						<td class="actiu">
							<ui-icon icon="lens" class="green" v-if="props.row.segur"></ui-icon>
							<ui-icon icon="trip_origin" class="red" v-else ></ui-icon>
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
	        console.log(page);
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

@import "../../assets/less/defines.less";


.table {
	.actiu > span {
		font-size:1em!important;
		text-align:center;
	}
}

.button-right{
	margin:0 1em 0 auto;
	float:right;
}

.vuetableContainer {
	width:90%!important;
	margin:0 auto;
    .actions {
    	width: 222px;
    	padding: 3px 0;
    }
}

.searchFilter {
	width:50%;
	margin-top: -16px;
	min-width:320px;
	display:flex;
	&>div{min-width: 80%;}
	button {
		margin-top: 20px;
	}
	@media(max-width:@screenTablet) {
		margin-top: 52px;
		width:initial;
	}
}


.pagination {
  display: inline-block;
  padding-left: 0;
  margin: 0;
  border-radius: 3px;

  > li {
    display: inline;
    > a,
    > span {
      position: relative;
      float: left; // Collapse white-space
      padding: 5px;
      text-decoration: none;
	  min-width: 40px;
	  text-align: center;
    }
    &:first-child {
      > a,
      > span {
        margin-left: 0;
      }
    }
    &:last-child {
      > a,
      > span {
      }
    }
  }

  > li > a,
  > li > span {
    &:hover,
    &:focus {
      z-index: 3;
      border-color: red;
    }
  }

  > .active > a,
  > .active > span {
    &,
    &:hover,
    &:focus {
      z-index: 2;
      background-color:#eeeeee;
      cursor: default;
    }
  }

  > .disabled {
    > span,
    > span:hover,
    > span:focus,
    > a,
    > a:hover,
    > a:focus {
      
    }
  }
}


</style>