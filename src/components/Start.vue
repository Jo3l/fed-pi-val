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
				<span class="creacio">{{ stringDate(noticia.modificacio) }}</span>
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
    	noticias: {},
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
            vm.noticias = response.data;
            vm.loadingBar(false);
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
		//this.getData();
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

@media screen and (min-width:768px){.__vev_calendar-wrapper{max-width:1200px;margin:0 auto}.__vev_calendar-wrapper .cal-wrapper{width:50%;padding:100px 50px}.__vev_calendar-wrapper .cal-wrapper .date-num{line-height:50px}.__vev_calendar-wrapper .events-wrapper{width:50%;background-color:#f29543;color:#fff;padding:40px 50px;position:absolute;left:50%;top:0;bottom:0}}@media screen and (max-width:768px){.__vev_calendar-wrapper .cal-wrapper{width:100%;padding:10px 5px}.__vev_calendar-wrapper .cal-wrapper .date-num{line-height:42px}.__vev_calendar-wrapper .events-wrapper{width:100%;margin-top:10px;padding:10px}}.__vev_calendar-wrapper{position:relative;overflow:hidden;width:100%}.__vev_calendar-wrapper *{box-sizing:border-box}.__vev_calendar-wrapper .cal-wrapper .cal-header{position:relative;width:100%;background-color:#fff;font-weight:500;overflow:hidden;padding-bottom:10px}.__vev_calendar-wrapper .cal-wrapper .cal-header>div{float:left;line-height:20px;padding:15px}.__vev_calendar-wrapper .cal-wrapper .cal-header .title{width:60%;text-align:center}.__vev_calendar-wrapper .cal-wrapper .cal-header .l{text-align:left;width:20%;cursor:pointer;user-select:none;-webkit-tap-highlight-color:rgba(0,0,0,0)}.__vev_calendar-wrapper .cal-wrapper .cal-header .r{text-align:right;width:20%;cursor:pointer;user-select:none;-webkit-tap-highlight-color:rgba(0,0,0,0)}.__vev_calendar-wrapper .cal-wrapper .cal-body{width:100%}.__vev_calendar-wrapper .cal-wrapper .cal-body .weeks{width:100%;overflow:hidden;text-align:center;font-size:1rem}.__vev_calendar-wrapper .cal-wrapper .cal-body .weeks .item{line-height:50px;float:left;width:14.285%}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates{width:100%;overflow:hidden;text-align:center;font-size:1rem}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item{position:relative;float:left;display:block;width:14.285%;cursor:default;-webkit-tap-highlight-color:rgba(0,0,0,0)}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item .date-num{font-size:1rem;position:relative;z-index:3}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item.event{cursor:pointer}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item.selected-day .is-event{background-color:#f29543}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item .is-event{content:"";border:1px solid #f29543;background-color:#fff;border-radius:50%;width:36px;height:36px;position:absolute;left:50%;top:50%;z-index:1;margin-left:-18px;margin-top:-19px}.__vev_calendar-wrapper .cal-wrapper .cal-body .dates .item .is-today{content:"";background-color:#f29543;border-radius:50%;opacity:.8;width:12px;height:4px;position:absolute;left:50%;top:50%;z-index:2;margin-left:-6px;margin-top:8px}.__vev_calendar-wrapper .events-wrapper{border-radius:10px}.__vev_calendar-wrapper .events-wrapper .cal-events{height:100%}.__vev_calendar-wrapper .events-wrapper .date{max-width:60%;min-width:200px;text-align:center;color:#fff;background-color:rgba(0,0,0,.2);border-radius:20px;margin:0 auto;font-size:22px}.__vev_calendar-wrapper .events-wrapper .event-item{padding:5px 20px;margin-top:15px;box-shadow:0 3px 11px 2px rgba(0,0,0,.1);background-color:#fff;border-radius:5px;color:#323232;position:relative}.__vev_calendar-wrapper .events-wrapper .event-item .title{height:40px;line-height:40px;color:#323232;font-size:16px;border-bottom:1px solid #f2f2f2}.__vev_calendar-wrapper .events-wrapper .event-item .time{position:absolute;right:30px;top:17px;color:#9b9b9b;font-size:14px}.__vev_calendar-wrapper .events-wrapper .event-item .desc{color:#9b9b9b;font-size:14px;padding:7px 0}.__vev_calendar-wrapper .arrow-left.icon{color:#000;position:absolute;left:6%;margin-top:10px}.__vev_calendar-wrapper .arrow-left.icon:before{content:"";position:absolute;left:1px;top:-5px;width:10px;height:10px;border-top:1px solid currentColor;border-right:1px solid currentColor;-webkit-transform:rotate(-135deg);transform:rotate(-135deg)}.__vev_calendar-wrapper .arrow-right.icon{color:#000;position:absolute;right:6%;margin-top:10px}.__vev_calendar-wrapper .arrow-right.icon:before{content:"";position:absolute;right:1px;top:-5px;width:10px;height:10px;border-top:1px solid currentColor;border-right:1px solid currentColor;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.__vev_calendar-wrapper h3,.__vev_calendar-wrapper p{margin:0;padding:0}

</style>
