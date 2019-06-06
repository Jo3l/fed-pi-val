<template>
    <transition name="fade">
		<div class="success">
			
			<pre>{{datos}}</pre>

		</div>

    </transition>
</template>

<script>


export default {
  name: 'Success',
  components: { },
  data () {
    return {
		datos:''
    }
  },
  methods: {

  },
  mounted: function() {
	  	var vm=this;
	  	
	  	
		vm.$http.post('/pagat', vm.$route.query)
        .then(function (response) {
        	
        	vm.datos=response.data;
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
  }

}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

</style>