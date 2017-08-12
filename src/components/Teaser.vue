<template>
    <transition name="fade">
	    <div class="teaser" >
			<picture v-bind:style="{ 'background-image': 'url(' + $store.newsTeaser.imatge + ')' }">
				<aside>
					<h1>{{ $store.newsTeaser.titol }}</h1>
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
			teaser: '',
		}
	},
	methods: {
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.$store.newsTeaser = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    }
	},
	mounted: function () {
		if(this.$store.newsTeaser == '') this.getData('/noticia/destacada/i/'+this.$i18n.locale);
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
		    background-image: linear-gradient(to right, rgb(135, 33, 46) 0%, rgba(255, 255, 255, 0) 100%);
		    padding: 15px 50px 25px 0;
			h1 {
				padding: 0px 5vw;
			    //font-family: 'Rambla', cursive;
			    margin: 0;
			    color: white;
			    text-shadow: 1px 1px 3px #101010;
			}
	    }

	}
}

</style>
