<template>
    <transition name="fade">
	    <div>

			<div style="position: absolute;right: 20px;margin-top: -15px;"><ui-fab color="normal" icon="keyboard_backspace" @click="goBack()" size="small"></ui-fab></div>

			<h1 style="margin-bottom:0;"><input v-model="trinquet.nom" /></h1>
			
			<div class="contentFlex formulari">
				
				<div class="left50">
					<p><strong>{{$i18n.t('common.place')}}:</strong> <input v-model="trinquet.pob" /></p>
					<p><strong>{{$i18n.t('common.address')}}:</strong> <input v-model="trinquet.dir" /></p>
					<p><strong>{{$i18n.t('common.phone')}}:</strong> <input v-model="trinquet.tel" /></p>
				</div>
				
				<div class="mapa">
					<iframe :src="'/map.html?'+ trinquet.gps + '&admin'"></iframe>
					<input v-model="trinquet.gps" placeholder="coordinades GPS"/>
				</div>				
			</div>
			
			<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">{{$i18n.t('common.save')}}</ui-button>

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
			},
			mapbox:{
				style: 'mapbox://styles/mapbox/light-v9',
				center: [-96, 37.8],
				zoom: 3,
				accessToken:'sk.eyJ1IjoiYWxzYW5hbiIsImEiOiJjam9pd2FqeG8wY2c1M3BwZmVodGZpN3ExIn0.Zq0TtD1l9B5Vl3GV6BFLKg',
				geolocateControl:{
				  show: true,
				  position: 'top-left'
				},
				scaleControl:{
				  show: true,
				  position: 'top-left'
				},
				fullscreenControl:{
				  show: true,
				  position: 'top-left'
				}
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
		setMap:function(map){
	    	var vm=this;
	    	vm.trinquet.gps = map.lat.toFixed(6)+','+map.lng.toFixed(6);
	    },
		saveForm: function() {
			
			if(document.querySelectorAll('.is-invalid:not(.is-disabled)').length>0) {
				document.querySelector('.is-invalid:not(.is-disabled) input').focus()
				return false;
			}
		
			var vm=this;

	        vm.$http.post('/trinquet/'+ vm.$route.params.instalacioId , vm.trinquet )
	        // [{"id":"11","nom":"Albal, Front\u00f3 Cooperativa","dir":"Avda Corts Valencianes n\u00ba5","cp":"46470","pob":"Albal","tel":".","gps":"39.397163,-0.412455"}]
	        .then(function (response) {
	        	alert('ok')
	            //vm.$router.push({ path: `/admin/jugadors/`});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},	    
	},
	mounted: function () {
		var vm=this;
		vm.getData();
		vm.$eventHub.$on('setMapData', vm.setMap);
	},
	created: function() {

	}
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.mapa{
	border:0;
	width:100%;
	height:400px;
	iframe {
		width:100%;
		height:400px;
		border: 1px solid @fedcolor;
	}
}
textarea, input {
    width: 100%;
    font-family: 'Rambla', cursive;
    display: block;
    font-size: 2em;
    border: none;
    border-bottom: 1px dashed #ccc;
    margin-bottom:0.5em;
    min-height: 42px;
}


</style>