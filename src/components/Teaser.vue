<template>
    <transition name="fade">
	    <div id="teaser">

			<picture style="background-image:url(/static/dev/foto1.jpg);">
				<aside>
					<h1>Bicorp i Xeraco jugaran de nou la final de l'Autonòmic de Raspall</h1>
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
	    pagina: 1,
	    links:{},
	    tag: {},
	    tagNames: {},
	    users: {}
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
#teaser {
	width:100%;
	min-height:200px;
	background-color:white;
	picture {
		width: 100%;
	    display: block;
	    margin: 0 auto;
	    position: relative;
	    overflow: hidden;
	    height: 50vh;
	    max-height: 80vw;
	    background-size: cover;
	    background-position: center;
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
