<template>
    <transition name="fade">
	    <div class="teaser">
		   	<div class="fancyEnd">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
					<path stroke="null" id="svg_1" fill="rgba(45,85,170,0.5)" d="m0,2.21516c29.9,0 57.8,14.53465 87.5,14.53465c30.2,0 58.1,-14.53465 87.1,-14.53465c29.9,0 57.8,14.53465 87.7,14.53465c29.9,0 57.8,-14.53465 87.7,-14.53465l0,-2.46494l-350,0l0,2.46494z"/>
				</svg>
			</div>
			<swiper :options="swiperOption" class="teaserPicture">
				<swiper-slide v-for="(noticia, index) in newsTeasers" v-if="noticia.idioma==$i18n.locale&&noticia.destacada==true&&index<=5">
					<aside>
							<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('common.news')+'/'+noticia.slug }"><h2>{{ noticia.titol }}</h2></router-link>
					</aside>
					<progressive-background v-if="noticia.url" :src="noticia.url" class="picture">
					</progressive-background>
				</swiper-slide>
			</swiper>

			<div class="three-quarters-loader"> </div>
			
			<div v-bind:class="{ competicions:true, active: newsTeasers!='' }">
				
				<div class="editable">
					<h3>{{notice.titol}}</h3>
					<div v-html="notice.contingut"></div>
				</div>
				<br>
				<div class="results">
					<h3>{{$i18n.t('common.lastResults')}}</h3>
					<ul>
						<li v-for="result in lastResults"><router-link :to="{ path: result[$i18n.locale].path }">{{result[$i18n.locale].nom}}</router-link></li>
					</ul>
				</div>

				
			</div>
			
	    </div>
    </transition>
</template>

<script>

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
	name: 'Teaser',
	components: { swiper, swiperSlide },
	data () {
		return {
			newsTeasers:'',
			notice:{},
			lastResults:[],
			swiperOption: {
		        slidesPerView: 1,
		        slidesPerColumn: 1,
		        spaceBetween: 0,
		        autoplay: {
	            	delay: 4500,
	            	disableOnInteraction: false
	        	}
		    },
		}
	},
	methods: {
	    getTeasers: function(apiUrl) {
	    	var vm=this;
	        var auth = !vm.$store.getters.isAuthenticatedWithRole(0);
	        
	        vm.$http.get(apiUrl, { cache: auth })
	        .then(function (response) {
	            vm.newsTeasers = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    getResults: function(apiUrl) {
	    	var vm=this;
	        var auth = !vm.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get(apiUrl, { cache: auth })
	        .then(function (response) {
	            vm.lastResults = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
	    getNotice: function(apiUrl) {
	    	var vm=this;
	        var auth = !vm.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get(apiUrl, { cache: auth })
	        .then(function (response) {
	            vm.notice = response.data[0];
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    }
	},
	mounted: function () {
		var vm=this;
		var auth = !vm.$store.getters.isAuthenticatedWithRole(0);
		vm.getTeasers('/noticia/destacada/i/'+vm.$i18n.locale);
		vm.getNotice('/node/253');
		vm.getResults('/ultimsresultats');

	}
}
</script>

<style lang="less">

@import "../assets/less/defines.less";


.teaser {
	width:100%;
	min-height:200px;
	background-color:white;
	position:relative;
	
	h3{
		margin:0;
	}

	.teaserPicture {
		width: 74.8%;
		box-shadow: 3px 15px 12px rgba(0, 0, 0, 0.2);
		@media(max-width:768px){
			width:100%;
		}
	    display: block;
	    margin: 0;
	    position: relative;
	    overflow: hidden;
	    height: 100vw;
    	max-height: 480px;

	    z-index:5;

		& + .three-quarters-loader {
		    position: absolute;
		    top: ~"calc(50% - 50px)";
		    left: ~"calc(50% - 50px)";
		    z-index:1;
		    border:1em solid rgba(135, 33, 46, 0.56);
		    border-right-color: transparent;
		}
		
		.picture {
			height: 100vw;
    		max-height: 480px;
    		background-size: cover;
	    	background-position: 50% 33%;
		}
		
	    aside {
	    	a{color:white;}
			position: absolute;
	    	bottom: -15px;
		    background-image: linear-gradient(to top, rgba(14, 2, 4, 0.61) 0%, rgba(255, 255, 255, 0) 100%);
		    padding: 15px 50px 50px 0;
		    width:100%;
		    z-index:9;
			h2 {
				padding: 0px 2vw;
			    //font-family: 'Rambla', cursive;
			    margin: 0;
			    color: white;
			    text-shadow: 1px 1px 3px #101010;
			    max-width: 80%;
			}
	    }
	}
	.competicions {
		position: absolute;
		width:25%;
	    right: 0px;
	    top: -5%;
	    opacity:0;
	    padding: 20px;
	    background-color: rgba(254,254,254,0.8);
	    border-radius: 0px 0px 0px 10px;
	    transition: all 1s ease;
	    transition-delay:2s;
	    max-height: 460px;
    	overflow-y: auto;
    	@media(max-width:@screenTablet) {
	    	display:none;
		}
			
	    &.active {
	    	top: 0%;
	    	opacity:1;
	    }
	    
		ul {
			list-style-type:none;
			padding-left:0;
			max-width: 250px;
			li {
				font-size:14px;
				margin-bottom:5px;
			}
		}
		
	}
}

</style>
