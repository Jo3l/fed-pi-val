<template>
    <transition name="fade">
    <div id="news">
	
	<div class="news" v-if="news">
		
		<aside v-if="$store.getters.isAuthenticatedWithRole(0)" class="nodeContentElement">
			
			<div v-if="news.url==''||news.url==null" style="background-color:#eeeeee" class="pictureP"></div>
			<progressive-img v-else v-bind:class="{ pictureP: true, active: showMobileMenu }" :src="news.url" fallback="/static/img/blank-user.jpg" />
			
			
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="star" x="0px" y="0px" viewBox="0 0 32.218 32.218" xml:space="preserve" @click="destacada=!destacada">
			<path v-bind:class="{ active: destacada }" d="M32.143,12.403c-0.494-1.545-3.213-1.898-6.092-2.279c-1.578-0.207-3.371-0.441-3.912-0.842 c-0.545-0.398-1.305-2.035-1.977-3.482c-1.222-2.631-2.379-5.113-3.997-5.117l-0.03-0.005c-1.604,0.027-2.773,2.479-4.016,5.082 c-0.685,1.439-1.464,3.07-2.007,3.465C9.563,9.616,7.77,9.836,6.187,10.028c-2.876,0.35-5.599,0.678-6.107,2.215 s1.479,3.426,3.585,5.422c1.156,1.098,2.465,2.342,2.671,2.982s-0.143,2.416-0.448,3.977c-0.558,2.844-1.085,5.537,0.219,6.5 c0.312,0.223,0.704,0.336,1.167,0.326c1.331-0.021,3.246-1.057,5.097-2.061c1.387-0.758,2.96-1.613,3.66-1.621 c0.677,0,2.255,0.879,3.647,1.654c1.893,1.051,3.852,2.139,5.185,2.117c0.416-0.006,0.771-0.113,1.061-0.322 c1.312-0.945,0.812-3.637,0.285-6.492c-0.29-1.564-0.615-3.344-0.41-3.984c0.212-0.637,1.536-1.865,2.703-2.955 C30.627,15.809,32.633,13.948,32.143,12.403z"/>
			<title>Noticia Portada</title>
	</svg>
			
			
			</progressive-background>

	        <ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', news, news.url, 'img')">{{$i18n.t('image.selectImage')}}</ui-button>
            
			 <label>Data Publicació:
			 <vue-datepicker-local v-model="publishedDate" :local="datePickerOptions" format="DD-MM-YYYY"></vue-datepicker-local>
			 </label>

			 
			<label>Titular:</label>
			<input v-model="news.titol">
		    <VuePellEditor 
		        :actions="editorOptions" 
		        :content="news.contingut" 
		        v-model="news.contingut"
		        :styleWithCss="false"
		        placeholder="..."
		    />
			
			<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="SaveNews()">{{$i18n.t('common.save')}}</ui-button>
			<ui-button v-if="news.id" color="blue" icon="delete" size="small" type="secondary" @click="deleteNews(news.id)">Borrar noticia ID:{{news.id}}</ui-button>
	
		</aside>
					
		
		<aside v-if="!$store.getters.isAuthenticatedWithRole(1)">
			<progressive-background :src="news.url" v-bind:class="{ pictureP: true, active: showMobileMenu }" @click.native="showMobileMenu = !showMobileMenu" >
			</progressive-background>
			
			<h1>{{ news.titol }}</h1>
			<small>{{ publishedDateText }}</small>
			
			<article v-html="news.contingut"></article>
			
			<em class="social">
				<vue-goodshare-facebook 
					title_social="Facebook"
				    has_counter
				    has_icon 
				></vue-goodshare-facebook>
				<vue-goodshare-twitter 
					title_social="Twitter"
				    has_icon 
				></vue-goodshare-twitter>
				<vue-goodshare-whatsapp 
					v-if="isMobileDevice()"
					title_social="WhatsApp"
				    has_icon 
				></vue-goodshare-whatsapp>
				<vue-goodshare-telegram 
					v-if="isMobileDevice()"
					title_social="Telegram"
				    has_icon 
				></vue-goodshare-telegram>
			</em>

			<em>{{ news.autor }}</em>
			
		</aside>
	

	</div>

	<NewsCarousel v-if="!$store.getters.isAuthenticatedWithRole(1)"></NewsCarousel>

    <ui-modal size="largeSquare" ref="uploadModal" title="Media Manager">
		<filemanager ref="upload" v-bind:pselected="selected"></filemanager>
		<div slot="footer">
            <ui-button @click="acceptModal('uploadModal')" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
            <ui-button @click="closeModal('uploadModal')">{{$i18n.t('modal.cancel')}}</ui-button>
        </div>
    </ui-modal>

	<pre v-if="$store.getters.isAuthenticatedWithRole(0)">
		

		
	</pre>

    </div>
    </transition>
