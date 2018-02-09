<template>
    <transition name="fade">
	    <div class="teaser">
		   	<div class="fancyEnd">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
					<path stroke="null" id="svg_1" fill="rgba(45,85,170,0.5)" d="m0,2.21516c29.9,0 57.8,14.53465 87.5,14.53465c30.2,0 58.1,-14.53465 87.1,-14.53465c29.9,0 57.8,14.53465 87.7,14.53465c29.9,0 57.8,-14.53465 87.7,-14.53465l0,-2.46494l-350,0l0,2.46494z"/>
				</svg>
			</div>
			
			
			<aside>
					<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('common.news')+'/'+newsTeaser.slug }"><h2>{{ newsTeaser.titol }}</h2></router-link>
			</aside>
			
			<progressive-background :src="newsTeaser.url" class="teaserPicture">
			

				
				<!--
				<div v-bind:class="{ competicions:true, active: newsTeaser!='' }">
							<h3>COMPETICIONS EN JOC</h3>
							<ul>
								<li><a href="http://fedpival.es/competicions/documents/13/12/13">Escala i Corda - Lliga Juvenil Tecnif.</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/14/20">Escala i Corda - Auton. AON EiC</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/18/113">Raspall - Individual Autonomic</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/13/285">Raspall - Circuit Sub-18 Femení</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/13/286">Raspall - Circuit Sub-23 Femení</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/7/312">Frontó - XXXVI JECV Parelles 2017-18</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/17/313">Llargues i Palma - Trofeu Hivern Llargues</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/5/316">Raspall - XXXVI JECV 2017-2018</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/6/317">Galotxa - XXXV JECV galotxa 17 - 18</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/49/318">Fundació per a la Pilota Valenciana - Convocatòria Projectes Fundació</a></li>
								<li><a href="http://fedpival.es/competicions/documents/13/51/321">Copa Generalitat - Copa Generalitat 2018 - Frontó</a></li>
							</ul>
				</div>
				-->
	
			</progressive-background>
			
			
			<div class="three-quarters-loader"> </div>
			
	    </div>
    </transition>
</template>

<script>

export default {
	name: 'Teaser',
  	components: {},
	data () {
		return {
			newsTeaser: '',
			loadedTeaser:false,
		}
	},
	methods: {
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.newsTeaser = response.data[0];
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    }
	},
	mounted: function () {
		this.getData('/noticia/destacada/i/'+this.$i18n.locale);
	},
	watch: {
	
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
    aside {
    	a{color:white;}
		position: absolute;
	    bottom: 0;
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
		}
    }
	.teaserPicture {
		width: 100%;
	    display: block;
	    margin: 0 auto;
	    position: relative;
	    overflow: hidden;
	    height: 100vw;
    	max-height: 480px;
	    background-size: cover;
	    background-position: 50% 33%;
	    z-index:5;

		& + .three-quarters-loader {
		    position: absolute;
		    top: ~"calc(50% - 50px)";
		    left: ~"calc(50% - 50px)";
		    z-index:1;
		    border:1em solid rgba(135, 33, 46, 0.56);
		    border-right-color: transparent;
		}
	}
	.competicions {
		position: absolute;
	    right: 0px;
	    top: -100%;
	    padding: 0 20px;
	    background-color: rgba(254,254,254,0.8);
	    border-radius: 0px 0px 0px 10px;
	    transition: top 1s ease;
	    transition-delay:2s;
	    max-height: 390px;
    	overflow-y: auto;
    	@media(max-width:@screenTablet) {
	    	display:none;
		}
			
	    &.active {
	    	top: 0%;
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
