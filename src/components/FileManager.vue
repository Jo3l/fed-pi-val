<template>
    <transition name="fade">
	    <div id="fileManager">
		
		

	    </div>
    </transition>
</template>

<script>

export default {
  	components: {  },
	data () {
		return {
			show:false,
		}
	},
	methods: {
		getData: function(apiUrl) {
	        var vm = this;
	        this.$http.get(apiUrl)
	        .then(function (response) {
	            vm.news = response.data[0];
	            vm.publishedDate = vm.fixDate(response.data[0].alta);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
	},
	mounted: function () {
		this.setOrder();
	},
	watch: {
	
	}
}
</script>


<style lang="less">




</style>
