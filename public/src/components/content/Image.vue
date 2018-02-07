<template>
    <transition name="fade">
	    <div>
			<img class="teaserImg wide" v-if="element.tipus == 'I' && element.url" :src="element.url">
			
			<div class="buttonContainer" v-if="element.tipus == 'I'">
            	<ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', element, element.url, 'img')">{{$i18n.t('image.uploadImages')}}</ui-button>
				<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
			</div>
	    </div>
    </transition>
</template>

<script>

export default {
	name: 'Clubs',
  	components: {},
  	props: ['element'],
	data () {
		return {
		    
		}
	},
	methods: {
		getData: function(apiUrl) {
	
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.$store.news = response.data[0];
	            vm.publishedDate = vm.fixDate(response.data[0].alta);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
	  	fixDate: function (date) { 
		  return Date.parse(date.substr(0, 4) + '.' + date.substr(4, 2) + '.' + date.substr(6,2) + ' ' + date.substr(8,2) + ':' + date.substr(10,2) + ':' + date.substr(12) ).toString("d/M/yyyy");
		},
	   	scrollToTop: function (scrollDuration) {
		const   scrollHeight = window.scrollY,
		        scrollStep = Math.PI / ( scrollDuration / 15 ),
		        cosParameter = scrollHeight / 2;
		var     scrollCount = 0,
		        scrollMargin,
		        scrollInterval = setInterval( function() {
		            if ( window.scrollY != 0 ) {
		                scrollCount = scrollCount + 1;  
		                scrollMargin = cosParameter - cosParameter * Math.cos( scrollCount * scrollStep );
		                window.scrollTo( 0, ( scrollHeight - scrollMargin ) );
		            } 
		            else clearInterval(scrollInterval); 
		        }, 15 );
		}
	},
	mounted: function () {

	},
	watch: {
	
	}
}
</script>

<style lang="less">

.clubs {

}

</style>