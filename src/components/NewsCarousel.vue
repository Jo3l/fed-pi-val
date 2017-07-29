<template>
    <transition name="fade">
    	
		<carousel :perPageCustom="[[0, 1],[480, 1],[768, 2],[992, 2], [1200, 3]]" :minSwipeDistance=30 :navigationEnabled="true" paginationActiveColor="#87212e" paginationColor="#e28b96" class="newsCarousel">

		  <slide v-for="noticia in $store.start" v-if="noticia.idioma==$i18n.locale&&noticia.destacada==false">
		  	<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('common.news')+'/'+noticia.slug }">
		    <article v-bind:style="{ 'background-image': 'url(' + noticia.imatge + ')' }">
		    	<div class="articleContainer">
			    	<small>{{ fixDate(noticia.alta) }}</small>
			    	<h2>{{ noticia.titol }}</h2>
			    	<section>
			    		<p>{{ noticia.contingut }}</p>
			    	</section>
		    	</div>
		    </article>
		    </router-link>
		  </slide>

		</carousel>

    </transition>
</template>

<script>

export default {
  name: 'Noticias',
  data () {
    return {
    }
  },
  methods: {
  	fixDate: function (date) { 
	  return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("d/M/yyyy");
	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	},
  },
    beforeCreate: function () {
    	
		this.$store.apiUrl += '/noticia/i/'+this.$i18n.locale;

    },
    watch: {

    }
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
.newsCarousel {
	a {text-decoration:none;color:black;}
	article {
	    min-height: 400px;
	    overflow: hidden;
	    background-size: 130%;
	    background-position: top;
	    display: flex;
	    flex-direction: column;
	    justify-content: flex-end;
	    background-repeat: no-repeat;
	    border-right: 2px solid white;
    	border-left: 2px solid white;
		cursor:pointer;
		.articleContainer {
			background-image: linear-gradient(rgba(0, 0, 0, 0) 0%, #ffffff 41%);
    		padding: 40px 20px 20px 20px;
		}
		
		small {text-shadow: 1px 1px 1px white;}
		
		h2 {
			margin:0;
			line-height:1em;
		}
		
		p {margin:0;}
	
		}
}

</style>
