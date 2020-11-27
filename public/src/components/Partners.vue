<template>
	<div id="patrocinadors">
		
		<h1> <span class="ui-icon material-icons">flag</span> Patrocinadors / Col·laboradors </h1>
			
		<div>
			<a href="http://pilotaescola.es" target="_blank"><img src="/static/img/footer/pilotaalescola.jpg" alt="Pilota a l'Escola" title="Pilota a l'Escola" loading="lazy" style="width:128px !important"></a>
			<a href="http://www.ceice.gva.es/ca/web/deporte/pilota-valenciana" target="_blank"><img src="/static/img/footer/gva.jpg" alt="Conselleria d'Educació, Investigació, Cultura i Esport" title="Conselleria d'Educació, Investigació, Cultura i Esport" loading="lazy"></a>
			<a href="http://www.sempreteua.gva.es/ca/elements-promocionals" target="_blank"><img src="/static/img/footer/sempreteua.jpg" alt="Sempre Teua" title="Sempre Teua" loading="lazy"></a>
			<a href="http://www.dival.es/" target="_blank"><img src="/static/img/footer/diputacioval.jpg" alt="Diputació de València" title="Diputació de València" loading="lazy"></a>
			<a href="http://www.diputacionalicante.es" target="_blank"><img src="/static/img/footer/alacant.jpg" alt="Diputació d'Alacant" title="Diputació d'Alacant" loading="lazy"></a>
			<a href="http://www.dipcas.es" target="_blank"><img src="/static/img/footer/castello.jpg" alt="Diputació de Castelló" title="Diputació de Castelló" loading="lazy"></a>
			<a href="http://www.valencia.es" target="_blank"><img src="/static/img/footer/valencia.jpg" alt="Ajuntament de València" title="Ajuntament de València" loading="lazy"></a>
			<a href="http://www.deportevalencia.com" target="_blank"><img src="/static/img/footer/fdm.jpg" alt="Fundació Esportiva Municipal" title="Fundació Esportiva Municipal" loading="lazy"></a>
			<a href="https://fundaciontrinidadalfonso.org/va/" target="_blank"><img src="/static/img/footer/fta.jpg" alt="Trinidad Alfonso" title="Trinidad Alfonso" loading="lazy" style="width:128px !important" /></a>
		</div>
		<div>
			<a href="http://www.elcorteingles.es" target="_blank"><img src="/static/img/footer/eci.jpg" alt="El Corte Inglés" title="El Corte Inglés" loading="lazy"></a>
			<a href="http://www.edicom.es" target="_blank"><img src="/static/img/footer/edicom.jpg" alt="Edicom" title="Edicom" loading="lazy"></a>
			<a href="http://www.caixapopular.es" target="_blank"><img src="/static/img/footer/caixapopular.jpg" alt="Caixa Popular" title="Caixa Popular" loading="lazy"></a>
			<a href="http://www.bankia.es" target="_blank"><img src="/static/img/footer/bankia.jpg" alt="Bankia" title="Bankia" loading="lazy"></a>
			<a href="http://www.acerbis.com/" target="_blank"><img src="/static/img/footer/acerbis.jpg" alt="Acerbis" title="Acerbis" loading="lazy"></a>
			<a href="https://labolata.com/" target="_blank"><img src="/static/img/footer/bolata.jpg" alt="La Bolata" title="La Bolata" loading="lazy"></a>
			<a href="http://www.intersport.es/" target="_blank"><img src="/static/img/footer/intersport.jpg" alt="Intersport" title="Intersport" loading="lazy"></a>
			<a href="https://www.aon.com" target="_blank"><img src="/static/img/footer/aon.jpg" alt="Aon" title="Aon" loading="lazy"></a>
			<a href="https://www.sgs.es/es-es/news/2015/11/iso-9001-2015" target="_blank"><img src="/static/img/footer/SGS_ISO 9001_TCL_HR.jpg" alt="iso 9001" title="iso 9001" loading="lazy"></a>
			<!--img src="/static/img/footer/iso.jpg" alt="ISO 9001" title="ISO 9001" loading="lazy">
			<a href="http://lacaixa.es/" target="_blank"><img src="/static/img/footer/lacaixa.jpg" alt="La Caixa" title="La Caixa" loading="lazy"></a>
			<a href="http://twitter.com/fedpival" target="_blank"><img src="/static/img/footer/tw.png" alt="Twitter" title="Twitter" loading="lazy"></a>
			<a href="http://facebook.com/fedpival" target="_blank"><img src="/static/img/footer/fb.png" alt="Facebook" title="Facebook" loading="lazy"></a>
			<a href="http://www.ceice.gva.es/web/deporte/pilota-valenciana" target="_blank"><img src="/static/img/footer/tv.png" alt="Partides" title="Partides" loading="lazy"></a-->
		</div>
	</div>
		
		
</template>

<script>

export default {
	name: 'Patrocinadors',
	components: {  },
	data () {
	return {
		loadingBar: false,
	}
	},
	methods: {
	
	},
	mounted: function() {

	},
	beforeMount: function() {

	},
	computed:{

	},
	created: function(){

	},
	computed: {

	},
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

#patrocinadors {
	margin-top:auto;
}

#patrocinadors div { 
	display: flex;
	flex-wrap:wrap;
	justify-content: center;
}
#patrocinadors div a{
	padding:0 20px;
}
#patrocinadors img { 
	width:auto !important;
	height:64px !important;
}


</style>