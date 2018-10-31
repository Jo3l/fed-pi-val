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


			        <ui-select
		                disable-filter
		                has-search
		                label="Modalitat"
		                placeholder="Selecciona Modalitat"
						@dropdown-close="onQueryChange"
		                :options="modalitatsTxt"
		                @query-change="onQueryChange"
		                v-model="modalitatDefault"
		            ></ui-select>
		            

					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Categoria"
						type="text"
			            v-model="equip.categoria"
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
			modalitats:[],
			modalitatsTxt:[],
			claus:{text: 'text',  value: 'value'},
			modalitatDefault:'',
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
		onQueryChange: function() {
        	var vm = this;
	        vm.modalitats.forEach(function(element) {
	        	console.log(element, vm.modalitatDefault)
				  if(element.nom == vm.modalitatDefault) vm.equip.modalitat = element.id;
			});
        },
		saveForm: function() {
			var vm=this;
			vm.equip.club = vm.$route.params.clubId;
	        vm.$http.post('/equip/'+ (vm.$route.params.equipId?vm.$route.params.equipId :'') , vm.equip)
	        .then(function (response) {
	            vm.$router.push({ path: `/admin/club/`+vm.equip.club});
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
	            
		        vm.$http.get('/modalitats')
		        .then(function (response) {
		        	
		        	var mod=[];
		        	
		        	response.data.forEach(function(element) {
					  mod.push(element.nom);
					  if(vm.equip.modalitat == element.id) vm.modalitatDefault = element.nom;
					});
		        	
		        	vm.modalitats = response.data;
		            vm.modalitatsTxt = mod;
		        })
		        .catch(function (error) {
		            console.log(error);
		        });
	            
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