</template>

<script>

import NewsCarousel from './NewsCarousel.vue';
import VueCoreImageUpload from 'vue-core-image-upload'
import VueDatepickerLocal from 'vue-datepicker-local'
import VuePellEditor from 'vue-pell-editor'
import VuePellEditorConfig from '../config/pelleditor'
import FileManager from './FileManager.vue'
import VueGoodshareFacebook from 'vue-goodshare/src/providers/Facebook.vue'
import VueGoodshareTwitter from 'vue-goodshare/src/providers/Twitter.vue'
import VueGoodshareWhatsapp from 'vue-goodshare/src/providers/WhatsApp.vue'
import VueGoodshareTelegram from 'vue-goodshare/src/providers/Telegram.vue'

export default {
	name: 'News',
  	components: {VueGoodshareFacebook,VueGoodshareTwitter,VueGoodshareWhatsapp,VueGoodshareTelegram, 'NewsCarousel' : NewsCarousel, VuePellEditor, 'filemanager':FileManager, VueDatepickerLocal,'vue-core-image-upload': VueCoreImageUpload},
	data () {
		return {
		    selected:{},
		    destacada:false,
		    news: '',
		    showMobileMenu: false,
		    publishedDateText: '',
		    publishedDate: '',
		    unPublishedDate: '',
			menuOptions: [
			    {
			        label: 'Borrar',
			        icon: 'delete'
			    }
			],
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
			editorOptions: VuePellEditorConfig(this.openModal),
		}
	},
	head : function() {
		return {...this.$route.meta,
	    	...{
		    	title: {
				  inner: this.news.titol
				},
		    }
		}    
	},
	methods: {
		isMobileDevice: function() {
		    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
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
		getPhoto:function(res) {
			var vm=this;
			vm.news.url = '/static'+res.file;
		},
		deleteNews: function(id){
			var vm=this;
			vm.$http.delete('/noticia/'+id).then(function (response) {
	             window.location.href = '/'+vm.$i18n.locale+'/'+vm.$i18n.t('common.newss');
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		SaveNews: function() {
			var vm = this;
			var id = vm.news.id ? vm.news.id : '';
			vm.$http.post('/noticia/'+id, 
				{
					idioma: vm.$i18n.locale,
					id: vm.news.id ? vm.news.id : null,
					tipus: "N",
					destacada: vm.destacada?vm.destacada:false,
					categoria: "noticies",
					tags:null,
					url: vm.news.url,
					alta: vm.news.alta,
					modificacio: new Date.today().toString('yyyyMMddHHmmss'),
					publicacio: vm.publishedDate.toString('yyyyMMddHHmmss'),
					//baixa: vm.unPublishedDate.toString('yyyyMMddHHmmss'),
					titol: vm.news.titol,
					contingut: vm.news.contingut
				}	
			).then(function (response) {
				vm.news = response.data[0];
	             window.location.href = '/'+vm.$i18n.locale+'/'+vm.$i18n.t('common.newss');
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	        	var n = response.data.find(function(element) {  return element.idioma == vm.$i18n.locale });
	            vm.news = n;
	            vm.destacada = n.destacada=='1';
	            vm.publishedDate = new Date.parse(vm.fixDateForParse(n.publicacio));
	            vm.publishedDateText = vm.fixDateForParse(n.publicacio||n.alta);
	            
	            vm.$emit('updateHead')
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
	  	fixDate: function (date) { 
		  if(date) return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("d/M/yyyy");
		},
	  	fixDateForParse: function (date) { 
		  if(date) return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("yyyy/M/d");
		},
	   	scrollToTop: function (scrollDuration) {
		const   scrollHeight = window.scrollY,
		        scrollStep = Math.PI / ( scrollDuration / 15 ),
		        cosParameter = scrollHeight / 2;
		var     scrollCount = 0,
		        scrollMargin,
		        scrollInterval = setInterval( function() {
		            if ( window.scrollY != 0 ) {
		                scrollCount = scrollCount + 1;  
		                scrollMargin = cosParameter - cosParameter * Math.cos( scrollCount * scrollStep );
		                window.scrollTo( 0, ( scrollHeight - scrollMargin ) );
		            } 
		            else clearInterval(scrollInterval); 
		        }, 15 );
		}
	},
	mounted: function () {
		if(this.$route.params.slug) {
			this.getData('noticia/slug/'+this.$route.params.slug);
		}
		else if(this.$store.getters.isAuthenticatedWithRole(0)) {
			this.news = {
					url: '',
					alta: new Date.today().toString('yyyyMMddHHmmss'),
					publicacio: new Date.today(),
					titol: '',
					contingut: ''
				};
			this.publishedDate=this.news.publicacio;
		} else {
			this.$router.push({ path: `/` });
		}
		this.scrollToTop(0);
		
		
	},
	watch: {
		destacada: function(old, newv){
			this.news.destacada=newv?false:true;
		}
	},
	beforeRouteUpdate (to, from, next) {
		//el puto router no actualitza el component ja que soles cambies de slug, pel que esta el before route update que ens permet cridar i actualitzar el objecte news al cambiar de slug
    	this.getData('noticia/slug/'+to.params.slug);
    	next();//aço actualitza la url
		this.scrollToTop(600);
	}
}
</script>

<style lang="less">
@import "../assets/less/defines.less";

.news {
	width:100%;
	min-height:200px;
	background-color:white;
	padding: 0 0 4vw 0;
	
	.nodeContentElement{
		
	}
	
	#star {
		width: 42px;
	    height: auto;
	    position: absolute;
	    top: 2%;
	    z-index: 1;
	    right: 2%;
	    cursor: pointer;
	    path {
	    	fill:@fedcolor;
	    	&.active{
	    		fill:#ffdd00;
	    	}
	    }
	    &:hover{
	    	animation: pulse 1s ease infinite;
	    }
	}
	
	.datepicker {
	    width: 210px;
	}
	.icon-right {
	    position: absolute;
	    right: 20px;
	}
	
	.uploader {
	    text-align: center;
	    width: 75%;
	    margin-left: 15%;
	    color: #87212e;
	    font-weight: bold;
	    input {
	    	cursor:pointer;
		}
	}
	
	.progressive-image-main {
	    height: 100%;
	    object-fit: cover;
	}
	
	& > aside > h1 {
		padding: 0px 5vw;
		font-family: 'Rambla', cursive;
		margin: 20px 0;
	}
	& > aside > small {
		padding: 0px 5vw;
		font-weight:bolder;
		text-transform:cursive;
	}
	& > aside > .pictureP {
		width: 100%;
	    display: block;
	    margin: 0 auto;
	    position: relative;
	    overflow: hidden;
	    height: 100vw;
	    max-height: 480px;
	    background-size: cover;
	    margin-bottom: 20px;
	    transition: max-height 1.5s ease;
	    cursor:zoom-in;
	    
	    & > div > div {
	    	background-position: 50% 33%!important;
			transition: all 1.5s ease;
		}
	    
	    &.active > div > div {
	    	max-height: 100vh;
		    position: fixed;
		    height: 100vh;
		    top: 0;
		    z-index: 99;
		    left: 0;
		    background-size: contain!important;
		    background-repeat: no-repeat;
		    background-color: black;
		    background-position: center;
		    cursor:zoom-out;
			
			&:before, &:after {
			  position: absolute;
			  right: 60px;
			  top: 30px;
			  content: ' ';
			  height: 33px;
			  width: 2px;
			  background-color: #ffffff;
			}
			&:before {
			  transform: rotate(45deg);
			}
			&:after {
			  transform: rotate(-45deg);
			}

	    }
	}
	
	& > aside > article {
		padding: 0px 5vw;
		font-size:1.1em;
	}
	em {
	    padding: 0px 5vw;
	    &.social{
	    	display:block;
	    }
	}
}

@keyframes pulse {
  from {
    transform: scale3d(1, 1, 1);
  }

  50% {
    transform: scale3d(1.2, 1.2, 1.2);
  }

  to {
    transform: scale3d(1, 1, 1);
  }
}

</style>
