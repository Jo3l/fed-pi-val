<template>
    <transition name="fade">
    	
	    <div v-if=" pagina==null ">
			<carousel :perPageCustom="[[0, 1],[480, 1],[768, 2],[992, 2], [1200, 3]]" :minSwipeDistance=30 :navigationEnabled="true" :paginationPadding=5 paginationActiveColor="#87212e" paginationColor="#e28b96" class="newsCarousel">
	
			  <slide v-for="noticia in newsCarousel" v-if="noticia.idioma==$i18n.locale&&noticia.destacada==false">
			  	
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
			    
			  </slide>
			</carousel>
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

import { Carousel, Slide } from 'vue-carousel';

export default {
  name: 'Noticias',
  components: { 'carousel': Carousel, 'slide': Slide},
  props: {
        pagina: {
            type: Number,
        }
  },
  data () {
    return {
    	newsCarousel:'',
    	updPage:'',
    	request:''
    }
  },
  methods: {
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
	getData: function(apiUrl) {

        var vm = this;
        this.$http.get(apiUrl, {

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
    	
    	if(this.pagina==null) {
    		this.getData('/noticia/i/'+this.$i18n.locale);
    	}else {
    		this.getData('/noticia/p/'+this.pagina+'/i/'+this.$i18n.locale);
    	}
	
    },
	watch: {
	    pagina: function (newVal, oldVal) {
	      this.getData('/noticia/p/'+newVal+'/i/'+this.$i18n.locale);
	    }
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
	    border-right: 2px solid white;
    	border-left: 2px solid white;
		cursor:e-resize;
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 40px 20px 20px 20px;
    		transition: transform .2s ease;
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
