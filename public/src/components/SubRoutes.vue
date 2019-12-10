<template>

  <div class="tree">
		<ul class="breadcrumb-wrapper">
			<li v-for="bread in breadCrumb" @click="updateBreadcrumb" v-bind:class="[ bread.id==currentPageId && treeLevel.children && treeLevel.children.length!=0 ? 'lastLi' : '', $store.getters.isAuthenticatedWithRole(0)? 'auth' : '']">    
    			<router-link  class="breadcrumb" v-bind:to="bread.link">{{ bread.name }}</router-link><span class="breadcrumb-separator"></span>
					<div class="vertical-menu" @click="updateBreadcrumb" v-if="bread.id==currentPageId">
						
						<draggable v-model="treeLevel.children" :options="{draggable:'em', disabled:!$store.getters.isAuthenticatedWithRole(0)}" @end="setOrder(true)">
							<em v-for="leaf in treeLevel.children" :key="leaf.order" v-bind:class="{ deleteable: !leaf.children && leaf.elements==0 && $store.getters.isAuthenticatedWithRole(0) }">
					    			<router-link v-bind:to="basePath+leaf.slug">{{ leaf.name }}</router-link> <!--<i></i> --> <i v-if="!leaf.children && leaf.elements==0 && $store.getters.isAuthenticatedWithRole(0)" class="remove" @click="removeNode(leaf)"></i>
					    	</em>
						</draggable>

					</div>
		    </li>
		</ul>

		<div class="flexWrap" v-if="$store.getters.isAuthenticatedWithRole(0)">
                <ui-icon-button tooltip="Crear Node" icon="create_new_folder" size="small" @click="$refs.insertNode.open()"></ui-icon-button>
                <ui-icon-button tooltip="Modificar Node" icon="format_size" size="small" @click="$refs.renameNode.open()"></ui-icon-button>
                <ui-icon-button tooltip="Crear Node Competició" icon="add_alarm" size="small" @click="$refs.insertNodeCompeticio.open()"></ui-icon-button>
        </div>
		
		<hr v-if="!$store.getters.isAuthenticatedWithRole(0)">
		
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
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            error=""
					            label="Nom de la Competició"
								type="text"
					            v-model="currentNodeName.name"
					></ui-textbox>
				</label> 
				
				
				<div class="input2flex" v-if="currentNodeName.compNode">
						<ui-datepicker
			                :placeholder="$i18n.t('calendar.dateTip')"
			                :start-of-week="datePickerOptions.dow"
			                v-model="modInici"
			                :lang="datePickerOptions"
			            >Data Inici Inscripció:</ui-datepicker>
			
						<ui-datepicker
			                :placeholder="$i18n.t('calendar.dateTip')"
			                :start-of-week="datePickerOptions.dow"
			                v-model="modFinal"
			                :lang="datePickerOptions"
			            >Data Final Inscripció:</ui-datepicker>
				</div>
				
				<div class="input3flex" v-if="currentNodeName.compNode">
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            error=""
					            label="Mínim Jugadors"
								type="number"
					            v-model="currentNodeName.minimjugadors"
					></ui-textbox>
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            error=""
					            label="Punts Tanteig"
								type="number"
					            v-model="currentNodeName.puntstanteig"
					></ui-textbox>
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            error=""
					            label="Punts Partida"
								type="number"
					            v-model="currentNodeName.puntspartida"
					></ui-textbox>
				</div>
					
				<div class="buttonGroupRight">
					<ui-button :loading="buttonLoading" size="small" @click="updateNode()">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
			</div>
        </ui-modal>
  
  		<ui-modal ref="insertNodeCompeticio" size="normal" title="Insertar Node Competició">
			<div class="ui-autocomplete__content" v-if="!newNode">
				<label class="ui-autocomplete__label">
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            error=""
					            label="Nom de la Competició"
								type="text"
					            v-model="newNodeName.name"
					></ui-textbox>
					

					<div class="input2flex">
							<ui-datepicker
				                :placeholder="$i18n.t('calendar.dateTip')"
				                :start-of-week="datePickerOptions.dow"
				                v-model="insInici"
				                :lang="datePickerOptions"
				            >Data Inici Inscripció:</ui-datepicker>
				
							<ui-datepicker
				                :placeholder="$i18n.t('calendar.dateTip')"
				                :start-of-week="datePickerOptions.dow"
				                v-model="insFinal"
				                :lang="datePickerOptions"
				            >Data Final Inscripció:</ui-datepicker>
					</div>
				
				
					<div class="input3flex">
						<ui-textbox
								    floating-label
						            autocomplete="off"
						            error=""
						            label="Mínim Jugadors"
									type="number"
						            v-model="newNodeName.minimjugadors"
						></ui-textbox>
						<ui-textbox
								    floating-label
						            autocomplete="off"
						            error=""
						            label="Punts Tanteig"
									type="number"
						            v-model="newNodeName.puntstanteig"
						></ui-textbox>
						<ui-textbox
								    floating-label
						            autocomplete="off"
						            error=""
						            label="Punts Partida"
									type="number"
						            v-model="newNodeName.puntspartida"
						></ui-textbox>
					</div>
					
				</label> 
				
				<div class="buttonGroupRight">
					<ui-button :loading="buttonLoading" size="small" @click="saveNode('competicio')">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
				
			</div>
        </ui-modal>
        
  </div>

