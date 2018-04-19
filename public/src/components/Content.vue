<template>

	    <div id="content">
		
		<!-- aço es public -->
		<div class="nodeContentEditable" v-if="!$store.getters.isAuthenticatedWithRole(1)">

				<div v-bind:class="{ nodeContentElement:true, autenticated: !$store.getters.isAuthenticatedWithRole(1) }" v-for="(element, key) in nodeContent">
					
					<img v-if="element.tipus == 'I'" :src="element.url" class="wide">

					<article v-if="element.tipus == 'H'">
						<h2 v-if="nodeContent[key].titol !== null">{{nodeContent[key].titol}}</h2>
						<div v-html="nodeContent[key].contingut"></div>
					</article>

					<a v-if="element.tipus == 'F'" :href="element.url"><ui-icon icon="attach_file"></ui-icon><strong>{{element.titol}}</strong></a>
					
					<div class="partida" v-if="element.tipus == 'J'">
					<table class="table results">
						<thead>
							<tr>
								<th>Data</th>
								<th>Lloc</th>
								<th>Local</th>
								<th>Visitant</th>
								<th>Res. Visitant</th>
								<th>Res. Local</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="element in nodeContent[key].partides" v-if="nodeContent[key].partides && nodeContent[key].partides.length > 0">
								<td>{{ element.data.toString('M/d/yyyy') }}</td>
								<td>{{element.lloc.nom}}</td>
								<td>{{element.local.nom}}</td>
								<td>{{element.visitant.nom}}</td>
								<td>{{element.resultatvisitant}}</td>
								<td>{{element.resultatlocal}}</td>
								<th>
									<ui-icon-button icon="edit" size="small" type="secondary" @click="editMatch(element)"></ui-icon-button>
									<ui-icon-button icon="delete" size="small" type="secondary" @click="deleteMatch(element)"></ui-icon-button>
								</th>
							</tr>
						</tbody>
					</table>
				</div>
					
					
				<!-- llistat -->
				<div class="buscador" v-if="listOn(element.tipus)">
					<pre>{{searchList}}</pre>
				</div>
				<!-- llistat -->
					
				</div>


		</div>
		<!-- aço es admin -->
		<div class="nodeContentEditable" v-if="$store.getters.isAuthenticatedWithRole(0)">
			<draggable v-model="nodeContent"  :options="{handle:'.drag',chosenClass:'floating'}" @end="setOrder()" preventOnFilter="true">
				<div class="nodeContentElement" v-for="(element, key) in nodeContent" v-if="nodeContent!=null">
					
					<!-- drag -->
					<i class="remove" @click="removeContent(element)"></i>
					<i class="drag"></i>
					<!-- drag -->
					
					<!-- imatge -->
					<img class="teaserImg wide" v-if="element.tipus == 'I' && element.url" :src="element.url">
					<div class="buttonContainer" v-if="element.tipus == 'I'">
	                	<ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', element, element.url, 'img')">{{$i18n.t('image.selectImage')}}</ui-button>
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- imatge -->
					
					<!-- Html -->
					<article v-if="element.tipus == 'H'">
						<input v-model="nodeContent[key].titol">
						    <VuePellEditor 
						        :actions="editorOptions" 
						        :content="nodeContent[key].contingut" 
						        v-model="nodeContent[key].contingut"
						        :styleWithCss="false"
						        placeholder="..."
						    />
					</article>
					<div class="buttonContainer" v-if="element.tipus == 'H'">
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- Html -->
					
					<!-- Partida -->
					<div class="partida" v-if="element.tipus == 'J'">
						<table class="table results">
							<thead>
								<tr>
									<th>Data</th>
									<th>Local</th>
									<th>Res. Local</th>
									<th>Res. Visitant</th>
									<th>Visitant</th>
									<th>Lloc</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="element in nodeContent[key].partides" v-if="nodeContent[key].partides && nodeContent[key].partides.length > 0">
									<td>{{ parseTime(element.data).toString('M/d/yyyy') }}</td>
									
									<td>{{element.local.nom}}</td>
									<td>{{element.resultatlocal}}</td>
									<td>{{element.resultatvisitant}}</td>
									<td>{{element.visitant.nom}}</td>
									<td>{{element.lloc.nom}}</td>
									<th>
										<ui-icon-button icon="edit" size="small" type="secondary" @click="editMatch(element)"></ui-icon-button>
										<ui-icon-button icon="delete" size="small" type="secondary" @click="deleteMatch(element)"></ui-icon-button>
									</th>
								</tr>
							</tbody>
						</table>
						<div class="form">
							 <h3>Nova Partida</h3>
							 <label>Data
							
							 <vue-datepicker-local v-model="newGame.data" :local="datePickerOptions" format="DD-MM-YYYY"></vue-datepicker-local>
							 </label>
							 
							<ui-select
				                has-search
				                floating-label
				                label="Lloc"
				                :keys="{ label: 'nom'}"
				                :options="places"
				                v-model="newGame.lloc"
				                error="This field is required"
				                :invalid="textbox_lloc && newGame.lloc.length === 0"
				                @touch="textbox_lloc = true"
				                searchPlaceholder=""
				            ></ui-select>
				            
							<ui-select
				                has-search
				                floating-label
				                label="Local"
				                :keys="{ label: 'nom'}"
				                :options="teams"
				                v-model="newGame.local"
				                error="This field is required"
				                :invalid="textbox_local && newGame.local.length === 0"
				                @touch="textbox_local = true"
				                searchPlaceholder=""
				            ></ui-select>
				            
				           <ui-select
				                has-search
				                floating-label
				                label="Visitant"
				                :keys="{ label: 'nom'}"
				                :options="teams"
				                v-model="newGame.visitant"
				                error="This field is required"
				                :invalid="textbox_visitant && newGame.visitant.length === 0"
				                @touch="textbox_visitant = true"
				                searchPlaceholder=""
				            ></ui-select>
				            
							 <ui-textbox
							    floating-label
				                autocomplete="off"
				                error="This field is required"
				                label="Resultat visitant"
				                :min="0"
								type="number"
				                v-model="newGame.resultatvisitant"
				            ></ui-textbox>
				            
							 <ui-textbox
							    floating-label
				                autocomplete="off"
				                error="This field is required"
				                label="Resultat local"
								type="number"
								:min="0"
				                v-model="newGame.resultatlocal"
				            ></ui-textbox>
							<div class="buttonContainer">
								<ui-button color="red" icon="save" size="small" type="secondary" @click="resetMatch()">Netejar</ui-button>
								<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveMatch(newGame)">{{$i18n.t('node.save_game')}}</ui-button>
							</div>

						</div>
					</div>
					<!-- Partida -->
					
					<!-- Arxiu -->
					<ui-textbox
							    floating-label
							    v-if="element.tipus == 'F'"
				                autocomplete="off"
				                error="This field is required"
				                label="Etiqueta"
								type="text"
				                v-model="element.titol"
				    ></ui-textbox>
					<span v-if="element.tipus == 'F'">{{element.url}}</span>
					<div class="buttonContainer" v-if="element.tipus == 'F'">
						<ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', element, element.url, 'pdf')">{{$i18n.t('common.uploadPdf')}}</ui-button>
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">Desar</ui-button>
					</div>
					<!-- Arxiu -->
					
					
					<!-- llistat -->
					<div class="buscador" v-if="listOn(element.tipus)">
						<pre>{{element.tipus}}</pre>
					</div>
					<!-- llistat -->
					
				</div>
				

			</draggable>
		</div>
		
		<div class="flexWrap" v-if="$store.getters.isAuthenticatedWithRole(0)">
				<hr>
                <ui-icon-button @click="addContentHtml" tooltip="Insertar Contenido" size="small" icon="font_download" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentFile" tooltip="Insertar archivo" size="small" icon="file_upload" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentImage" tooltip="Insertar imagen" size="small" icon="photo" type="secondary"></ui-icon-button>
                <ui-icon-button v-if="disable && !gameOn" @click="addContentPartida" tooltip="Insertar Resultado" size="small" icon="assignment" type="secondary"></ui-icon-button>
        </div>
        
        <ui-modal size="largeSquare" ref="uploadModal" title="Media Manager">
			<filemanager ref="upload" v-bind:pselected="selected"></filemanager>
			<div slot="footer">
                <ui-button @click="acceptModal('uploadModal')" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
                <ui-button @click="closeModal('uploadModal')">{{$i18n.t('modal.cancel')}}</ui-button>
            </div>
        </ui-modal>

