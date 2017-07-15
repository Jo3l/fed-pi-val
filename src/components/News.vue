<template>
    <transition name="fade">
    <div id="news">
	
	<div class="news">
		<picture style="background-image:url(/static/dev/foto1.jpg);">
		</picture>
		<h1>Bicorp i Xeraco jugaran de nou la final de l'Autonòmic de Raspall</h1>
		<small>13/11/2001</small>
		<article>
			<p>Amb la tornada de les semifinals de la máxima categoría del Campionat Autonòmic de Raspall ja coneixem els dos equips finalistes, que porten ja tres finals consecutives.</p>
			<p>Els defensor del títol de l’any passat, Bicorp, que jugava aquesta setmana amb Victor, Armando i Juan Carlos, eliminaven al Villalonga en la seua semifinal, mentre que Xeraco, subcampions en el 2016, es desfeia en l’altre costat del quadre de la formació d’Oliva.</p>
			<p>La divisió de plata també coneix als seus finalistes, Quatretonda A, que ha deixat en semifinals als seus companys del B, i Otos, que va eliminar al Gavarda B. En tercera encara no està clar, ja que falten les partides de tornada que es jugaran el pròxim cap de semana. Queden pendents també resultats de quarta A, mentre que en quarta B seran l’Alqueria d’Asnar i La Vall D els que jugaran la final.</p>
			<p>Les finals es disputaran en el carrer de pilota de Polinya, sent la seua presentación oficial el pròxim dimecres 28 de juny, a les 20 hores en la Casa de la Cultura de Polinyà del Xúquer.</p>
		</article>
		<em>Nom del autor</em>
	</div>

	<news></news>

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
	    links:{},
	    tag: {},
	    tagNames: {},
	    users: {}
	}
	},
	methods: {
	
	},
	mounted: function () {
	console.log(this.$route.params.slug);
	console.log(this.$router.options.routes);
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
