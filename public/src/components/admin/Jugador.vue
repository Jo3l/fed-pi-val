<template>
    <transition name="fade">
	    <div>

			<h1>Jugador</h1>

			<div class="contentFlex formulari">
			
				<div class="left50">
					
					<div class="contentFlex intern">
						<div class="left50">
							<ui-switch v-model="jugador.actiu">Actiu</ui-switch>
						</div>
						<div class="left50">
	                		<ui-switch v-model="jugador.segur">Assegurat</ui-switch>
	                	</div>
					</div>
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Nom"
						type="text"
			            v-model="jugador.nom"
			            required
			        ></ui-textbox>

					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Cognoms"
						type="text"
			            v-model="jugador.cognoms"
			            required
			        ></ui-textbox>
			        
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Format incorrecte"
			            label="Dni"
						type="text"
			            v-model="jugador.dni"
			            :invalid="$store.getters.validate({string:jugador.dni,type:'dni'})"
			        ></ui-textbox>

			        <ui-radio-group
		                name="sexe"
		                error="Sel·lecció necesària"
		                :options="[{label: 'Home', value: 'h'},{label: 'Dona', value: 'd'}]"
		                v-model="jugador.sexe"
		                :invalid="$store.getters.validate({string:jugador.sexe,type:'not-null'})"
		            >Sexe</ui-radio-group><br>
			       
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Num. Llicència"
						type="number"
			            v-model="jugador.numsoci"
			            required
			        ></ui-textbox>
		        	
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Correu Electrònic"
						type="email"
			            v-model="jugador.email"
			            :invalid="$store.getters.validate({string:jugador.email,type:'email'})"
			        ></ui-textbox>
		        	
					<ui-datepicker
						v-if="typeof jugador.naixement === 'object'"
		                placeholder="$i18n.t('calendar.dateTip')"
		                :start-of-week="datePickerOptions.dow"
		                v-model="jugador.naixement"
		                :lang="datePickerOptions"
		            >Data Naixement</ui-datepicker>
	            
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Telèfon"
						type="number"
			            v-model="jugador.telefon"
			        ></ui-textbox>
			        
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Adreça"
						type="text"
			            v-model="jugador.dir"
			        ></ui-textbox>
			        
			        <ui-select
		                disable-filter
		                has-search
		                label="Codi Postal"
		                placeholder="Busca la població a partir del codi postal"
		                search-placeholder="Escriu el codi postal"
						@dropdown-close="setPoblacio"
		                :loading="loading"
		                :no-results="noResults"
		                :options="poblacions"
		                @query-change="onQueryChange"
		                v-model="jugador.cp"
		                
		            ></ui-select>
			        
					<ui-textbox
					    floating-label
					    disabled
			            autocomplete="off"
			            error="Camp de text no correcte"
			            label="Població"
						type="text"
			            v-model="jugador.poblacio"
			        ></ui-textbox>
			        
			    	<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">{{$i18n.t('common.save')}}</ui-button>

				</div>
				<div class="left50">
					<img v-if="jugador.foto==null" class="jugador" src="/static/img/blank-user.jpg">
					<progressive-img v-else class="jugador" :src="jugador.foto" fallback="/static/img/blank-user.jpg" />
					
					<vue-core-image-upload
					    :text="$i18n.t('image.uploadAndCut')"
					    class="uploader"
						crop="local"
						cropRatio="1:1"
						compress="50"
					    url="/api/static/uploadimgjugador"
						@imageuploaded="getPhoto"
					    :data="{do:'uploadimgjugador'}"
					    extensions="jpg,jpeg"
					    inputAccept	="image/jpg,image/jpeg"
					>
					</vue-core-image-upload>
					
				</div>
				
			</div>
	    </div>
    </transition>
</template>

<script>

import VueCoreImageUpload from 'vue-core-image-upload'
import { mapGetters, mapActions } from 'vuex'

