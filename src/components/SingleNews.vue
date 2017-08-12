<template>
    <transition name="fade">
    <div id="news">
	
	<div class="news">
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
		
		
		<h1>{{ $store.news.titol }}</h1>
		<small>{{ publishedDate }}</small>
		<article v-html="$store.news.contingut"></article>
		<em>{{ $store.news.autor }}</em>
	</div>

	<NewsCarousel></NewsCarousel>

    </div>
    </transition>
</template>

<script>

import NewsCarousel from './NewsCarousel.vue';

export default {
	name: 'News',
  	components: {'NewsCarousel' : NewsCarousel},
	data () {
		return {
		    pagina: 1,
		    showMobileMenu: false,
		    publishedDate: '',
			menuOptions: [
			    {
			        label: 'Editar',
			        icon: 'edit'
			    },
			    {
			        label: 'Borrar',
			        icon: 'delete'
			    }
			]
		}
	},
	methods: {
		menuSelection: function(e) {
			if(e.label == 'Editar')  this.$router.push('/'+this.$i18n.locale+'/'+this.$i18n.t('common.news')+'/edit/'+this.$store.news.slug);
		},
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.$store.news = response.data;
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
		this.scrollToTop(0);
	},
	watch: {
	
	},
	beforeRouteUpdate (to, from, next) {
		//el puto router no actualitza el component ja que soles cambies de slug, pel que esta el before route update que ens permet cridar i actualitzar el objecte news al cambiar de slug
    	this.getData(this.$i18n.t('common.news')+'/slug/'+to.params.slug);
    	next();//aço actualitza la url
		this.scrollToTop(600);
	}
}
</script>

<style lang="less">

.news {
	width:100%;
	min-height:200px;
	background-color:white;
	padding: 0 0 4vw 0;
	
	.icon-right {
	    position: absolute;
	    right: 20px;
	}
	
	& > h1 {
		padding: 0px 5vw;
		font-family: 'Rambla', cursive;
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
	    cursor:zoom-in;
	    &.active {
	    	max-height: 100vh;
		    position: fixed;
		    height: 100vh;
		    top: 0;
		    z-index: 99;
		    left: 0;
		    background-size: contain;
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
	
	& > article {
		padding: 0px 5vw;
		font-size:1.1em;
	}
	em {
	    padding: 0px 5vw;
	}
}

</style>