<pre v-if="$store.getters.isAuthenticatedWithRole(0)">
	
	{{nodeContent}}
	
</pre>


	    </div>

</template>

<script>

import draggable from 'vuedraggable'
import VuePellEditor from 'vue-pell-editor'
import VueDatepickerLocal from 'vue-datepicker-local'
import FileManager from './FileManager.vue'

export default {
  	components: { draggable, VuePellEditor, VueDatepickerLocal, 'filemanager':FileManager },
  	props: ['nodeId', "disableBlock"],
	data () {
		return {
			selected:{},
			selectedCancel:'',
			places:[],
			teams:[],
			textbox_lloc: false,
			textbox_local: false,
			textbox_visitant: false,
			textbox_resultatvisitant: false,
			textbox_resultatlocal: false,
			datePickerOptions: {
				yearSuffix: '',
				monthsHead: this.$parent.$i18n.t('calendar.months'),
				dow: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
        		hourTip: this.$parent.$i18n.t('calendar.hourTip'),
    			minuteTip: this.$parent.$i18n.t('calendar.minuteTip'),
        		secondTip: this.$parent.$i18n.t('calendar.secondTip'),
        		months: this.$parent.$i18n.t('calendar.monthsShort'),
    			weeks: this.$parent.$i18n.t('calendar.weekShort')
			},
            editorOptions: [
              'bold',
              'underline',
              {
                name: 'italic',
                result: () => exec('italic')
              },
              {
                name: 'custom',
                icon: '<b><u><i>C</i></u></b>',
                title: 'Custom Action',
                result: () => console.log(this)
              },
              {
                name: 'image',
                result: () => {
                
                  this.openModal('uploadModal', {url:''}, '', 'img');
                  //VuePellEditor.components.pell.exec('insertImage', this.selected.url);
                }
              },
              {
                name: 'link',
                result: () => {
                  const url = window.prompt('Enter the link URL')
                  if (url) VuePellEditor.components.pell.exec('createLink', ensureHTTP(url))
                }
              }
            ],
			searchList:[],
			newOrder:[],
			customToolbar: [
			  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
			  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
			  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
			  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
			  [{ 'align': [] }],
			
			  ['clean']                                         // remove formatting button
			],
			nodeContent:[],
			newGame:{}
			
		}
	},
	methods: {
		parseTime: function(time) {
			
			var str = time;
			var year = str.substring(0, 4);
			var month = str.substring(4, 6);
			var day = str.substring(6, 8);
			var hour = str.substring(8, 10);
			var minute = str.substring(10, 12);
			var second = str.substring(12, 14);
			
			return new Date(year, month-1, day, hour, minute, second);	
			
		},
		addNewGame: function(content) {
			var vm=this;
	        for(var i = 0; i < content.length; i++) {

				if(content[i].tipus == 'J') {
					vm.newGame = { data:new Date(), lloc:'', local:'', visitant:'', resultatvisitant:'0', resultatlocal:'0', jerarquia:vm.nodeId, registreid: content[i].id};
				}
			}
		},
		delEmptyNodes:function(array) {
		
			var newArray = [];
			for(var i = 0; i < array.length; i++) {
				
					if(array[i].tipus) {
						newArray.push(array[i]);
					}
			}
			
			return newArray;
		},
		openModal:function(ref, object, cancel, tipo) {
			this.$refs.upload.activate(tipo);
			this.selectedCancel = cancel;
			this.selected=object;
            this.$refs[ref].open();
        },
        acceptModal:function(ref) {
        	console.log(this.selected.url);
        	VuePellEditor.components.pell.exec('insertImage', this.selected.url);
        	this.selected={};
            this.$refs[ref].close();
        },
        closeModal:function(ref) {
        	this.selected.url = this.selectedCancel;
        	this.selected={};
            this.$refs[ref].close();
        },
		saveContent: function(element) {
			console.log(element);
			var vm = this;
	        
	        vm.$http.post('/node/'+this.nodeId, element)
	        .then(function (response) {
	            
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
			
			
		},
		editMatch: function(element) {
			console.log(element);

		},
		deleteMatch: function(element) {
			var vm = this;

		//aço es tonteria fer-ho aci, pero mentres...
		/*
			for(var i = 0; i < vm.nodeContent.length; i++) {
			    if(vm.nodeContent[i].id == element.blocId) {
			    	for(var o = 0; o < vm.nodeContent[i].partides.length; o++) {
			    		if(vm.nodeContent[i].partides[o].id == element.id) {
					        vm.nodeContent[i].partides.splice(o, 1);
					        break;
			    		}
			    	}
			    }
			}
		*/
		
		},
		getNode: function(){
			
	        var vm = this;
	        
	    	vm.$http.get('/node/'+vm.nodeId)
	        .then(function (response) {

	            vm.nodeContent = vm.delEmptyNodes(response.data);
	            vm.addNewGame(vm.nodeContent);
				
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getPlaces: function(){
	        var vm = this;
	        vm.$http.get('trinquet')
	        .then(function (response) {
	            vm.places = response.data;
	            vm.places.unshift({id:null, nom:'---'});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getTeams: function(){
	        var vm = this;
	        vm.$http.get('equip')
	        .then(function (response) {
	            vm.teams = response.data.data;

	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getList: function(listName) {
	        var vm = this;
	        //var url = objSearch.valor ? listName+'/search/'+objSearch.camp+'/'+objSearch.valor : listName;
	        
	        vm.$http.get(listName)
	        .then(function (response) {
	            vm.searchList = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		resetMatch: function() {
			var vm = this;
			
			vm.addNewGame(vm.nodeContent);

			vm.textbox_lloc=false;
			vm.textbox_local=false;
			vm.textbox_visitant=false;
			vm.textbox_resultatvisitant=false;
			vm.textbox_resultatlocal=false;
			
		},
		saveBlock: function(block){
			
			var vm = this;

	        vm.$http.post('/node/'+vm.nodeId, block)
	        .then(function (response) {
	        	
	            vm.nodeContent = vm.delEmptyNodes(response.data);
	            vm.addNewGame(vm.nodeContent);
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		saveMatch: function(gameBlock){
			var vm = this;
			
			gameBlock.data = gameBlock.data.toString('yyyyMMddHHmmss');
			
	        for(var i = 0; i < vm.nodeContent.length; i++) {
				if(vm.nodeContent[i].tipus == 'J') {
					
					//vm.nodeContent[i].partides.push(gameBlock);
					
			        vm.$http.post('/partida', gameBlock)
			        .then(function (response) {
			            vm.nodeContent = vm.delEmptyNodes(response.data);
						vm.addNewGame(vm.nodeContent);
			        })
			        .catch(function (error) {
			            console.log(error);
			        });
				}
			}
			vm.resetMatch(gameBlock);
		},
		removeContent: function(element) {

			var vm=this;

			vm.$http.delete('/node/'+vm.nodeId+'/element/'+element.id)
	        .then(function (response) {
	            vm.nodeContent = vm.delEmptyNodes(response.data);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	        
		},
		setOrder: function(initial){
			var vm=this;
			
			vm.newOrder = [];
			
			if(vm.nodeContent) {
			    for (var node = 0; node < vm.nodeContent.length; node++){
						vm.newOrder.push( { 'id': vm.nodeContent[node].id, 'ordre' : node } );
			    }
			}
			
			if(!initial) {
				vm.$http.post('/node/'+vm.nodeId+'/ordre', vm.newOrder)
		        .then(function (response) {
		        	
		            vm.nodeContent = vm.delEmptyNodes(response.data);
		
		        })
		        .catch(function (error) {
		            console.log(error);
		        });
			}
			
		},
		auth:function() {
			return this.$store.getters.isAuthenticated && this.$store.getters.role>=10;
		}, 
		addContentImage: function() {
			this.saveBlock({ tipus:'I', url:'' });
		},
		addContentHtml: function() {
			this.saveBlock({ tipus:'H', titol:'', contingut:'' });
		},
		addContentFile: function() {
			this.saveBlock({ tipus:'F', titol:'', url:'' });
		},
		addContentPartida: function() {
			//this.saveBlock({ tipus:'J', partides: [], newGame:{ data:new Date(), lloc:'', local:'',	visitant:'', resultatvisitant:'', resultatlocal:'', blocId:''} });
			this.saveBlock({ tipus:'J', partides: []});
		},
	    listOn: function(nom) {
	    	var vm = this;
	    	
	    	var tipos = ['jugadors','equips','clubs'];
	    	
	    	if(!tipos.includes(nom)) return false;
	    	
	      	for(var i = 0; i < vm.nodeContent.length; i++) {
				if(vm.nodeContent[i].tipus==nom) {

					if(vm.searchList.length == 0) {
						
						vm.getList(vm.nodeContent[i].tipus);
						
					}
					return true;
					
				}

			}
			return false;
	    }
		
	},
	mounted: function () {
    	if(this.$store.getters.isAuthenticatedWithRole(0)) {
			this.getTeams();
			this.getPlaces();
    	}
		
	},
	watch: { 
      	nodeId: function(newVal, oldVal) {
          this.getNode();
          this.setOrder(true);
        },
        authOn: function(){
        	if(this.$store.getters.isAuthenticatedWithRole(0)) {
				this.getTeams();
				this.getPlaces();
        	}

        }
	},
	 computed: {
	 	disable: function() {
	 		var root = this.$route.path.split('/')[2];
	 		var vm=this;
	 		return root == vm.disableBlock;
	 	},
	 	authOn: function() {
	 		return this.$store.getters.isAuthenticatedWithRole(0);
	 	},
	    gameOn: function () {
	    	var vm = this;
	      	for(var i = 0; i < vm.nodeContent.length; i++) {
				if(vm.nodeContent[i].tipus=="J") {
					return true;
				}

			}
			return false;
	    }
	    
	}
}
</script>


<style lang="less">

@import "../assets/less/defines.less";

.ui-button--type-primary.ui-button--color-fedpival {
    background-color: @fedcolor;
    color:white;
}

.ui-modal--size-largeSquare {
	.ui-modal__container{
    	width: 90%;
        @media(max-width:@screenTablet) {
    		height: 100%;
    		width: 100%;
		}
	}
}
.ui-select__display {
    border-bottom-color: rgb(204, 204, 204);
    border-bottom-style: dashed;
}

.buttonContainer {
	text-align: right;
	display: block;
    margin-left: auto;
    margin-right: 0;
    margin-top:10px;
}
.ui-button--color-blueButtonToRight {
	color:#87212e;
    margin-left: auto;
    display: block;
    margin-top: 10px;
}

.ui-button--color-customBlueRight{
	background-color:#117edd;
	color:white;
	display: block;
    margin-left: auto;
    margin-right: 0;
}

.ui-select{margin-top:10px;}

.is-invalid {
	.ui-textbox__label-text {
		color:#f44336!important;
	}
}

.is-active{
	.ui-textbox__label-text {
    	color: #117edd!important;
	}
	.ui-textbox__input {
		border-bottom-color: #117edd!important;
	}
}

.partida{
	width:100%;
	h2 {padding:0;margin:0!important;}
	

	.form {
		display:flex;
		flex-wrap:wrap;
		&>label {
			max-width: ~"calc(50% - 20px)";
	    	margin-right: 20px;
	    	min-width: 220px;
	    	width:100%;
	    	color: rgba(0,0,0,.54);
	    	@media(max-width:@screenTablet) {
	    		max-width:initial;
			}
		}
		h3{display:block;width:100%;}
	}
	.ui-textbox, .ui-autocomplete, .ui-select{
		max-width: ~"calc(50% - 20px)";
    	margin-right: 20px;
    	min-width: 220px;
    	width:100%;
    	
    	@media(max-width:@screenTablet) {
    		max-width:initial;
		}
    	
	};
	table {
		td, th {
		    padding: 0;
		    text-align:center;
		    @media(max-width:@screenTablet) {
	    		font-size:0.8em;
			}
		}


	}
}

.datepicker {
    display: block!important;
    margin-top: -3px;
        input {
        	color:#232323!important;
			width: 100%!important;
		    font-family: 'Rambla', cursive!important;
		    display: block!important;
		    font-size: 2em!important;
		    border: none!important;
		    border-bottom: 1px dashed #ccc!important;
		    margin-bottom:0.5em!important;
		    min-height: 42px!important;
		}
}

.pell-content {
	height:initial!important;
	min-height:100px;
	border-bottom: 1px dashed #ccc;
	img{
		max-width:100%;
	}
}

.nodeContentEditable, .eventSelected, .news {

	h2 {
		font-size: 2em;
    	margin: 0.5em 0 0 0;
    	font-family: 'Rambla', cursive;
    	width: 100%;
    	display: block;
	}
	
	h4 {
		font-size: 1em;
    	margin: 0.5em 0 0 0;
    	font-family: 'Rambla', cursive;
    	width: 100%;
    	display: block;
	}

	.nodeContentElement{
		padding: 10px;
	    margin-left: -10px;
	    position: relative;
	    align-items: center;
	    box-sizing: border-box;
	    margin: 10px 0;
	    border: 1px solid @fedcolor;
	    &.autenticated {
		margin:20px 0;
		border:none;
		padding:0;
		}
		&.floating {
		    border: 2px dashed #87212e;
		    opacity:0.7;
		    .drag{
				background-color:#87212e;
			}
		}
		
		.teaserImg, &>img {
		    max-width: 100%;
		    max-height: 480px;
		    margin: 0 auto 20px auto;

		    &.wide {
		    	margin:0;
		    	max-width:initial;
		    	object-fit: cover;
			    max-height: 480px;
			    width: 100%;
			    object-position: 50% 50%;
		    }
		    
		}
		
		.ql-editor {
			font-family: 'Yantramanav', cursive;
	    	font-size: 1.4em;
		}
		.wysiwyg {
		    margin: 0px;
		}
		#quill-container {
	    	height: initial;
		}
		.ql-container.ql-snow {
	    	border: none;
		}
		article {
			width:100%;
		}
		textarea, input {
		    width: 100%;
		    font-family: 'Rambla', cursive;
		    display: block;
		    font-size: 2em;
		    border: none;
		    border-bottom: 1px dashed #ccc;
		    margin-bottom:0.5em;
		    min-height: 42px;
		}
		div#quill-container {
		    border: 1px dashed #cccccc;
		    border-top: none;
		}
		picture {
			width: 100%;
		    display: block;
		    margin: 0 auto;
		    position: relative;
		    overflow: hidden;
		    height: 100vw;
		    max-height: 480px;
		    background-size: cover;
		    background-position: 50% 33%;
		    margin-bottom: 20px;
		    transition: max-height 1s ease;
		    cursor:pointer;
		}
		
	}
	
	.drag {
	    cursor: n-resize;
	    width: 10px;
	    height: 10px;
	    margin-right: 10px;
	    margin-left: -20px;
	    border: 1px solid #87212e;
	    position:absolute;
	    top: -1px;
	    &:hover{
	    	border: 2px solid #87212e;
	    }
	}
	.remove {
		cursor: pointer;
	    width: 10px;
	    height: 10px;
	    margin-right: 10px;
	    margin-left: -40px;
	    position: absolute;
	    top: -1px;
		&:hover {
		    &:before, &:after {	width: 2px; }
		}
	    &:before, &:after {
		    position: absolute;
		    left: 5px;
		    content: ' ';
		    height: 10px;
		    width: 1px;
		    background-color: #87212e;
		}
		&:before {
		  transform: rotate(45deg);
		}
		&:after {
		  transform: rotate(-45deg);
		}
	}
}




</style>