export default {
  	components: { 'vue-core-image-upload': VueCoreImageUpload},
	data () {
		return {
			loading:false,
		    noResults:false,
			poblacions: [],
		    jugador:{
			      nom: null,
			      actiu: null,
			      segur: null,
			      naixement: new Date(),
			      dni: null,
			      numsoci: null,
			      email: null,
			      telefon: null,
			      dir: null,
			      cp: null,
			      poblacio: null,
			      foto: null,
		    },
		    datePickerOptions: {
			  dow: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
			  months: {
			    full: this.$parent.$i18n.t('calendar.months'),
			    abbreviated: this.$parent.$i18n.t('calendar.monthsShort')
			  },
			  days: {
			    full: this.$parent.$i18n.t('calendar.weekLong'),
			    abbreviated: this.$parent.$i18n.t('calendar.weekShort'),
			    initials: this.$parent.$i18n.t('calendar.weekInitials')
			  }
			},
		}
	},
	methods: {
		chkDNI: function() {
			// comprovació que siga jugador nou i que el dni no existisca ja
			var vm= this;
			
			vm.$http.get('/jugador/search/'+vm.jugador.dni)
			.then( (r)=> {
				var inexistent= ( !r.data || r.data.length==0 );
				console.log(r.data.length,inexistent)
				if (!inexistent) r= r.data[0].id+': '+r.data[0].nom+' '+r.data[0].cognoms+' ('+r.data[0].dni+') Soci:'+r.data[0].numsoci;
				if (!inexistent) alert("S'ha trobat un altre jugador existent amb el mateix DNI\n"+r);
				return Promise.resolve(inexistent)
				//return inexistent;
			})
			return Promise.reject();
		},
		setPoblacio:function(){
			var vm = this;
			var poblacio = vm.jugador.cp.split(" - ");
			vm.jugador.cp = poblacio[0];
			vm.jugador.poblacio = poblacio[1];
		},
		onQueryChange: function(query) {
            if (query.length < 3) {
                return;
            }
            
        	var vm = this;
	        vm.loading=true;
	        
	        
	        vm.$http.get('/postal/search/'+query, { cache: false })
	        .then(function (response) {
	        	var pob = [];
	        	
	        	for (var key = 0; key < response.data.length; key++){
				  pob.push(response.data[key].codipostal+" - "+response.data[key].poblacio);
				}
	            vm.poblacions = pob;
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
            
        },
		saveForm: async function() {
			
			if(document.querySelectorAll('.is-invalid:not(.is-disabled)').length>0) {
				document.querySelector('.is-invalid:not(.is-disabled) input').focus()
				return false;
			}
		
			var vm=this;
			vm.jugador.naixement = vm.jugador.naixement.toString('yyyyMMddHHmmss');
			var editant= vm.$route.params.jugadorId?true:false;
			/*console.log(editant?'editant':'noedit');
			if (!editant) { // o siga, que és nou
				var inexistent= await this.chkDNI();
				console.log(inexistent?'nota':'jatava')
				if (!inexistent) return false; // o siga que ja existeix
			}*/
	        vm.$http.post('/jugador/'+ (editant?vm.$route.params.jugadorId :'') , vm.jugador)
	        .then(function (response) {
	        	if (response.status==200) return alert('Error, DNI existent');
	        	console.log(response);
	        	return;
	            //vm.jugador = response.data[0];
	            vm.jugador.naixement = vm.parseTime(vm.jugador.naixement);
	            vm.$router.push({ path: `/admin/jugadors/`});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		parseTime: function(time) {
			
			var str = time;
			var year = str.substring(0, 4);
			var month = str.substring(4, 6);
			var day = str.substring(6, 8);
			var hour = str.substring(8, 10);
			var minute = str.substring(10, 12);
			var second = str.substring(12, 14);
			
			return new Date(year, month-1, day, hour, minute, second);	
			
		},
		getPhoto:function(res) {
			var vm=this;
			vm.jugador.foto = '/static'+res.file;
		},
	  	remove:function(row) {
			var vm=this;

			vm.$http.delete('/jugador/'+row.id)
	        .then(function (response) {
	        	
	            vm.getData('jugador');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/jugador/'+vm.$route.params.jugadorId, { cache: false })
	        .then(function (response) {
	            vm.jugador = response.data[0];
	            vm.jugador.naixement = vm.parseTime(vm.jugador.naixement);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		if(vm.$route.params.jugadorId >= 0) vm.getData();
		var predefined= location.search.substring(1);
		if (predefined) {
			var data= JSON.parse(atob(unescape(predefined)));
			vm.jugador.nom= data.nom;
			vm.jugador.cognoms= data.cognoms;
			vm.jugador.naixement= data.naixement;
			vm.jugador.email= data.email;
			vm.jugador.telefon= data.telefon;
			vm.jugador.dir= data.dir;
			vm.jugador.cp= data.cp;
			vm.jugador.poblacio= data.poblacio;
			vm.jugador.dni= data.dni;
			vm.jugador.sexe= data.sexe;
			vm.jugador.foto= data.foto;
		}
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
	},
	computed: {
	    ...mapGetters({
	    	validate:'validate'
	    })
	}
}
</script>

<style lang="less">



</style>