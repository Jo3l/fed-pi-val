<template>
    <transition name="fade">
    	
	    <div v-if="pagina==-1">
	    	
			<swiper :options="swiperOption" class="newsCarousel">
	
			  <swiper-slide v-for="(noticia, index) in newsCarousel" v-if="noticia.idioma==$i18n.locale&&noticia.destacada==false">
			    <div class="articleP">
			    	
			    	<img v-if="index>5" :data-src="noticia.url" class="swiper-lazy"/>
			    	<img v-else :src="noticia.url" class="swiper-lazy"/>
			    	
			    	<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('common.news')+'/'+noticia.slug }">
				    	<div class="articleContainer">
					    	<small>{{ fixDate(noticia.alta) }}</small>
					    	<h2>{{ noticia.titol }}</h2>
					    	<section>
					    		<p>{{ stripHtmlToText(noticia.contingut) }}</p>
					    	</section>
				    	</div>
			    	</router-link>
			    </div>
			    
			  </swiper-slide>
			  
			  <div class="swiper-pagination nuws" slot="pagination"></div>
		  	  <ui-icon-button icon="chevron_left" type="primary" class="swiper-button-prev nuws" slot="button-prev"></ui-icon-button>
			  <ui-icon-button icon="chevron_right" type="primary" class="swiper-button-next nuws" slot="button-next"></ui-icon-button>
			  
			</swiper>
		</div>
		
		<div class="flex" v-else="v-else">

			  <div class="multipleNews" v-for="(noticia, index) in newsCarousel" v-if="noticia.idioma==$i18n.locale">
			  	
			    <progressive-background class="articleP" :src="noticia.url">
			    	<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('common.news')+'/'+noticia.slug }">
				    	<div class="articleContainer">
					    	<small>{{ fixDate(noticia.alta) }}</small>
					    	<h2>{{ noticia.titol }}</h2>
					    	<section>
					    		<p>{{ stripHtmlToText(noticia.contingut) }}</p>
					    	</section>
				    	</div>
			    	</router-link>
			    </progressive-background>
			    
			  </div>
		</div>



    </transition>
</template>