</template>

<script>

import draggable from 'vuedraggable'
import Content from './Content.vue';
import DoubleDatepicker from './admin/DoubleDatepicker.vue'
export default {
    components: {
		draggable, 'node-content':Content, DoubleDatepicker
    },
  	props: ['propDisable'],
	head : function() {
		return {...this.$route.meta,
	    	...{
		    	title: {
				  inner: this.breadCrumb.map(function(e){return e.name}).join(" -> ")
				},
		    }
		}    
	},
    data () {
      return {
      	newOrder: [],
      	newNode: false,
      	buttonLoading: false,
      	insInici:new Date(),
      	insFinal:new Date(),
      	modInici:new Date(),
      	modFinal:new Date(),
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
        breadCrumb: [],
        treeLevel: {},
        basePath: this.$route.path+'/',
        tree: [],
	    datePickerOptions: {
		  dow: Number(eval(this.$parent.$i18n.t('calendar.mondayFirst'))),
		  months: {
		    full: this.$parent.$i18n.t('calendar.months'),
		    abbreviated: this.$parent.$i18n.t('calendar.monthsShort')
		  },
		  days: {
		    full: this.$parent.$i18n.t('calendar.weekLong'),
		    abbreviated: this.$parent.$i18n.t('calendar.weekShort'),
		    initials: this.$parent.$i18n.t('calendar.weekInitials')
		  }
		},
      }
    },
    methods: {
    fixDateForParse: function (date) { 
		  if(typeof date === 'string' || date instanceof String) {
		  	return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) );
		  } else {
		  	return date;
		  }
	},
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
    saveNode: function(tipus){
    	var vm=this;
    	var root = this.$route.path.split('/')[2];
    	if(tipus=='competicio'){
    		vm.postData('node/'+root, vm.prepareCompNode(vm.newNodeName), false);
    	} else {
    		vm.postData('node/'+root, vm.newNodeName, false);
    	}
    },
    prepareCompNode: function(node){
    	var obj = JSON.parse(JSON.stringify(node));
    	obj.inici=this.insInici.toString('yyyyMMddHHmmss');
    	obj.fi=this.insFinal.toString('yyyyMMddHHmmss');
    	return obj;
    },
    prepareModCompNode: function(node){
    	var obj = JSON.parse(JSON.stringify(node));
    	obj.inici=this.modInici.toString('yyyyMMddHHmmss');
    	obj.fi=this.modFinal.toString('yyyyMMddHHmmss');
    	return obj;
    },
    updateNode: function(){
    	var vm=this;
    	//calculem el slug del pare a partir del route path
		var parent = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/');
		var root = vm.$route.path.split('/')[2];
		parent.pop();
    	vm.postData('node/'+root, vm.prepareModCompNode(vm.currentNodeName), parent.join('/') );
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

			//reset variables de node
			vm.insInici=new Date();
	      	vm.insFinal=new Date();
	      	vm.modInici=new Date();
	      	vm.modFinal=new Date();
	      	vm.currentNodeName.inici='';
	      	vm.currentNodeName.fi='';
	      	vm.currentNodeName.minimjugadors=0;
	      	vm.currentNodeName.puntstanteig=0;
	      	vm.currentNodeName.puntspartida=0;
        })
        .catch(function (error) {
            console.log(error);
        });

    },
    getData: function(apiUrl) {

        var vm = this;
        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
        vm.$http.get(apiUrl, { cache: auth })
        .then(function (response) {
            vm.tree = [response.data];
            vm.updateBreadcrumb();
            
            vm.$emit('updateHead')
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
    	vm.basePath = vm.$route.path+'/',
		vm.breadCrumb = vm.createBreadcrumb();
		var lastBread = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').slice(vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').indexOf(vm.tree[0].slug)).join('/');
		vm.currentPageId = vm.findNodeBySlug(vm.tree, lastBread).id;
		vm.newNodeName.parent_id = vm.currentPageId;
		vm.currentNodeName.id = vm.currentPageId;
		vm.currentNodeName.name = vm.findNodeBySlug(vm.tree, lastBread).name;
		if(vm.findNodeBySlug(vm.tree, lastBread).inici&&vm.findNodeBySlug(vm.tree, lastBread).fi) {
	      	vm.modInici=vm.fixDateForParse(vm.findNodeBySlug(vm.tree, lastBread).inici);
	      	vm.modFinal=vm.fixDateForParse(vm.findNodeBySlug(vm.tree, lastBread).fi);
	    	vm.currentNodeName.minimjugadors= vm.findNodeBySlug(vm.tree, lastBread).minimjugadors;
	      	vm.currentNodeName.puntstanteig= vm.findNodeBySlug(vm.tree, lastBread).puntstanteig;
	      	vm.currentNodeName.puntspartida= vm.findNodeBySlug(vm.tree, lastBread).puntspartida;
	      	vm.currentNodeName.compNode=true;
		} else {
			vm.currentNodeName.compNode=false;
		}
		
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
	
.tree{
	padding: 1em 2em;
	.datepicker-popup {
	    position: fixed;
	}
	.input2flex{
		display:flex;
		justify-content:space-between;
		margin-top:20px;
		&>div{width:43%;}
	}
	.input3flex{
		display:flex;
		justify-content:space-between;
		&>div{width:30%;}
	}
}
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
  margin-bottom: -40px;
  @media(max-width:480px){
  	line-height:2em;
  }
	.breadcrumb {
		color: #232323;
		border: 2px solid #87212e;
		padding: 4px 8px;
		font-family: arial;
		font-size: 14px;
		border-radius: 30px;
		text-transform: capitalize;
		cursor: pointer;
		@media print {    
	    	border:0;
		}
		
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
	    @media print {    
	    	display:none;
		}
	}

  li { 
  	display: inline-block;
  	//margin-bottom: 16px;
  	min-height: 35px;
  	&.lastLi {
  		padding: 0;
	    //padding: 10px 7px 18px 7px;
	    margin: 0 0 20px 0;
	    border-radius: 20px;
	    &.auth{
	    	//border: 1px dashed #87212e;
	    }
  	}
  }
  
  li:last-of-type{ span{display:none;}}
  a { text-decoration: none }
  

  
	.vertical-menu {
		height: 15px;
		display: initial;
		@media print {    
	    	display:none;
		}
		&>div{
			//display:flex;
			//flex-wrap: wrap;
			column-count:2;
			column-gap: 40px;
			margin-top:10px;
			margin-left: 10px;
			@media(max-width:@screenTablet) {
				column-count:1;
				width: ~"calc(100vw - 85px)";
			}
			@media(max-width:480px){
				font-size: 80%;
			}
		}

		margin: 0;
		padding: 0px 11px 0 11px;
			em{
	  		    margin: 0;
			    display: block;
			    padding: 3px 10px 3px 11px;
				font-style: initial;
				margin-left: -10px;
				border-radius: 20px;
				position:relative;
				page-break-inside: avoid;
                break-inside: avoid;
				
				a {
					color:#232323;
					text-decoration:none;
					text-transform: capitalize;
					white-space: nowrap; 
				    overflow: hidden;
				    text-overflow: ellipsis;
				    display:block;
				}
				i {
				    cursor: n-resize;
				    width: 5px;
				    height: 5px;
				    display: inline-block;
				    border-top: 1px solid #87212e;
				    border-bottom: 1px solid #87212e;
				}
				
				i.remove {
					display:none;
					cursor: pointer;
				    width: 10px;
				    height: 10px;
				    position: absolute;
				    right: 8px;
    				top: 8px;
    				
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
						//padding: 3px 25px 3px 10px;
						padding-right: 22px;
						margin-right:-5px;
					}
					a{color: #fff;}
				    background-color: #87212e;

					.remove {
							display:inline;
					}
				}
			}
	}
  
}

</style>