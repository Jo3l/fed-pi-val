<template>
    <transition name="fade">
    <div id="news">
    	
    	<h1>{{ $t('common.newss') }} <ui-button v-if="$store.getters.isAuthenticatedWithRole(0)" color="blueButtonToRight" icon="save" size="small" type="secondary" @click="$router.push({ path:'/'+$i18n.locale+'/noticia'})">{{ $t('common.newsNew') }}</ui-button></h1>
    	
    	<nav>
			<ui-icon-button @click="decPage" icon="chevron_left" type="primary"></ui-icon-button>
			<strong class="textPositioned">{{ $t('common.page') }}: {{ pagina }}</strong>
			<ui-icon-button @click="incPage" icon="chevron_right" type="primary"></ui-icon-button>
		</nav>
		
		<NewsCarousel :pagina=pagina></NewsCarousel>
		
    	<nav>
			<ui-icon-button @click="decPage" icon="chevron_left" type="primary"></ui-icon-button>
			<strong class="textPositioned">{{ $t('common.page') }}: {{ pagina }}</strong>
			<ui-icon-button @click="incPage" icon="chevron_right" type="primary"></ui-icon-button>
		</nav>
		
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
		    pagina: 0,
		    any:'',
		    mes:''
		}
	},
	methods: {
		incPage: function() {
			this.pagina++;
			this.$router.push({ path: `/${location.pathname.split('/')[1]}/${location.pathname.split('/')[2]}/${this.pagina}` });
		},
		decPage: function() {
			if(this.pagina >= 1) this.pagina--;
			this.$router.push({ path: `/${location.pathname.split('/')[1]}/${location.pathname.split('/')[2]}/${this.pagina}` });
		},
	},
	mounted: function () {
		if(this.$route.params.page>0) this.pagina = this.$route.params.page;
	},
	beforeRouteUpdate (to, from, next) {
		next();
	}
}
</script>

<style lang="less">

#news{

	nav {
	    padding: 20px 60px;
	    display: flex;
	    justify-content: space-between;
	}
}

</style>
