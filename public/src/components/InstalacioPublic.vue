<template>
    <transition name="fade">
	    <div>

			<div style="position: absolute;right: 20px;margin-top: -15px;"><ui-fab color="normal" icon="keyboard_backspace" @click="goBack()" size="small"></ui-fab></div>

			<h1 style="margin-bottom:0;">{{trinquet.nom}}</h1>
			
			<div class="contentFlex formulari">
				
				<div class="left50">
					<p><strong>{{$i18n.t('common.place')}}:</strong> {{trinquet.pob}}</p>
					<p><strong>{{$i18n.t('common.address')}}:</strong> {{trinquet.dir}}</p>
					<p><strong>{{$i18n.t('common.phone')}}:</strong> {{trinquet.tel}}</p>
				</div>
				
				<iframe class="map" :src="'/map.html?'+ trinquet.gps"></iframe>
				
			</div>

	    </div>
    </transition>
</template>

<script>

export default {
  	components: { },
	data () {
		return {
			trinquet:{
				cp: "",
				dir: "",
				gps: "",
				id: "",
				nom: "",
				pob: "",
				tel: ""
			}
		}
	},
	methods: {
		goBack:function(){
    		window.history.back();
		},
		getData: function(){
	        var vm = this;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('/trinquet/'+vm.$route.params.instalacioId, { cache: auth })
	        .then(function (response) {
	            vm.trinquet = response.data[0];
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		vm.getData();
	},
	created: function() {

	}
}
</script>

<style lang="less">

.map{
	border:0;
	width:100%;
	height:400px;
}

</style>