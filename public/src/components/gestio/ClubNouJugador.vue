<template>

	<div class="contentFlex formulari nou-jugador">

		<div class="left50">
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.required')"
	            label="Nom"
				type="text"
	            v-model="noujugador.nom"
	            :invalid="$store.getters.validate({string:noujugador.nom,type:'not-null'})"
	        ></ui-textbox>

			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.required')"
	            label="Cognoms"
				type="text"
	            v-model="noujugador.cognoms"
	            :invalid="$store.getters.validate({string:noujugador.cognoms,type:'not-null'})"
	        ></ui-textbox>
	        
	        <br>
	        <ui-radio-group
                name="sexe"
                :error="$i18n.t('common.required')"
                :options="[{label: 'Home', value: 'h'},{label: 'Dona', value: 'd'}]"
                v-model="noujugador.sexe"
                :invalid="$store.getters.validate({string:noujugador.sexe,type:'not-null'})"
            >Sexe</ui-radio-group><br>
            
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.requiredmore')"
	            label="Dni"
				type="text"
	            v-model="noujugador.dni"
	            :invalid="$store.getters.validate({string:noujugador.dni,type:'dni'})"
	            required
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.requiredmore')"
	            label="Correu Electrònic"
				type="email"
	            v-model="noujugador.email"
	            :invalid="$store.getters.validate({string:noujugador.email,type:'email'})"
	            required
	        ></ui-textbox>
            <br>
			<ui-datepicker
                :placeholder="$i18n.t('calendar.dateTip')"
                :start-of-week="datePickerOptions.dow"
                v-model="noujugador.naixement"
                :lang="datePickerOptions"
                required
            >Data Naixement</ui-datepicker>
	            
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="Camp de text no correcte"
	            label="Telèfon"
				type="number"
	            v-model="noujugador.telefon"
	            :invalid="$store.getters.validate({string:noujugador.telefon,type:'not-null'})"
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="Camp de text no correcte"
	            label="Adreça"
				type="text"
	            v-model="noujugador.dir"
	            :invalid="$store.getters.validate({string:noujugador.dir,type:'not-null'})"
	        ></ui-textbox>
	        <br>
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
                v-model="noujugador.cp"
            ></ui-select>
	        
			<ui-textbox
			    floating-label
			    disabled
	            autocomplete="off"
	            error="Camp de text no correcte"
	            label="Població"
				type="text"
	            v-model="noujugador.poblacio"
	            :invalid="$store.getters.validate({string:noujugador.poblacio,type:'not-null'})"
	        ></ui-textbox>

			<!--<img v-if="mapa==''" src="/static/img/mapsPlaceholder.jpg">
			<progressive-img v-else  :src="mapa" fallback="/static/img/mapsPlaceholder.jpg" />-->
	        
	        </div>
	        <div class="left50">
	        
			<img v-if="noujugador.imatge==null" class="jugador" src="/static/img/blank-user.jpg" style="opacity: 0.15;">
			<progressive-img v-else class="jugador" :src="noujugador.imatge" fallback="/static/img/blank-user.jpg"/>
			
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
		
		<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">Demanar registre</ui-button>

	</div>

</template>

<script>
import VueCoreImageUpload from 'vue-core-image-upload'

export default {
  	components: {'vue-core-image-upload': VueCoreImageUpload},
	data () {
		return {
			error:{},
		    lloc:'', 
		    telefon:'', 
			loading:false,
		    noResults:false,
			poblacions: [],
			mapa: null,
			club:{},
		    noujugador:{
			      nom: '',
			      cognoms: '',
			      dni: '',
			      sexe: '',
			      naixement: new Date,
			      dir: null,
			      cp: 0,
			      poblacio: null,
			      tel: null,
			      email: null,
			      imatge: null,
			      club: null
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
			}
		}
	},
	methods: {
		closeModal: function(ref) {
            this.$refs[ref].close();
        },
	    closeAllModals: function() {
	    	var vm=this;
		    for (var key in vm.$refs) {
	        	vm.$refs[key].close();
	        }
	    },
		setPoblacio:function(){
			var vm = this;
			var poblacio = vm.noujugador.cp.split(" - ");
			vm.noujugador.cp = poblacio[0];
			vm.noujugador.poblacio = poblacio[1];
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
			if(document.querySelectorAll('.nou-jugador .is-invalid:not(.is-disabled)').length>0) {
				document.querySelector('.nou-jugador .is-invalid:not(.is-disabled) input').focus()
				return false;
			}
			var vm=this;
			console.log(vm.noujugador.naixement)
			vm.noujugador.naixement = vm.noujugador.naixement.toString('yyyyMMdd');
	        vm.$http.post('/jugador/registre' , vm.noujugador)
	        .then(function (response) {
	            alert('ok. Sol·licitud enviada... ');
	            //vm.$router.push({ path: `/gestio/club`});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		emailValid: function(email) {
			return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
		},

		getPhoto:function(res) {
			var vm=this;
			vm.noujugador.imatge = '/static'+res.file;
		},
		getData: function(){
	        /*var vm = this;
	        
	        vm.$http.get('/club/'+vm.$store.getters.userId, { cache: false })
	        .then(function (response) {
	            vm.club = response.data[0];
	            vm.club.fundacio = vm.parseTime(response.data[0].fundacio);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });*/
	        alert('NOTHING HERE');
		},
		parseTime: function(time) {
			if(typeof time !== 'string') return time;
			
			var year = time.substring(0, 4);
			var month = time.substring(4, 6);
			var day = time.substring(6, 8);
			var hour = time.substring(8, 10);
			var minute = time.substring(10, 12);
			var second = time.substring(12, 14);
			
			return new Date(year, month-1, day, hour, minute, second);	
			
		},

	},
	mounted: function () {
		var vm= this;
		vm.noujugador.club= vm.$store.getters.userId;
	},


}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

</style>
