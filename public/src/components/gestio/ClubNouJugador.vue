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
				<template v-if="!nofoto">
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
				</template>
			</div>
			
			<br/>
			<details ref="mesinfo2">
				<summary><ui-button color="fedpival" @click="$refs['mesinfo2'].open=!$refs['mesinfo2'].open;">Cl&aacute;usula informativa</ui-button> 
				<ui-alert type="warning" v-show="!acceptaClausula" :dismissible="false">Ha d'acceptar primer la clàusula per demanar el registre...</ui-alert>
				</summary><div style="margin:0 1em; padding:1em; box-shadow:0 0 5px 2px black;">

<table width="605">
<tbody>
<tr>
<td width="605">
<p><strong>&iquest;Qui&eacute;n es el responsable del tratamiento de sus datos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Identidad:</strong> FEDERACION DE PILOTA VALENCIANA con <strong>CIF:</strong> G-46374351</p>
<p><strong>Direcci&oacute;n postal:</strong>&nbsp; C/ Marqu&eacute;s de San Juan, 32 baix B - 46015 &ndash; Valencia</p>
<p><strong>Tel&eacute;fono:</strong>&nbsp; 963 74 95 58&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Correo electr&oacute;nico:</strong> <a href="mailto:secretari@fedpival.es">secretari@fedpival.es</a></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Delegado de Protecci&oacute;n de Datos: </strong>MEDINALEON CONSULTORES ASOCIADOS SL</p>
<p><strong>Contacto DPD: </strong>Pedro Medina&nbsp;&nbsp; <strong>Correo electr&oacute;nico:</strong> <a href="mailto:fedpival@dpddigital.com">dpd@ml-asociados.es</a> &nbsp;</p>
<p><strong>Canal RGPD: </strong><a href="https://fedpival-canaletico.appcore.es/"><strong>https://fedpival-canaletico.appcore.es/</strong></a></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;Con qu&eacute; finalidad tratamos sus datos personales?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>tratamos datos de car&aacute;cter personal con la finalidad de tramitar las solicitudes de alta en la federaci&oacute;n recibidas, responder a las consultas realizadas y llevar a cabo la prestaci&oacute;n de servicio acordada. Asimismo, se podr&aacute; tomar y utilizar im&aacute;genes para publicitar las actividades de la federaci&oacute;n a trav&eacute;s de la p&aacute;gina web de la misma y las redes sociales.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>C&oacute;mo dejar de recibir comunicaciones comerciales</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>De conformidad con lo establecido en cumplimento de la legislaci&oacute;n vigente en materia de protecci&oacute;n de datos, en el caso de que el usuario desee dejar de recibir comunicaciones comerciales o promocionales puede solicitar la baja del servicio enviando un correo electr&oacute;nico a&nbsp; <a href="mailto:secretari@fedpival.es">secretari@fedpival.es</a></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;Por cu&aacute;nto tiempo conservaremos sus datos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>Mientras se mantenga la relaci&oacute;n administrativa y no se solicite su supresi&oacute;n por el interesado. Las im&aacute;genes que se incorporen a redes sociales se mantendr&aacute;n hasta que se solicite su cancelaci&oacute;n.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;Cu&aacute;l es la legitimaci&oacute;n para el tratamiento de sus datos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>La base legal para el tratamiento de sus datos es el consentimiento del interesado o de los tutores legales del menor, en cualquier caso, el tutor firmante declara haber obtenido el consentimiento del otro. El uso de im&aacute;genes de los participantes es obligatorio por lo que no podr&aacute; participar si no acepta este uso.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;A qu&eacute; destinatarios se comunicar&aacute;n sus datos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>Los datos se comunicar&aacute;n a entidades colaboradoras necesarias para llevar a cabo la prestaci&oacute;n del servicio.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;Cu&aacute;les son sus derechos cuando nos facilita sus datos? </strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>Cualquier persona tiene derecho a obtener confirmaci&oacute;n sobre si estamos tratando datos personales que les conciernan, o no. Las personas interesadas tienen derecho a acceder a sus datos personales, as&iacute; como a solicitar la rectificaci&oacute;n de los datos inexactos o, en su caso, solicitar su supresi&oacute;n cuando, entre otros motivos, los datos ya no sean necesarios para los fines que fueron recogidos. En determinadas circunstancias y por motivos relacionados con su situaci&oacute;n particular, los interesados podr&aacute;n oponerse al tratamiento de sus datos. Dejaremos de tratar los datos, salvo por motivos leg&iacute;timos imperiosos, o el ejercicio o la defensa de posibles reclamaciones.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;C&oacute;mo ejercer sus derechos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>Pueden ejercer sus derechos remitiendo escrito, adjuntando copia de documento oficial que le identifique y concretando el derecho o derechos que desea ejercer, al Delegado de Protecci&oacute;n de Datos</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Canal RGPD: </strong><a href="https://fedpival-canaletico.appcore.es/"><strong>https://fedpival-canaletico.appcore.es/</strong></a></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>&iquest;C&oacute;mo hemos obtenido sus datos?</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>Los datos personales que tratamos en proceden del interesado o su representante legal</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Obtenemos el consentimiento cuando:</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p>El interesado cumplimenta un formulario en formato papel o electr&oacute;nico.</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Las categor&iacute;as de datos que se tratan son:</strong></p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Datos identificativos:</strong> nombre y apellidos, DNI</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Datos de contacto:</strong> tel&eacute;fono, direcci&oacute;n postal, Correo electr&oacute;nico</p>
</td>
</tr>
<tr>
<td width="605">
<p><strong>Datos de caracter&iacute;sticas personales: </strong>No se tratan datos especialmente protegidos.</p>
</td>
</tr>
<tr>
<td width="605">
<p><ui-checkbox v-model="acceptaClausula"> Declaro que he le&iacute;do y acepto el contenido de la cl&aacute;usula informativa.</ui-checkbox></p>
<!--
<p>☐SI ☐NO Acepto recibir comunicaciones comerciales sobre productos o servicios.</p>
<p>☐SI ☐NO Acepto el uso de fotos de m&iacute; como jugador del equipo para redes sociales, promoci&oacute;n, etc. Con ocasi&oacute;n de eventos deportivos.</p>
<p>☐ D./D&ntilde;a________________________________________________________con NIF______________ declaro como tutor legal del menor______________________________ que he le&iacute;do y acepto el contenido de la cl&aacute;usula informativa</p>
<p>Firma:</p>
<p>&nbsp;</p>
-->
</td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
				
			</div></details>
			<hr/>
		
			<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()" :disabled="!acceptaClausula">Demanar registre</ui-button>

	</div>

</template>

<script>
import VueCoreImageUpload from 'vue-core-image-upload'

export default {
  	components: {'vue-core-image-upload': VueCoreImageUpload},
  	props: ['nofoto'],
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
			acceptaClausula:false,
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
			//console.log(vm.noujugador.naixement)
			//vm.noujugador.naixement = vm.noujugador.naixement.toString('yyyyMMdd');
	        vm.$http.post('/jugador/registre' , {...vm.noujugador, ...{naixement: vm.noujugador.naixement.toString('yyyyMMddHHmmss')}})
	        .then(function (response) {
	            alert('ok. Sol·licitud enviada... ');
	            vm.$router.push({ path: `/gestio/club`});
	        })
	        .catch(function (error) {
	            //console.log(error);
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
