<template>

  <div class="tree">

		<ul class="breadcrumb-wrapper">
			<li v-for="bread in breadCrumb" @click="updateBreadcrumb" v-bind:class="[ bread.id==currentPageId && treeLevel.children && treeLevel.children.length!=0 ? 'lastLi' : '', $store.getters.isAuthenticatedWithRole(0)? 'auth' : '']">    
    			<router-link  class="breadcrumb" v-bind:to="bread.link">{{ bread.name }}</router-link><span class="breadcrumb-separator"></span>
					<ul class="vertical-menu" @click="updateBreadcrumb" v-if="bread.id==currentPageId">
						
						<draggable v-model="treeLevel.children" :options="{draggable:'li', disabled:!$store.getters.isAuthenticatedWithRole(0)}" @end="setOrder(true)">
							<li v-for="leaf in treeLevel.children" :key="leaf.order" v-bind:class="{ deleteable: !leaf.children && leaf.elements==0 && $store.getters.isAuthenticatedWithRole(0) }">
					    			<router-link v-bind:to="basePath+leaf.slug">{{ leaf.name }}</router-link> <!--<i></i> --> <em v-if="!leaf.children && leaf.elements==0 && $store.getters.isAuthenticatedWithRole(0)" class="remove" @click="removeNode(leaf)"></em>
					    	</li>
						</draggable>

					</ul>
		    </li>
		</ul>

		<div class="flexWrap" v-if="$store.getters.isAuthenticatedWithRole(0)">
                <ui-icon-button tooltip="Crear Nodo" icon="create_new_folder" size="small" @click="$refs.insertNode.open()"></ui-icon-button>
                <ui-icon-button tooltip="Renombrar Nodo" icon="format_size" size="small" @click="$refs.renameNode.open()"></ui-icon-button>
        </div>
		
		<hr v-if="!$store.getters.isAuthenticatedWithRole(1)">
		
		<node-content :nodeId="currentPageId" :disableBlock="propDisable"></node-content>
		
	
		<ui-modal ref="insertNode" size="normal" v-bind:title="$t('node.insert_node')">
			<div class="ui-autocomplete__content" v-if="!newNode">
				<label class="ui-autocomplete__label">
					<input v-model="newNodeName.name" v-bind:placeholder="$t('node.write_node')" class="ui-autocomplete__input"> 
				</label> 
				<div class="ui-autocomplete__feedback">
					<div class="ui-autocomplete__feedback-text">{{ $t('node.remember_node') }}</div>
				</div>
				
				<div class="buttonGroupRight">
					<ui-button :loading="buttonLoading" size="small" @click="saveNode()">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
				
			</div>
        </ui-modal>

		<ui-modal ref="renameNode" size="normal" v-bind:title="$t('node.rename_node')">
			<div class="ui-autocomplete__content" v-if="!newNode">
				<label class="ui-autocomplete__label">
					<input v-model="currentNodeName.name" v-bind:placeholder="$t('node.write_node')" class="ui-autocomplete__input"> 
				</label> 
				<div class="ui-autocomplete__feedback">
					<div class="ui-autocomplete__feedback-text">{{ $t('node.remember_node') }}</div>
				</div>
				
				<div class="buttonGroupRight">
					<ui-button :loading="buttonLoading" size="small" @click="updateNode()">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
			</div>
        </ui-modal>
  

  </div>

</template>

<script>

