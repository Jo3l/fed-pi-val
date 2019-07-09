<template>
	<div class="contentFlex formulari">
		<div class="left50">
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.wrong')"
	            label="Nom"
				type="text"
	            v-model="club.nom"
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="This field is required"
	            label="President"
				type="text"
	            v-model="club.president"
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="This field is required"
	            label="Secretari"
				type="text"
	            v-model="club.secretari"
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            :error="$i18n.t('common.wrong')"
	            label="Cif"
				type="text"
	            v-model="club.cif"
	            :invalid="$store.getters.validate({string:club.cif,type:'cif'})"
	        ></ui-textbox>
        	
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="This field is required"
	            label="Correu Electrònic"
				type="email"
	            v-model="club.email"
	        ></ui-textbox>
        	

			<ui-datepicker
                placeholder="$i18n.t('calendar.dateTip')"
                :start-of-week="datePickerOptions.dow"
                v-model="club.fundacio"
                v-if="typeof club.fundacio === 'object'"
                :lang="datePickerOptions"
            >Data Fundació</ui-datepicker>
        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="This field is required"
	            label="Telèfon"
				type="number"
	            v-model="club.telefon"
	        ></ui-textbox>
	        
			<ui-textbox
			    floating-label
	            autocomplete="off"
	            error="This field is required"
	            label="Adreça"
				type="text"
	            v-model="club.dir"
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
                v-model="club.cp"
            ></ui-select>

			<ui-textbox
			    floating-label
			    disabled
	            autocomplete="off"
	            error="This field is required"
	            label="Població"
				type="text"
	            v-model="club.poblacio"
	        ></ui-textbox>
	        
			<!--<img v-if="mapa==''" src="/static/img/mapsPlaceholder.jpg">
			<progressive-img v-else  :src="mapa" fallback="/static/img/mapsPlaceholder.jpg" />-->
	        
	        </div>
	        <div class="left50">
	        
			<img v-if="club.imatge==null" class="jugador" src="/static/img/shield.png" style="opacity: 0.15;">
			<progressive-img v-else class="jugador" :src="club.imatge" fallback="/static/img/shield.png"/>
			
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
		
		<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">{{$i18n.t('common.save')}}</ui-button>

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
		    club:{
			      nom: null,
			      fundacio: new Date(),
			      cif: null,
			      geoloc: null,
			      email: null,
			      telefon: null,
			      dir: null,
			      cp: null,
			      poblacio: null,
			      imatge: null,
				  president:null,
				  secretari:null
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
			var poblacio = vm.club.cp.split(" - ");
			vm.club.cp = poblacio[0];
			vm.club.poblacio = poblacio[1];
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
			
			if(document.querySelectorAll('.formulari .is-invalid:not(.is-disabled)').length>0) {
				document.querySelector('.formulari .is-invalid:not(.is-disabled) input').focus()
				return false;
			}
			
			var vm=this;
			vm.club.fundacio = vm.club.fundacio.toString('yyyy');
	        vm.$http.post('/club/'+ vm.$store.getters.userId , vm.club)
	        .then(function (response) {
	            vm.club.fundacio = vm.parseTime(vm.club.fundacio);
	            vm.$router.push({ path: `/gestio/club`});
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
			vm.club.imatge = '/static'+res.file;
		},
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/club/'+vm.$store.getters.userId, { cache: false })
	        .then(function (response) {
	            vm.club = response.data[0];
	            vm.club.fundacio = vm.parseTime(response.data[0].fundacio);

	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
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
		var vm=this;
		if(vm.$store.getters.isAuthenticated) vm.getData();
		
	},


}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

</style>
