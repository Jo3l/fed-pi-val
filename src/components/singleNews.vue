<template>
    <transition name="fade">
    <div id="news">
	
	<div class="news">
		<picture v-bind:style="{ 'background-image': 'url(' + $store.news.imatge + ')' }">
		</picture>
		<h1>{{ $store.news.titol }}</h1>
		<small>{{ $store.news.alta }}</small>
		<article>{{ $store.news.contingut }}</article>
		<em>{{ $store.news.autor }}</em>
	</div>

    </div>
    </transition>
</template>

<script>

import News from './NewsCarousel.vue';

export default {
	name: 'News',
  	components: {'news' : News},
	data () {
	return {
	    pagina: 1,
	}
	},
	methods: {
	getData: function(apiUrl) {

        var vm = this;
        this.$http.get(apiUrl)
        .then(function (response) {
            vm.$store.news = response.data;
            console.log(vm.$store.news);
        })
        .catch(function (error) {
            console.log(error);
        });
        
    }
	},
	mounted: function () {
		this.getData(this.$parent.$i18n.t('common.news')+'/slug/'+this.$route.params.slug);
	},
	watch: {
	
	}
}
</script>

<style lang="less">

.news {
	width:100%;
	min-height:200px;
	background-color:white;
	padding: 0 0 4vw 0;
	
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
	    height: 50vh;
	    max-height: 80vw;
	    background-size: cover;
	    background-position: center;
	    margin-bottom: 3vw;
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