<script>

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
  name: 'Noticias',
  components: { swiper, swiperSlide },
  props: ['pagina','busca'],
  head : function() {
	return this.$route.meta;
  },
  data () {
    return {
    	newsCarousel:'',
    	updPage:'',
    	request:'',
	    swiperOption: {
	        slidesPerView: 4,
	        slidesPerColumn: 1,
	        spaceBetween: 10,
	        lazy: true,
	        navigation: {
	          nextEl: '.swiper-button-next.nuws',
	          prevEl: '.swiper-button-prev.nuws'
	        },
	        pagination: {
	          el: '.swiper-pagination.nuws',
	          type: 'bullets',
	          clickable: true
	        },
	        breakpoints: {
	          768: {
	            slidesPerView: 2,
	            spaceBetween: 10
	          },
	          480: {
	            slidesPerView: 2,
	            spaceBetween: 10
	          },
	          320: {
	            slidesPerView: 1,
	            spaceBetween: 10
	          }
	        }
	    },
    }
  },
  methods: {
	  	slugify:function(str) {
	  	if(str==undefined) return false;
	    str = str.replace(/^\s+|\s+$/g, ''); // trim
	    str = str.toLowerCase();
	  
	    // remove accents, swap ñ for n, etc
	    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
	    var to   = "aaaaeeeeiiiioooouuuunc------";
	    for (var i=0, l=from.length ; i<l ; i++) {
	        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	    }
	
	    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
	        .replace(/\s+/g, '+') // collapse whitespace and replace by -
	        .replace(/-+/g, '+'); // collapse dashes
	
	    return str;
	},
	stripHtmlToText: function(html)	{
	    var tmp = document.createElement("DIV");
	    tmp.innerHTML = html;
	    var res = tmp.textContent || tmp.innerText || '';
	    res.replace('\u200B', '');
	    res = res.trim();
	    return res;
	},
  	fixDate: function (date) { 
	  return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("d/M/yyyy");
	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	},
	getData: function(apiUrl, ncache) {

        var vm = this;
        this.$http.get(apiUrl, {
			cache: ncache,
		    // use before callback
		    before(request) {
		
		      // abort previous request, if exists
		      if (this.previousRequest) {
		        this.previousRequest.abort();
		      }
		
		      // set previous request on Vue instance
		      this.previousRequest = request;
		    }
		}).then(function (response) {
            vm.newsCarousel = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
        
    }
  },
    mounted: function () {
    	
    	//usem -1 quan el carousel es carrega desde la plana principal, per tant no hi ha paginat.
    	if(this.pagina==-1) {
    		this.getData('/noticia/i/'+this.$i18n.locale, true);
    	} else {
    		this.getData('/noticia/p/'+this.pagina+'/i/'+this.$i18n.locale+(this.busca!='all'&&this.busca!=''?'/s/'+this.slugify(this.busca):''), true);
    	}
    },
	watch: {
	    pagina: function (newVal, oldVal) {
	      this.getData('/noticia/p/'+newVal+'/i/'+this.$i18n.locale+(this.busca!='all'&&this.busca!=''?'/s/'+this.slugify(this.busca):''), true);
	    }, 
	    busca: function (newVal, oldVal) {
	    	console.log(newVal);
	      this.getData('/noticia/p/'+this.pagina+'/i/'+this.$i18n.locale+(newVal!='all'&&newVal.busca!=''?'/s/'+this.slugify(newVal):''), false);
	    }, 
	},
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

.VueCarousel-navigation-button {
	transform:initial!important;
	color:white!important;
	text-shadow: 0px 0px 1px black!important;
	@media(max-width:@screenTablet) {
		display:none;
	}
}
.flex {
    display: flex;
    flex-wrap: wrap;
    width: 90%;
    margin: 0 auto;
}
.newsCarousel {
	min-height:374px;
	a {text-decoration:none;color:black;}
	.swiper-slide {
		overflow:hidden;
	}
	.articleP {
		&>div{padding-bottom:initial!important;}
	    min-height: 370px;
	    overflow: hidden;
	    background-size: 130%;
	    background-position: top;
	    display: flex;
	    flex-direction: column;
	    justify-content: flex-end;
	    background-repeat: no-repeat;
	    //border-right: 2px solid white;
    	//border-left: 2px solid white;
		cursor:e-resize;
		& > img{
		    position: absolute;
		    object-fit: contain;
		    display: block;
		    z-index: -1;
		    height: 87%;
		    width: auto;
		    top: 0;
		    left: -25%;
		}
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 40px 20px 20px 20px;
    		transition: transform .2s ease;
    		width:~"calc(100% + 1px)";
    		&:hover {
    			//transform: translateY(10%)
    		}
		}
		
		small {text-shadow: 1px 1px 1px white;}
		
		h2 {
			margin:0;
			line-height:1em;
		}
		
		p {margin:0;}
	
		}
}

.multipleNews {
	width:20%;

	@media(max-width:@screenDesktop) {
		width: 33.3%;
	}
	
	@media(max-width:@screenTablet) {
		width:50%;
	}
	
	@media(max-width:@screenMobile) {
		width:100%;
	}
	
	a {text-decoration:none;color:black;}
	.articleP {
		&>div{padding-bottom:initial!important;}
		padding-top:50%;
	    overflow: hidden;
	    background-size: 130%;
	    background-position: top;
	    display: flex;
	    flex-direction: column;
	    justify-content: flex-end;
	    background-repeat: no-repeat;
	    border-right: 2px solid white;
    	border-left: 2px solid white;
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 10px;
    		transition: transform .2s ease;
    		width:~"calc(100% + 1px)";
		}
		
		small {text-shadow: 1px 1px 1px white;}
		
		h2 {
			margin:0;
			line-height:1em;
			font-size:1em;
			text-shadow: 1px 1px 1px white;
		}
		
		p {
			margin:0;
			font-size:0.9em;
		}
	
		}
}

.newsSinCarousel{
	
}

</style>