import draggable from 'vuedraggable'
import Content from './Content.vue';
export default {
    components: {
		draggable, 'node-content':Content
    },
  	props: ['propDisable'],
    data () {
      return {
      	newOrder: [],
      	newNode: false,
      	buttonLoading: false,
      	currentNodeName: {
      		id:'',
      		name:'', 
      		idioma: this.$i18n.locale
      	},
      	newNodeName: {
      		parent_id: '',
      		name:''
      	},
      	currentPageId: '',
        newTree: {},
        refs: this.$refs,
        breadCrumb: {},
        treeLevel: {},
        basePath: this.$route.path+'/',
        tree: [],
      }
    },
    methods: {
	removeNode: function(node) {

		var vm=this;
		var root = this.$route.path.split('/')[2];
		
		vm.$http.delete('/node/'+root+'/'+ node.id)
        .then(function (response) {
        	
	        vm.tree = [response.data];
            vm.updateBreadcrumb();

        })
        .catch(function (error) {
            console.log(error);
        });
	        
	},
	setOrder: function(manual){
		var vm=this;
		
		vm.newOrder = [];
		
		if(vm.treeLevel.children) {
		    for (var node = 0; node < vm.treeLevel.children.length; node++){
					vm.newOrder.push( { 'id': vm.treeLevel.children[node].id, 'ordre' : node } );
		    }
		}
		if (manual) this.postOrder('node/ordre', vm.newOrder);
	},
	postOrder: function(apiUrl, order) {
        var vm = this;
        vm.$http.post(apiUrl, order)
        .then(function (response) {
			//res
        })
        .catch(function (error) {
            console.log(error);
        });
    },
    saveNode: function(){
    	var root = this.$route.path.split('/')[2];
    	this.postData('node/'+root, this.newNodeName, false);
    },
    updateNode: function(){
    	var vm=this;
    	//calculem el slug del pare a partir del route path
		var parent = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/');
		var root = this.$route.path.split('/')[2];
		parent.pop();
    	this.postData('node/'+root, this.currentNodeName, parent.join('/') );
    },
    postData: function(apiUrl, node, redirect) {
        var vm = this;
        vm.buttonLoading = true;
        vm.$http.post(apiUrl, node)
        .then(function (response) {
            vm.tree = [response.data];
            
            //si es false, no fa el redirect, soles fa update del breadcrumb
            if(!redirect) {
            	vm.updateBreadcrumb();
            }
            //else fa la redireccio al pare, ja que no tenim el nom "slug" que genera el backend
            else { 
            	vm.$router.push(redirect);
            	vm.updateBreadcrumb();
            }
            vm.buttonLoading = false;
            
            //tancar qualsevol modal
            vm.closeAllModals();

        })
        .catch(function (error) {
            console.log(error);
        });

    },
    getData: function(apiUrl) {

        var vm = this;
        vm.$http.get(apiUrl)
        .then(function (response) {
            vm.tree = [response.data];
            vm.updateBreadcrumb();
        })
        .catch(function (error) {
            console.log(error);
        });

    },
    closeAllModals: function() {
    	var vm=this;
	    for (var key in vm.$refs) {
        	vm.$refs[key].close();
        }
    },
    updateBreadcrumb: function() {
    	var vm=this;
    	this.basePath = this.$route.path+'/',
		this.breadCrumb = this.createBreadcrumb();
		var lastBread = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').slice(vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').indexOf(vm.tree[0].slug)).join('/');
		this.currentPageId = this.findNodeBySlug(this.tree, lastBread).id;
		this.newNodeName.parent_id = this.currentPageId;
		this.currentNodeName.id = this.currentPageId;
		this.currentNodeName.name = this.findNodeBySlug(this.tree, lastBread).name;
    },
	findNodeBySlug: function (tree, slug) {
		var vm=this;
	    for (var node = 0; node < tree.length; node++){
	    	if (tree[node].fullSlug === slug) {
	    		return tree[node];
	    	}
	    	if (tree[node].children) {
	    		if(vm.findNodeBySlug(tree[node].children, slug)) {
	    			return vm.findNodeBySlug(tree[node].children, slug);
	    		}else continue;
	    	} 
	    }
	    return false;
	},
	createBreadcrumb: function() {
      	var vm = this;
    	var incPath = '';
    	var breadCrumb = [];
    	//                          el route path, llevant el trailing slash, convertit a array, i fent slice dels elements a partir del nom del tree, que dependrà del idioma, naturalment
    	var tempBreadCrumb = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').slice(vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').indexOf(vm.tree[0].slug));
    	var baseUrl = vm.$route.path.replace(tempBreadCrumb.join('/'), '');

    	for (var bread in tempBreadCrumb) {
    		incPath = incPath+tempBreadCrumb[bread]+'/';
    		if(!vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1") )) vm.$router.push('/404'); //si el slug no esta en el tree, ens envia a 404
		    breadCrumb.push({'id':vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1") ).id, 'name': vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1") ).name,'slug': tempBreadCrumb[bread], 'link':baseUrl+incPath.replace(/^(.+?)\/*?$/, "$1")});
		}
		
		vm.treeLevel = vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1"));
		if(vm.treeLevel.children) vm.treeLevel.children.sort((a, b) => a.ordre - b.ordre); //ordenar per ordre
		vm.setOrder(false);
		
		return breadCrumb;
		
		
      }
    },
    watch: { 
      	slug: function(newVal, oldVal) { // watch it
          var root = this.$route.path.split('/')[2];
	      this.getData('node/'+root);
        }
    },
    created: function () {
    	var root = this.$route.path.split('/')[2];
    	this.getData('node/'+root);
    },
	mounted: function() {

	},
	computed: {
		slug:function(){
			return this.$route.path;
		}
	}
}

</script>

<style lang="less">

@import "../assets/less/defines.less";
	
.tree{padding: 1em 2em;}
.flexWrap {
	display:flex;
	flex-wrap: wrap;
	button {margin:10px;}
}
.icon-separator {
	width:20px;
}


.breadcrumb-wrapper {
  list-style: none;
  margin: 0;
  padding: 0;
  display:flex;
  flex-wrap: wrap;
  margin-bottom: 10px;
	.breadcrumb {
		color: #232323;
		border: 2px solid #87212e;
		padding: 4px 8px;
		font-family: arial;
		font-size: 14px;
		border-radius: 30px;
		text-transform: capitalize;
		cursor: pointer;

		&:hover {
			color: #fff;
			background-color: #87212e;
		}
	}

	.breadcrumb-separator {
    border-color: #87212e;
    border-style: solid;
    border-width: 2px 2px 0 0;
		font-size: 11px;
    margin: 0 8px 0 5px;
    width: 7px;
    height: 7px;
    display: inline-block;
    transform: rotate(41deg);
	}

  li { 
  	display: inline-block;
  	margin-bottom: 16px;
  	&.lastLi {
  		padding: 0 0 6px 5px;
	    //padding: 10px 7px 18px 7px;
	    margin: -8px 0 0 0;
	    border-radius: 20px;
	    &.auth{
	    	border: 1px dashed #87212e;
	    }
  	}
  }
  
  li:last-of-type{ span{display:none;}}
  a { text-decoration: none }
  

  
	.vertical-menu {
		height: 15px;
		display: inline-table;
		&>div{display:flex;flex-wrap: wrap;}
		list-style: none;
		margin: 0;
		padding: 0px 11px 0 11px;
			li{
	  		    margin: 5px 0 0 0;
			    display: list-item;
			    padding: 3px 16px 0 0px;
			    white-space: nowrap;
				height: 25px;
				
				a {
					color:#232323;
					text-decoration:none;
					text-transform: capitalize;
				}
				i {
				    cursor: n-resize;
				    width: 5px;
				    height: 5px;
				    display: inline-block;
				    margin-bottom: 2px;
				    margin-left: 10px;
				    border-top: 1px solid #87212e;
				    border-bottom: 1px solid #87212e;
				    float:right;
				    margin-top:8px;
				}
				
				em.remove {
					display:none;
					cursor: pointer;
				    width: 10px;
				    height: 10px;
				    margin-top: 5px;
				    position: absolute;
				    margin-left: 5px;
					&:hover {
					    &:before, &:after {	width: 2px; }
					}
				    &:before, &:after {
					    position: absolute;
					    left: 5px;
					    content: ' ';
					    height: 10px;
					    width: 1px;
					    background-color: white;
					}
					&:before {
					  transform: rotate(45deg);
					}
					&:after {
					  transform: rotate(-45deg);
					}
				}
				

				&:hover {
					&.deleteable {
						padding: 3px 25px 3px 10px;
					}
					a{color: #fff;}
				    background-color: #87212e;
				    padding: 3px 10px 3px 11px;
					margin-left: -10px;
					margin-right: 5px;
				    border-radius: 20px;

					em.remove {
							display:inline;
					}
				}
			}
	}
  
}


</style>