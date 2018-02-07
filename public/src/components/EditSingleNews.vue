<template>
    <transition name="fade">
		<div class="newsEdit">
			<picture v-bind:style="{ 'background-image': 'url(' + $store.news.imatge + ')' }"  v-bind:class="{ active: showMobileMenu }" v-on:click="showMobileMenu = !showMobileMenu">
			</picture>
			
            <div class="page__demo-group icon-right">
                <ui-icon-button color="primary" has-dropdown icon="code" ref="dropdownButton">
                    <ui-menu
                        contain-focus
                        has-icons
                        slot="dropdown"
                        :options="menuOptions"
                        @close="$refs.dropdownButton.closeDropdown()"
                        @select="menuSelection"
                    ></ui-menu>
                </ui-icon-button>
            </div>			
			
			<textarea v-model="$store.news.titol"></textarea>
			
			<small>{{ publishedDate }}</small>
			
			<vue-editor v-model="$store.news.contingut" :editorToolbar="customToolbar" class="wysiwyg"></vue-editor>
			<em>{{ $store.news.autor }}</em>
		</div>
    </transition>
</template>

<script>

import { VueEditor } from 'vue2-editor'

export default {
	name: 'News',
  	components: {VueEditor},
	data () {
		return {
		    pagina: 1,
		    showMobileMenu: false,
		    publishedDate: '',
		    htmlForEditor: null,
		    customToolbar: [
			  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
			  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
			  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
			  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
			  [{ 'align': [] }],
			
			  ['clean']                                         // remove formatting button
			],
			menuOptions: [
			    {
			        label: 'Borrar',
			        icon: 'delete'
			    }
			]
		}
	},
	methods: {
		listLanguageOptions: function() {
			var availableLanguages = this.$i18n.messages;

			for( var lang in availableLanguages ) {

				var tempLabel;
				if(this.$i18n.locale===lang) {
					tempLabel = this.$i18n.t('common.save_on_other_lang')+availableLanguages[lang].common.label;
				} else {
					tempLabel = this.$i18n.t('common.duplicate_on_other_lang')+availableLanguages[lang].common.label;
				}
				
				this.menuOptions.unshift({
						label: tempLabel,
						icon: 'save',
						language: lang
				});
				
			}
		},
		menuSelection: function(e) {
			
			console.log(e.language);
			
		},
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.$store.news = response.data[0];
	            vm.publishedDate = vm.fixDate(response.data.alta);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
	  	fixDate: function (date) { 
		  return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("d/M/yyyy");
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
		
		this.getData(this.$i18n.t('common.news')+'/slug/'+this.$route.params.slug);
		this.listLanguageOptions();
		
	}
}
</script>

<style lang="less">

.newsEdit {
	width:100%;
	min-height:200px;
	background-color:white;
	padding: 0 0 4vw 0;
	.icon-right {
	    position: absolute;
	    right: 20px;
	}
	.ql-editor {
		font-family: 'Yantramanav', cursive;
    	font-size: 1.4em;
	}
	.wysiwyg {
	    margin: 20px 50px;
	}
	#quill-container {
    	height: initial;
	}
	.ql-container.ql-snow {
    	border: none;
	}
	& > textarea {
	    margin: 0px 5vw 20px 5vw;
	    max-width: 80%;
	    width: 80%;
	    font-family: 'Rambla', cursive;
	    display: block;
	    font-size: 2em;
	    border: none;
	    border-bottom: 1px dashed #ccc;
	}
	& > small {
		padding: 0px 5vw;
		font-weight:bolder;
		text-transform:cursive;
	}
	& > picture {
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
	
	& > article {
		padding: 0px 5vw;
		font-size:1.1em;
	}
	em {
	    padding: 0px 5vw;
	}
}

</style>
