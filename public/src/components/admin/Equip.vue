<template>
    <transition name="fade">
	    <div>

			<h1>Equip</h1>
			
			<div class="contentFlex formulari">
			
				<div class="left50">
					
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Nom"
						type="text"
			            v-model="equip.nom"
			        ></ui-textbox>

					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Modalitat"
						type="text"
			            v-model="equip.modalitat"
			        ></ui-textbox>
			        
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Categoria"
						type="text"
			            v-model="equip.categoria"
			        ></ui-textbox>
			        
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Competició"
						type="text"
			            v-model="equip.competicio"
			        ></ui-textbox>
			        
			    	<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">Desar</ui-button>

				</div>
				
			</div>
	    </div>
    </transition>
</template>

<script>

export default {
  	components: {},
	data () {
		return {
			loading:false,
		    noResults:false,
			poblacions: [],
		    equip:{
			    nom: null,
			    modalitat: null,
			    categoria: null,
			    competicio:null,
				club:null,
		    },

		}
	},
	methods: {
		saveForm: function() {
			var vm=this;
	        vm.$http.post('/equip/'+ (vm.$route.params.equipId?vm.$route.params.equipId :'') , vm.equip)
	        .then(function (response) {
	            vm.$router.push({ path: `/admin/club/`+$route.params.equipId});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/equip/'+vm.$route.params.equipId, { cache: false })
	        .then(function (response) {
	            vm.equip = response.data[0];
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		if(vm.$route.params.equipId >= 0) vm.getData();
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
	}
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";



</style>