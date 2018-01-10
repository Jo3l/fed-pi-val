<template>
    <transition name="fade">
    <div id="page404">
    	
	<p>Error 404<br>Disculpe, no hem trobat la pàgina.</p>
	<img src="/static/img/404.png">

    </div>
    </transition>
</template>

<script>


export default {
	name: 'Page404',
  	components: {},
	data () {
	return {
	    pagina: 1,
	}
	},
	methods: {
	
	},
	mounted: function () {

	},
	watch: {
	
	}
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

#page404 {
	img{
		max-width: 40%;
	    margin: 5% auto;
	    display: block;
	    @media(max-width:@screenTablet) {
    		max-width: 60%;
		}
		@media(max-width:@screenMobile) {
    		max-width: 80%;
		}
	}
	p{
		text-align: center;
	    font-size: 2em;
	    color: #87212e;
	    font-family: 'Rambla', sans-serif;
		@media(max-width:@screenTablet) {
    		font-size: 1.5em;
		}
		@media(max-width:@screenMobile) {
    		font-size: 1em;
		}
	}
}


</style>
