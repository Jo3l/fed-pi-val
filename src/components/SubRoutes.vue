<template>
  <div class="tree">

		<ul class="breadcrumb-wrapper" v-if="!selectYear">
			<li v-for="bread in breadCrumb" @click="updateBreadcrumb">    
    			<router-link  class="breadcrumb" v-bind:to="bread.link">{{ bread.name }}</router-link><span class="breadcrumb-separator"></span>
		    </li>
		</ul>

		<ul class="vertical-menu" v-if="!selectYear" @click="updateBreadcrumb">
			<li v-if="treeLevel.children" v-for="leaf in treeLevel.children">
	    			<router-link v-bind:to="basePath+leaf.slug">{{ leaf.name }}</router-link>
	    	</li>
		</ul>
		
		<div class="flexWrap">
                <ui-fab icon="add" size="small" @click="$refs.insertNode.open()"></ui-fab>
                <ui-fab icon="edit" size="small" @click="$refs.renameNode.open()"></ui-fab>
                <ui-fab icon="directions_walk" size="small" @click="$refs.insertGame.open()"></ui-fab>
        </div>
		
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
					<input v-model="newNodeName" v-bind:placeholder="$t('node.write_node')" class="ui-autocomplete__input"> 
				</label> 
				<div class="ui-autocomplete__feedback">
					<div class="ui-autocomplete__feedback-text">{{ $t('node.remember_node') }}</div>
				</div>
				<br>
			</div>
        </ui-modal>
  
  		<ui-modal ref="insertGame" size="normal" v-bind:title="$t('node.insert_game')">
			<div class="ui-autocomplete__content" v-if="!newNode">
				<label class="ui-autocomplete__label">
					<input v-model="newNodeName" v-bind:placeholder="$t('node.write_node')" class="ui-autocomplete__input"> 
				</label> 
				<div class="ui-autocomplete__feedback">
					<div class="ui-autocomplete__feedback-text">{{ $t('node.remember_node') }}</div>
				</div>
				<br>
			</div>
        </ui-modal>      
        
	<pre>
		
		{{newNodeName}}
		{{currentPageId}}
		
	</pre>
		
		

  </div>
</template>

<script>

export default {
    components: {

    },
  	props: {
        selectYear: {
            type: Boolean,
            default: false
        }
	},
    data () {
      return {
      	newNode: false,
      	buttonLoading: false,
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
    saveNode: function(){
    	this.postData('competicio', this.newNodeName);
    },
    postData: function(apiUrl, node) {
        var vm = this;
        vm.buttonLoading = true;
        vm.$http.post(apiUrl, node)
        .then(function (response) {
            vm.tree = [response.data];
            vm.updateBreadcrumb();
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
		var lastBread = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').slice(vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').indexOf(vm.tree[0].name)).join('/');
		this.currentPageId = this.findNodeBySlug(this.tree, lastBread).id;
		this.newNodeName.parent_id = this.currentPageId;
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
    	var tempBreadCrumb = vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').slice(vm.$route.path.replace(/^(.+?)\/*?$/, "$1").split('/').indexOf(vm.tree[0].name));
    	var baseUrl = vm.$route.path.replace(tempBreadCrumb.join('/'), '');

    	for (var bread in tempBreadCrumb) {
    		incPath = incPath+tempBreadCrumb[bread]+'/';
    		if(!vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1") )) vm.$router.push('/404'); //si el slug no esta en el tree, ens envia a 404
		    breadCrumb.push({'name': vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1") ).name,'slug': tempBreadCrumb[bread], 'link':baseUrl+incPath.replace(/^(.+?)\/*?$/, "$1")});
		}
		
		vm.treeLevel = vm.findNodeBySlug(vm.tree, incPath.replace(/^(.+?)\/*?$/, "$1"));
		
		return breadCrumb;
		
		
      }
    },    
    created: function () {
    	this.getData('competicio/i/'+this.$i18n.locale);
    },
	beforeRouteUpdate (to, from, next) {
		next();
	}
}

</script>

<style lang="less">

@import "../assets/less/defines.less";
	
.tree{padding: 2em 0 1em 2em;}
.flexWrap {
	display:flex;
	button {margin:10px;}
}
.vertical-menu {
	list-style: disc;
	margin: 20px 0;
	
		li{
	  		margin:10px 0;
			a {
				color:#232323;
				text-decoration:none;
				&:hover {
					color: #fff;
				    background-color: #87212e;
				    padding: 3px 9px;
				    border-radius: 20px;
				}
			}
		}
}

.breadcrumb-wrapper {
  list-style: none;
  margin: 0;
  padding: 0;
  
	.breadcrumb {
		color: #232323;
		border: 2px solid #87212e;
		padding: 4px 8px;
		font-family: arial;
		font-size: 11px;
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
  
  li { display: inline-block }
  li:last-of-type{ span{display:none;}}
  a { text-decoration: none }
}


</style>