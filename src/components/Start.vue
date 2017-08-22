<template>
    <transition name="fade">
    <div id="start">
	
    	<teaser></teaser>
    
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
    	
    	<newsCarousel></newsCarousel>
    	
		<Calendar></Calendar>
		
		<cart type="slider"></cart>
		
    </div>
    </transition>
</template>

<script>

import NewsCarousel from './NewsCarousel.vue';
import Calendar from './Calendar.vue';
import Cart from './Cart.vue';
import Teaser from './Teaser.vue';

export default {
  name: 'Start',
  components: {'NewsCarousel' : NewsCarousel, 'Calendar': Calendar, 'cart': Cart, 'teaser' : Teaser },
  data () {
    return {

    }
  },
  methods: {
  	stringDate: function(dateStr) {
  		return dateStr.substr(6,2)+'/'+dateStr.substr(4,2)+'/'+dateStr.substr(0,4);
  	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	}
  },
    mounted: function () {
    },
    watch: {

    }
}
</script>

<style>
/*
.teaser {
	width: 100%;
    height: 50vw;
    max-height: 400px;
    background-position: right center;
    background-repeat: no-repeat;
    background: url(/static/dev/player.svg) no-repeat 120% 0% / auto 121%, url(/static/dev/wave.svg) no-repeat 0% 31% / 100%, linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #ec9c33 100%), linear-gradient(to right, #e04820 0%, #e76723 100%);
    
}
*/
</style>
