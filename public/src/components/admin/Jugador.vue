<template>
    <transition name="fade">
	    <div>

			<h1>Jugador</h1>

			<div class="contentFlex formulari">
			
				<div class="left50">
					
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
			            label="Dni/Nie"
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
			        ></ui-textbox>
			        <!-- :invalid="$store.getters.validate({string:jugador.email,type:'email'})" -->
		        	
					<ui-datepicker
		                :placeholder="$i18n.t('calendar.dateTip')"
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

					<!--div class="contentFlex intern">
						<div class="left50">
							<ui-switch v-model="jugador.actiu">Actiu</ui-switch>
						</div>
						<div class="left50">
	                		<ui-switch v-model="jugador.segur">Assegurat</ui-switch>
	                	</div>
					</div-->

					<ui-select
						has-search
						label="Club al que pertany"
						placeholder="Club al que pertany"
						:options="clubs"
						type="basic"
						@input="updateClub"
						v-model="clubName"
					></ui-select>

			        <!--ui-radio-group
			        	name="tipusfitxa"
						v-bind:data-id="jugador.id"
						:options="[{value:1,label:'Jugador Prof.'},{value:2,label:'Jug. Amateur'},{value:3,label:'Jutge/Home Bó'},{value:4,label:'Trinqueter'},{value:5,label:'Feridor'},{value:6,label:'Escolar'},{value:7,label:'Tècnic/Monitor'}]"
						v-model="jugador.tipusfitxa"
		            >Tipus de fitxa</ui-radio-group><br-->
		            
		            <!-- {{ jugador.fitxa }}--{{!$route.params.jugadorId}}-->
			        
			        <ui-checkbox-group
			        v-if="Array.isArray(jugador.fitxa)"
                	:options="jugadorOpcions"
                	v-model="jugador.fitxa"
            		>Fitxes</ui-checkbox-group><br/>
            		
					<ui-datepicker
						v-if="typeof jugador.dataactiu === 'object'"
		                :placeholder="$i18n.t('calendar.dateTip')"
		                :start-of-week="datePickerOptions.dow"
		                v-model="jugador.dataactiu"
		                :lang="datePickerOptions"
		            >Actiu fins al </ui-datepicker>

					<ui-datepicker
						v-if="typeof jugador.datasegur === 'object'"						
		                :placeholder="$i18n.t('calendar.dateTip')"
		                :start-of-week="datePickerOptions.dow"
		                v-model="jugador.datasegur"
		                :lang="datePickerOptions"
		            >Assegurat fins al </ui-datepicker>	

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
			      tipusfitxa: 0,
			      fitxa: null,
			      club: null,
			      datasegur: new Date(),
			      dataactiu: new Date()
		    },
		    jugadorOpcions:['Profesional','Amateur','Jutge','Trinqueter','Feridor','Escolar','Monitor'],
		    clubName:'',
		    clubs:[],
		    datePickerOptions: {
			  dow: Number(eval(this.$parent.$i18n.t('calendar.mondayFirst'))),
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
		updateClub:function(club) {
			var vm=this;
			vm.jugador.club = club.value;
			vm.clubName = club.label;
		},
		returnClubNamefromId: function(id) {
			var vm=this;
			var name;
			vm.clubs.forEach(function(club){
				if(id==club.value) {
					name = club.label;
				}
			});
			return name;
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
			var editant= vm.$route.params.jugadorId?true:false;
			
			/*console.log(editant?'editant':'noedit');
			if (!editant) { // o siga, que es nou
				var inexistent= await this.chkDNI();
				console.log(inexistent?'nota':'jatava')
				if (!inexistent) return false; // o siga que ja existeix
			}*/
			
	        vm.$http.post('/jugador/'+ (editant?vm.$route.params.jugadorId :'') , {...vm.jugador, 
		        ...{
		        	naixement: vm.jugador.naixement.toString('yyyyMMddHHmmss'),
		        	datasegur: vm.jugador.datasegur.toString('yyyyMMddHHmmss'),
		        	dataactiu: vm.jugador.dataactiu.toString('yyyyMMddHHmmss'),
		        	fitxa: vm.jugador.fitxa ? vm.jugador.fitxa.join(',') : ''
		        }
	        }).then(function (response) {
	        	if (response.status==409) return alert('Error, DNI existent');
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
	            vm.jugador.naixement = vm.jugador.naixement ? vm.parseTime(vm.jugador.naixement) : new Date();
	            vm.jugador.datasegur = vm.jugador.datasegur ? vm.parseTime(vm.jugador.datasegur) : new Date();
	            vm.jugador.dataactiu = vm.jugador.dataactiu ? vm.parseTime(vm.jugador.dataactiu) : new Date();
	            if (isNaN(vm.jugador.tipusfitxa)) vm.jugador.tipusfitxa = 0;
	            //if (!vm.jugador.fitxa) vm.jugador.fitxa = [];
	            console.log('split',vm.jugador.fitxa)
	            vm.jugador.fitxa= vm.jugador.fitxa ? vm.jugador.fitxa.split(',') : [];
	            //vm.jugador.fitxa.forEach( (e,idx)=> vm.jugador.fitxa[idx]= '"'+e+'"' )

		        /*vm.$http.get('/nomsclubs')
		        .then( function (response) {
		        	vm.clubs.push( { 'label': 'cap', 'value': 0 } ); // afegir un en blanc. a la api ja ho canvie a null
		        	response.data.forEach( (clb)=> vm.clubs.push( { 'label':clb.nom, 'value':clb.id } ) );
		            vm.clubName = vm.returnClubNamefromId(vm.jugador.club);
		        } )
		        .catch(function (error) {
		            console.log(error);
		        });*/
		        
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
			console.log(data);
			vm.jugador.nom= decodeURIComponent(escape(data.nom));
			vm.jugador.cognoms= decodeURIComponent(escape(data.cognoms));
			vm.jugador.club= data.club;
			vm.jugador.naixement= vm.parseTime(data.naixement);
			vm.jugador.email= data.email;
			vm.jugador.telefon= data.telefon;
			vm.jugador.dir= decodeURIComponent(escape(data.dir));
			vm.jugador.cp= data.cp;
			vm.jugador.poblacio= data.poblacio;
			vm.jugador.dni= data.dni;
			vm.jugador.sexe= data.sexe;
			vm.jugador.foto= data.foto;
			//vm.jugador.tipusfitxa= data.tipusfitxa;
			vm.jugador.fitxa= [];
			vm.jugador.dataactiu= vm.parseTime((new Date()).getFullYear()+'1231235959');
			vm.jugador.datasegur= vm.parseTime((new Date()).getFullYear()+'1231235959');
		}
		
        vm.$http.get('/nomsclubs')
        .then( function (response) {
        	vm.clubs.push( { 'label': 'cap', 'value': 0 } ); // afegir un en blanc. a la api ja ho canvie a null
        	response.data.forEach( (clb)=> vm.clubs.push( { 'label':clb.nom, 'value':clb.id } ) );
            vm.clubName = vm.returnClubNamefromId(vm.jugador.club);
        } )
        .catch(function (error) {
            console.log(error);
        });
		
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

.ui-radio__label-text { white-space: nowrap; }

</style>