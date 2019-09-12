<template>
    <transition name="fade">
	    <div id="fileManager">
		
			<div class="uploadContainer">
				
				<vue-core-image-upload v-if="tipo=='pdf'"
			    :text="$i18n.t('common.uploadPdf')"
			    class="uploader"
			    url="/api/static/uploadpdf"
				@imageuploaded="getData(uploadFolder)"
			    :data="{do:'uploadpdf'}"
			    extensions="pdf"
			    inputAccept	="application/pdf"
			    >
			  </vue-core-image-upload>
			  
			  <vue-core-image-upload v-else-if="imgCrop"
			    :text="$i18n.t('image.uploadAndCut')"
			    class="uploader"
				crop="local"
				:maxWidth="1188"
				cropRatio="2.26: 1"
				compress="50"
			    url="/api/static/uploadimg"
				@imageuploaded="getData(uploadFolder)"
			    :data="{do:'uploadimg'}"
			    extensions="jpg,jpeg"
			    inputAccept	="image/jpg,image/jpeg"
			    >
			  </vue-core-image-upload>
	
			  <vue-core-image-upload v-else
			    :text="$i18n.t('image.uploadImages')"
			    class="uploader"
			    :max-file-size="5242880"
			    :maxWidth="1188"
			    :multiple="true"
				:multiple-size="4"
				compress="50"
			    url="/api/static/uploadimg"
				@imageuploaded="getData(uploadFolder)"
			    :data="{do:'uploadimg'}"
			    extensions="jpg,jpeg"
			    inputAccept	="image/jpg,image/jpeg"
			    >
			  </vue-core-image-upload>
			  
			  <ui-switch v-if="tipo=='img'" v-model="imgCrop" color="fedpival" switch-position="left">{{$i18n.t('image.cutImages')}}</ui-switch>
			</div>
			
			<div style="text-align:center;" v-if="!enableFileManager">
				<br>
				<ui-button color="fedpival" raised icon="delete" size="small" @click="enableFileManager=true">{{$i18n.t('common.enableFileManager')}}</ui-button></em>
			</div>
			
			<hr v-if="enableFileManager">
			<span class="relativePath" v-if="enableFileManager">{{actualRelativePath}}</span>

			<div class="fileContainer" v-if="enableFileManager">
				<div v-bind:class="['fileMain', { selected: selected.selected }]">
					
					<div class="element" v-if="showBack">
					    <picture v-if="" @click="getData(lastRelativePath)">
							<i class="material-icons explorer">arrow_back</i>
						</picture>
						<span>..</span>
					</div>
					
					<div class="element" v-for="(element, key) in files.results" v-if="element.is_dir">
						<picture @click="getData(element.path)">
							<i class="material-icons explorer">folder</i>
						</picture>
						<span>{{element.name}}</span>
					</div>
		
					<div v-for="(element, key) in files.results" v-if="!element.is_dir&&/\.(jpg|jpeg|png|gif|svg)$/i.test(element.path)&&tipo=='img'" v-bind:class="['element', { selected: element.selected }]">
						<picture  class="mediaImage" @click="select(element)">
							<img :src="basePath+element.path">
						</picture>
						<span>{{element.name}}</span>
					</div>
					
					<div v-for="(element, key) in files.results" v-if="!element.is_dir&&/\.(pdf)$/i.test(element.path)&&tipo=='pdf'" v-bind:class="['element', { selected: element.selected }]">
						<picture @click="select(element)">
							<i class="material-icons explorer">description</i>
						</picture>
						<span>{{element.name}}</span>
					</div>
					
				</div>
				
				<aside v-if="selected.selected">
					<img :src="basePath+selected.path" v-if="tipo=='img'">
					<span>{{selected.name}}</span>
					<span>{{formatTimestamp(selected.mtime)}}</span>
					<span>{{formatFileSize(selected.size)}}</span>
					<em><ui-button color="black" raised icon="delete" size="small" @click="deleteFile(selected.path)">{{$i18n.t('common.delete')}}</ui-button></em>
					<hr>
				</aside>
				
				
			</div>
		
	    </div>
    </transition>
