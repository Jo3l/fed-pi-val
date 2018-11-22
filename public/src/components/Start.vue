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
    	
    	<NewsCarousel :pagina="-1"></NewsCarousel>
    	
		<Calendar type="slider"></Calendar>
		
		<products type="slider"></products>
		
		<div class="social">
				<vue-goodshare-facebook 
					title_social="Facebook"
				    has_counter
				    has_icon 
				></vue-goodshare-facebook>
				<vue-goodshare-twitter 
					title_social="Twitter"
				    has_icon 
				></vue-goodshare-twitter>
				<vue-goodshare-whatsapp 
					v-if="isMobileDevice()"
					title_social="WhatsApp"
				    has_icon 
				></vue-goodshare-whatsapp>
				<vue-goodshare-telegram 
					v-if="isMobileDevice()"
					title_social="Telegram"
				    has_icon 
				></vue-goodshare-telegram>
		</div>
			
    </div>
    </transition>
</template>

<script>

import NewsCarousel from './NewsCarousel.vue';
import Calendar from './Calendar.vue';
import Products from './shop/Products.vue';
import Teaser from './Teaser.vue';
import VueGoodshareFacebook from 'vue-goodshare/src/providers/Facebook.vue'
import VueGoodshareTwitter from 'vue-goodshare/src/providers/Twitter.vue'
import VueGoodshareWhatsapp from 'vue-goodshare/src/providers/WhatsApp.vue'
import VueGoodshareTelegram from 'vue-goodshare/src/providers/Telegram.vue'

export default {
  name: 'Start',
  components: {VueGoodshareFacebook,VueGoodshareTwitter,VueGoodshareWhatsapp,VueGoodshareTelegram,'NewsCarousel' : NewsCarousel, 'Calendar': Calendar, 'products': Products, 'teaser' : Teaser },
  data () {
    return {

    }
  },
  head : function() {
	return this.$route.meta;
  },
  methods: {
  	isMobileDevice: function() {
	    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
	},
  	stringDate: function(dateStr) {
  		return dateStr.substr(6,2)+'/'+dateStr.substr(4,2)+'/'+dateStr.substr(0,4);
  	},
  	loadingBar: function(data){
			this.$parent.$emit('loadingBar', data);
	}
  },
  created : function() {

  },
  mounted: function () {
	
  },
  watch: {

  }
}
</script>

<style lang="less">

#start{
	.social{
		margin-left:20px;
	}
}

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
