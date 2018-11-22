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
			            error="Camp de text no correcte"
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
		        	
		        	<label>Data Naixement
					<vue-datepicker-local v-model="jugador.naixement" :local="datePickerOptions" format="DD-MM-YYYY" v-if="typeof jugador.naixement === 'object'"></vue-datepicker-local>
					</label>
		        
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
import VueDatepickerLocal from 'vue-datepicker-local'
import { mapGetters, mapActions } from 'vuex'

export default {
  	components: { VueDatepickerLocal,'vue-core-image-upload': VueCoreImageUpload},
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
				yearSuffix: '',
				monthsHead: this.$parent.$i18n.t('calendar.months'),
				dow: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
        		hourTip: this.$parent.$i18n.t('calendar.hourTip'),
    			minuteTip: this.$parent.$i18n.t('calendar.minuteTip'),
        		secondTip: this.$parent.$i18n.t('calendar.secondTip'),
        		months: this.$parent.$i18n.t('calendar.monthsShort'),
    			weeks: this.$parent.$i18n.t('calendar.weekShort')
			},
		}
	},
	methods: {
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
		saveForm: function() {
			
			if(document.querySelectorAll('.is-invalid:not(.is-disabled)').length>0) {
				document.querySelector('.is-invalid:not(.is-disabled) input').focus()
				return false;
			}
		
			var vm=this;
			vm.jugador.naixement = vm.jugador.naixement.toString('yyyyMMddHHmmss');
	        vm.$http.post('/jugador/'+ (vm.$route.params.jugadorId?vm.$route.params.jugadorId :'') , vm.jugador)
	        .then(function (response) {
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