</template>

<script>

import VueCoreImageUpload  from 'vue-core-image-upload'

export default {
  	components: { 'vue-core-image-upload': VueCoreImageUpload},
  	props: {
  		pselected:{
	      type: Object,
	      default: function () {
	        return { url: '' }
	      }	
  		}
  	},
	data () {
		return {
			files:[],
			basePath:'/static/',
			showBack:false,
			lastRelativePath:'',
			actualRelativePath:'',
			uploadFolder: '',
			selected:{},
			imgCrop:true,
			tipo:'img',
			enableFileManager: false,
		}
	},
	methods: {
		activate:function(tipo){
			var vm=this;
			vm.tipo=tipo;
			
			if(vm.tipo=="pdf") {
				vm.uploadFolder = "pdf/" + (Date.today().getYear() + 1900) + "/" + ("0" + (Date.today().getMonth()+1)).slice(-2);
			} else {
				vm.uploadFolder = "upload/" + (Date.today().getYear() + 1900) + "/" + ("0" + (Date.today().getMonth()+1)).slice(-2);
			}

			vm.getData(vm.uploadFolder);
	
		},
		formatFileSize: function(bytes) {
			var s = ['bytes', 'KB','MB','GB','TB','PB','EB'];
			for(var pos = 0;bytes >= 1000; pos++,bytes /= 1024);
			var d = Math.round(bytes*10);
			return pos ? [parseInt(d/10),".",d%10," ",s[pos]].join('') : bytes + ' bytes';
		},
		formatTimestamp: function(unix_timestamp) {
			var m = this.$parent.$i18n.t('calendar.months');
			var d = new Date(unix_timestamp*1000);
			return [m[d.getMonth()],' ',d.getDate(),', ',d.getFullYear()," ",
				(d.getHours() % 12 || 12),":",(d.getMinutes() < 10 ? '0' : '')+d.getMinutes(),
				" ",d.getHours() >= 12 ? 'PM' : 'AM'].join('');
		},
		select: function(element) {
			this.selected = element;
			for(var i in this.files.results)  {
				if(this.files.results[i].name == element.name) {
					this.files.results[i].selected = true;
				} else {
					this.files.results[i].selected = false;
				}
			}
			this.pselected.url = this.basePath+element.path;

		},
		deleteFile: function(relativePath) {
	        var vm = this;
	        vm.selected={};
	        vm.$http.delete('static/'+relativePath)
	        .then(function (response) {
				vm.getData(vm.actualRelativePath);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getData: function(relativePath) {
	        var vm = this;
	        vm.files={};
	        vm.selected={};
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('static/'+relativePath, { cache: auth })
	        .then(function (response) {
				vm.files=response.data;
				if(relativePath!=null&&relativePath!='') vm.showBack = true;
				else vm.showBack = false;
				
                relativePath='/'+relativePath;
				vm.lastRelativePath = relativePath.replace(new RegExp("(.*\/)[^/]+$"),"$1").replace(/^\//, '').replace(/\/+$/, '');
				vm.actualRelativePath = relativePath.replace(/^\//, '').replace(/\/+$/, '');
	        })
	        .catch(function (error) {
	        	vm.files={};
	        	vm.actualRelativePath='';
	            console.log(error);
	        });
	        
	    },
	},
	mounted: function () {

	},
	watch: {

	}
}
</script>


<style lang="less">
@import "../assets/less/defines.less";

.ui-switch--color-fedpival.is-checked:not(.is-disabled) .ui-switch__thumb {
    background-color: @fedcolor;
}

.ui-switch--color-fedpival.is-checked:not(.is-disabled) .ui-switch__track {
    background-color: rgba(135, 33, 46,.5);
}

.g-core-image-corp-container .btn-upload {
    background: @fedcolor!important;
    border-color: #2e080d!important;
}
.g-core-image-corp-container .btn-upload:hover {
    background: #ac4955!important;
    border-color: #2e080d!important;
}

.uploadContainer {
	display:flex;
	margin:2%;
	justify-content: center;
    align-items: center;
    
	.uploader {
	    padding: 2% 19%;
	    text-align: center;
		margin-right:2%;
	    @media(max-width:@screenTablet) {

		}

	    cursor: pointer;
	    border: 2px dashed @fedcolor;
	    input{
	    	cursor:pointer;
	    }
	}
}


.relativePath {
	margin: 1% 2%;
    display: block;
}

.fileContainer{
	display:flex;
	max-height: 600px;
	@media(max-width:@screenMobile) {
		max-height: initial;
	}
	@media(max-width:@screenDesktop) {
		flex-direction: column-reverse;
	    justify-content: center;
	    align-items: center;
	}

	aside {
		width:25%;
		margin-right: 3%;
		border-radius: 4px;
		box-shadow: 2px 2px 4px rgba(0,0,0,0.5);
		height: 0%;
    	padding-bottom: 20px;
    	@media(max-width:@screenMobile) {
			padding-bottom: 80%;
		}
		@media(max-width:@screenDesktop) {
			width:75%;
			box-shadow:initial;
		}
		img{
			width:100%;
		}
		&>hr{
			display:none;
			@media(max-width:@screenDesktop) {
				display:block;
			}
		}
		&>span, em{
			text-align: center;
		    margin-top: 4px;
		    white-space: nowrap;
		    overflow: hidden;
		    text-overflow: ellipsis;
		    width: 90%;
		    margin-left: auto;
		    margin-right: auto;
		    display: block;
		}
		&>em{overflow:visible;margin-top:10px;display:block;}
		
	}
	.fileMain{
		width:100%;
		position:relative;
	    display: flex;
	    flex-wrap: wrap;
	    //justify-content: space-between;
	    height: inherit;
	    overflow-y: scroll;
	    margin-right: 25px;
	    @media(max-width:@screenMobile) {
			overflow-y: auto;
			margin-right: 0px;
			height: 100%;
		}
			
		&.selected {
			width:75%;
			@media(max-width:@screenDesktop) {
				width: 100%; 
			}
		}
	
		.element {
	
			height: 140px;
			box-sizing: border-box;
			margin-bottom: 18px;
	        width: ~"calc(1/6*100%)"; 
	        
	        @media(max-width:@screenLarge) {
				width: ~"calc(1/5*100%)"; 
			}
		    @media(max-width:@screenDesktop) {
				width: ~"calc(1/4*100%)"; 
			}
			@media(max-width:@screenTablet) {
				width: ~"calc(1/3*100%)"; 
			}
			@media(max-width:@screenMobile) {
				width: ~"calc(1/2*100%)"; 
			}
	    	position: relative;
	    	display: flex;
	    	justify-content: center;
	    	flex-direction:column;
	    	
	    	&.selected {
	    		picture {
	    			border: 3px solid #87212e;
	    			box-sizing: border-box;
	    		}
	    	}
	    	
	    	picture{
	    		width: 120px;
			    height: 100%;
			    overflow: hidden;
			    margin: 0 auto;
			    cursor:pointer;
			    display: flex;
	    	    justify-content: center;
	    	    align-items:center;
	    	    position:relative;
			    &.mediaImage{
				    border-radius: 4px;
				    box-shadow: 2px 2px 4px rgba(0,0,0,0.5);
	    		}
	    		em{
	    			position: absolute;
				    bottom: 0;
				    right: 0;
	    		}
	    		i{
				    color: @fedcolor;
				    font-size: 65px;
				    text-align: center;
				    width: 100%;
				    margin: 0;
				    padding: 0;
				    vertical-align: bottom;
				}
				img{
					height: 100%;
	    			width: 100%;
				    object-fit: cover;
				}
	    	}
	    	&>span{
	    		text-align: center;
			    margin-top: 4px;
			    white-space: nowrap;
			    overflow: hidden;
			    text-overflow: ellipsis;
			    width: 90%;
			    margin-left: auto;
			    margin-right: auto;
			    line-height: 27px;
	    	}
		}
	}
}

</style>