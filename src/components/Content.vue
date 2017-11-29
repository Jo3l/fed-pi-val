<template>
    <transition name="fade">
	    <div id="content">
		
		<button @click="auth">Autenticat</button>
		<!-- aço es public -->
		<div class="nodeContentEditable" v-if="show">
				<div v-bind:class="{ nodeContentElement:true, autenticated: show }" v-for="(element, key) in nodeContent">
					
					<img v-if="element.type == 'image'" :src="element.url">

					<article v-if="element.type == 'html'">
						<h2 v-if="nodeContent[key].title !== null">{{nodeContent[key].title}}</h2>
						<div v-html="nodeContent[key].content"></div>
					</article>

					<a v-if="element.type == 'file'" :href="element.url"><ui-icon icon="attach_file"></ui-icon><strong>{{element.title}}</strong></a>
					
				</div>

		</div>
		<!-- aço es admin -->
		<div class="nodeContentEditable" v-if="!show">
			<draggable v-model="nodeContent"  :options="{filter: '.ignore-elements',chosenClass:'floating'}" @end="setOrder()">
				<div class="nodeContentElement" v-for="(element, key) in nodeContent">
					
					<i class="remove ignore-elements" @click="removeContent(key)"></i>
					<i class="drag"></i>
					
					<img class="ignore-elements" v-if="element.type == 'image'" :src="element.url">
					
					<article v-if="element.type == 'html'">
						<input class="ignore-elements" contenteditable='true' v-model="nodeContent[key].title">
						    <VuePellEditor 
						    	class="ignore-elements"
						        :actions="editorOptions" 
						        :content="nodeContent[key].content" 
						        v-model="nodeContent[key].content"
						        :styleWithCss="false"
						    />
    
    
					</article>
					
					<div class="partida" v-if="element.type == 'partida'">
						 <h2>Nova Partida</h2>
						 <div class="form ignore-elements">
						 	
							 <label>Data
							 <vue-datepicker-local v-model="nodeContent[key].data" :local="datePickerOptions"></vue-datepicker-local>
							 </label>
							 
							 <label>Lloc
							 <input type="text" v-model="nodeContent[key].lloc">
							 </label>
							 
							 <label>Local
							 <input type="text" v-model="nodeContent[key].local">
							 </label>
							 
							 <label>Visitant
							 <input type="text" v-model="nodeContent[key].visitant">
							 </label>
							 
							 <label>Resultat visitant
							 <input type="text" v-model="nodeContent[key].resVisitant">
							 </label>
							 
							 <label>Resultat local
							 <input type="text" v-model="nodeContent[key].resLocal">
							 </label>
						</div>
					</div>
					
					<a class="ignore-elements" v-if="element.type == 'file'" :href="element.url"><ui-icon icon="attach_file"></ui-icon><strong>{{element.title}}</strong></a>
				</div>
				

			</draggable>
		</div>
		
		<div class="flexWrap" v-if="!show">
				<hr>
                <ui-icon-button @click="addContentHtml" tooltip="Insertar Contenido" size="small" icon="font_download" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentFile" tooltip="Insertar archivo" size="small" icon="file_upload" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentImage" tooltip="Insertar imagen" size="small" icon="photo" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentHtml" tooltip="Insertar Resultado" size="small" icon="assignment" type="secondary"></ui-icon-button>
        </div>
        <br><br><br><br><br>
			<pre>{{newOrder}}</pre>
			<pre>{{nodeContent}}</pre>
			
	    </div>
    </transition>
</template>

<script>

import draggable from 'vuedraggable'
import VuePellEditor from 'vue-pell-editor'
import VueFormGenerator from 'vue-form-generator/dist/vfg-core.js'
import VueDatepickerLocal from 'vue-datepicker-local'

export default {
  	components: { draggable, VuePellEditor, 'vue-form-generator': VueFormGenerator.component, VueDatepickerLocal },
  	props: ['nodeId'],
	data () {
		return {
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
                
                  const url = window.prompt('Enter the image URL')
                  if (url) VuePellEditor.components.pell.exec('insertImage', url)
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
			
			
			
			show:false,
			newOrder:[],
			customToolbar: [
			  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
			  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
			  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
			  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
			  [{ 'align': [] }],
			
			  ['clean']                                         // remove formatting button
			],
			nodeContent:[{
				type:'image',
				url:'/static/img/noticies/foto5961.jpg',
				id:1
			}, 
			{
				type:'html',
				title:'Esto es el titulo',
				content:'Esto es el <b>contenido</b>',
				id:2
			},
			{
				type:'file',
				title:'Esto es un archivo',
				url:'/static/img/noticies/foto5961.jpg',
				id:3
			},
			{
				type:'partida',
				data:'1111',
				lloc:'2222',
				local:'333',
				visitant:'444',
				resVisitant:'5555',
				resLocal:'666'
			}
			],
			
		}
	},
	methods: {
		removeContent: function(index) {
			this.nodeContent.splice( index, 1 );
		},
		setOrder: function(){
			var vm=this;
			
			vm.newOrder = [];
			
			if(vm.nodeContent) {
			    for (var node = 0; node < vm.nodeContent.length; node++){
						vm.newOrder.push( { 'id': vm.nodeContent[node].id, 'order' : node } );
			    }
			}
		},
		auth:function() {
			this.show = !this.show;
		}, 
		addContentImage: function() {
			this.nodeContent.push({ type:'image', url:'' });
		},
		addContentHtml: function() {
			this.nodeContent.push({ type:'html', title:'sdfsdfs', content:'<p>sdfsdfs</p>' });
		},
		addContentFile: function() {
			this.nodeContent.push({ type:'file', title:'', url:'' });
		},
		addContentPartida: function() {
			this.nodeContent.push({ type:'partida', title:'', url:'' });
		}
	},
	mounted: function () {
		this.setOrder();
	},
	watch: {
	
	}
}
</script>


<style lang="less">

.partida{
	width:100%;
	h2 {padding:0;margin:0!important;}
	.form {
		display:flex;
		flex-wrap:wrap;
	}
	label{
		max-width: ~"calc(50% - 20px)";
    	margin-right: 20px;
    	min-width: 220px;
    	width:100%;
	};
}

.datepicker {
    display: block!important;
}

.pell-content {
	height:initial!important;
	min-height:100px;
	border-bottom: 1px dashed #ccc;
}

.nodeContentEditable {

	h2 {
		font-size: 2em;
    	margin: 0.5em 0 0 0;
    	font-family: 'Rambla', cursive;
    	width: 100%;
    	display: block;
	}

	.nodeContentElement{
		padding: 10px;
	    margin-left: -10px;
	    position: relative;
	    display: flex;
	    align-items: center;
	    box-sizing: border-box;
	    margin: 10px 0;
	    border: 1px solid #87212e;
	    &.autenticated {
		margin:0;
		border:none;
		}
		&.floating {
		    border: 2px dashed #87212e;
		    opacity:0.7;
		    .drag{
				background-color:#87212e;
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
