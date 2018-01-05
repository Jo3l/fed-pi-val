<template>
    <transition name="fade">
	    <div class="teaser">
		   	<div class="fancyEnd">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
					<path stroke="null" id="svg_1" fill="rgba(45,85,170,0.5)" d="m0,2.21516c29.9,0 57.8,14.53465 87.5,14.53465c30.2,0 58.1,-14.53465 87.1,-14.53465c29.9,0 57.8,14.53465 87.7,14.53465c29.9,0 57.8,-14.53465 87.7,-14.53465l0,-2.46494l-350,0l0,2.46494z"/>
				</svg>
			</div>
			<picture v-bind:style="{ 'background-image': 'url(' + newsTeaser.url + ')' }">
				<aside>
					<h2>{{ newsTeaser.titol }}</h2>
				</aside>
			</picture>
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
.teaser {
	width:100%;
	min-height:200px;
	background-color:white;
	picture {
		width: 100%;
	    display: block;
	    margin: 0 auto;
	    position: relative;
	    overflow: hidden;
	    height: 100vw;
    	max-height: 480px;
	    background-size: cover;
	    background-position: 50% 33%;
	    aside {
			position: absolute;
		    bottom: 0;
		    background-image: linear-gradient(to top, rgba(14, 2, 4, 0.61) 0%, rgba(255, 255, 255, 0) 100%);
		    padding: 15px 50px 25px 0;
		    width:100%;
			h2 {
				padding: 0px 2vw;
			    //font-family: 'Rambla', cursive;
			    margin: 0;
			    color: white;
			    text-shadow: 1px 1px 3px #101010;
			}
	    }

	}
}

</style>
