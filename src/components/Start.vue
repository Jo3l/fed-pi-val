<template>
    <transition name="fade">
    <div id="start">
	
    	<div class="teaser"></div>
    
	<div class="fancyStart">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			     <path stroke="null" d="m0,18.44316c29.9,0 57.8,-13.60243 87.5,-13.60243c30.2,0 58.1,13.60243 87.1,13.60243c29.9,0 57.8,-13.60243 87.7,-13.60243c29.9,0 57.8-13.60243 87.7,13.60243l0,2.30684l-350,0l0,-2.30684z" fill="rgba(69,121,226,0.5)" id="svg_1"/>


		</svg>
	</div> 
	<div class="fancyEnd">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 350 20" preserveAspectRatio="none" enable-background="new 0 0 350 20" xml:space="preserve">
			<path stroke="null" id="svg_1" fill="rgba(45,85,170,0.5)" d="m0,2.21516c29.9,0 57.8,14.53465 87.5,14.53465c30.2,0 58.1,-14.53465 87.1,-14.53465c29.9,0 57.8,14.53465 87.7,14.53465c29.9,0 57.8,-14.53465 87.7,-14.53465l0,-2.46494l-350,0l0,2.46494z"/>
		</svg>
	</div>    	
    	
    	<news></news>
    	
		<div class="noticiasdiv">
			<div class="wrapper">
				<div v-for="noticia in noticias" class="noticia">
				<span class="creacio">{{ noticia.modificacio }}</span>
				<h3>{{ noticia.titol }}</h3>
				{{ noticia.imatge }}
				<p class="cos">{{ noticia.contingut }}</p>
				</div>
			</div>
		</div>
		
        <div class="col-group">

		<ui-button @click="post()">Hello world!</ui-button>

	            
        </div>

    </div>
    </transition>
</template>

<script>

import News from './NewsCarousel.vue';

export default {
  name: 'Start',
  components: {'news' : News},
  data () {
    return {
    	noticias: {}
        /*id: 1,
        slug:'',
        titol: '',
        categoria: '',
        tags: '',
        idioma: '',
        autor: '',
        contingut: '',
        imatge: '',
        json: '',
        alta:'',
        modificacio:'',
        publicacio:'',
        baixa:''*/
    }
  },
  methods: {
  	dayGroup: function(dateStr) {
  		
  		return dateStr.split(' ')[0]
  		
  		if (vm.day != dateStr.split(' ')[0]) {
  			//vm.day = dateStr.split(' ')[0];
  			//return true;
  		} else {
			//return false;
  		}
  	},
  	stringDate: function(dateStr) {
  		return dateStr.substr(6,2)+'/'+dateStr.substr(4,2)+'/'+dateStr.substr(0,4);
  	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	},
    getData: function() {

        var vm = this;
		vm.loadingBar(true);
        this.$http.get('index.php/noticia')
        .then(function (response) {
            
            // console.log(response);
            vm.noticias = response.data;
            vm.noticias.forEach(function(elm){ 
            	elm.alta= vm.stringDate(elm.alta);
            	elm.modificacio= vm.stringDate(elm.modificacio);
            	elm.baixa= vm.stringDate(elm.baixa);
            });
            vm.loadingBar(false);
            console.log(vm.noticias);
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
    },
    post: function() {
    	console.log(this.links);
    	this.$http.post('', {'user':'joel', 'password':'cibermatch'} ).then(function (response) { console.log(response) });
    }
  },
    mounted: function () {
		this.getData();
    },
    watch: {

    }
}
</script>

<style>
.VueCarousel img {
    width: 100%;
}
.teaser {
	width: 100%;
    height: 50vw;
    max-height: 400px;
    background-position: right center;
    background-repeat: no-repeat;
    background: url(/static/dev/player.svg) no-repeat 120% 0% / auto 121%, url(/static/dev/wave.svg) no-repeat 0% 31% / 100%, linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #ec9c33 100%), linear-gradient(to right, #e04820 0%, #e76723 100%);
    
    /*background: url(/static/dev/player.svg) no-repeat 120% 0% / auto 121%, linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #963b48 100%), linear-gradient(to right, #e61626 0%, #c38f96 100%);*/
}
</style>
