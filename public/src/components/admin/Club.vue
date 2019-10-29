<template>
    <transition name="fade">
	    <div>

			<h1 style="margin-bottom:0;">Club</h1>
			
            <ui-tabs type="text">
                <ui-tab title="Dades Club">
                    
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
					        
							<img v-if="club.imatge==null" class="jugador" src="/static/img/shield.png">
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
						
						<ui-button color="saveForm" icon="security" size="small" type="secondary" title="Només es pot enviar amb un email correcte especificat" :class="emailValid(club.email)?'':'is-disabled'" v-if="club.id" @click="sendPwd()">{{$i18n.t('common.sendPwd')}} <span v-if="!validPwd()"> &nbsp; per primera vegada</span></ui-button>
						
						<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm()">{{$i18n.t('common.save')}}</ui-button>
		
					</div>
                    
                </ui-tab>

                <ui-tab title="Equips">
                	<span class="button-right" v-if="false">
						<ui-button icon="add_circle_outline" icon-position="left" size="big" @click="edit({id:''})">Afegir Equip</ui-button>
					</span>
					<br>
					<div class="vuetableContainer">
						<tablerone :tableList="list" :tableColumns="columns">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions" v-if="false">
									<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="edit(props.row)">Editar</ui-button>
									<ui-button color="red" icon="delete" icon-position="left" size="small" type="secondary" @click="remove(props.row)">Borrar</ui-button>
								</td>
							</template>
						</tablerone>
						<paginate
						    :page-count="Math.ceil(list.total / list.per_page)"
							:clickHandler="clickCallback"
							:page-range="2"
		    				:margin-pages="0"
						    :prev-text="$i18n.t('common.prev')"
						    :next-text="$i18n.t('common.next')"
						    :container-class="'pagination'"
						    :page-class="'page-item'">
						</paginate>
					</div>
                </ui-tab>

                <ui-tab title="Accés com a club" v-if="club.id && club.pwd/*validPwd()*/">
                	
					
						<br>
						<p style="text-align:center;">
							Per a accedir al panel d'administració com a club, cal fer click amb el botó de la dreta del ratolí sobre el botó i obrir l'enllaç a <u>una finestra d'incògnit</u> i així no perdre la sessió d'administració.
							<br><br>
							<a target="_blank" :href="'/login#'+tempPwd()"><ui-button icon="vpn_key" icon-position="left" size="big">Accedir club</ui-button></a>
						</p>
						

                </ui-tab>

            </ui-tabs>
			
	    </div>
    </transition>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import VueCoreImageUpload from 'vue-core-image-upload'

import VuePellEditor from 'vue-pell-editor'
import VuePellEditorConfig from '../../config/pelleditor'
import Table from '../custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
  	components: { VuePellEditor, 'vue-core-image-upload': VueCoreImageUpload, 'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    list:{},
		    columns:[
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            }
	        ],
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
				  secretari:null,
				  json:null,
				  pwd: null
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
			},
			editorOptions: VuePellEditorConfig(this.openModal),
		}
	},
	methods: {
	    clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getEquips('equipsdeclub', pageNum);
	    },
	  	edit:function(row) {
	    	this.$router.push({ path: `/admin/club/`+ this.club.id +`/equip/`+row.id });
	    },
	  	remove:function(row) {
			var vm=this;

			vm.$http.post('/equip/', {'delete_id': row.id})
	        .then(function (response) {
	        	
	            vm.getEquips('equipsdeclub');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
		getGeo:function(){
		var vm=this;
		var apiKey="AIzaSyAjtbR2DWMqbb7xHU-fUJQjPZexdEc9hYE";
		var sizeWidth="400";
		var sizeHeight="300";
		var zoom="13";
		var mapType="roadmap"
		vm.mapa= "https://maps.googleapis.com/maps/api/staticmap?maptype="+mapType+"&size="+sizeWidth+"x"+sizeHeight+"&zoom="+zoom+"&key="+apiKey+"&style=feature:all|saturation:-100|gamma:0.5&markers=label:O%7C'"+encodeURI(vm.club.dir+', '+vm.club.cp+', '+vm.club.poblacio);
		
		//https://maps.googleapis.com/maps/api/staticmap?center=ball%20de%20la%20carxofa%20algemesi%20valencia&zoom=15&size=800x400&maptype=roadmap&format=png&key=&scale=1&language=val
		},
		setPoblacio:function(){
			var vm = this;
			var poblacio = vm.club.cp.split(" - ");
			vm.club.cp = poblacio[0];
			vm.club.poblacio = poblacio[1];
			vm.getGeo();
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
			vm.club.fundacio = vm.club.fundacio.toString('yyyy');
	        vm.$http.post('/club/'+ (vm.$route.params.clubId?vm.$route.params.clubId :'') , vm.club)
	        .then(function (response) {
	            vm.club.fundacio = vm.parseTime(vm.club.fundacio);
	            vm.$router.push({ path: `/admin/clubs/`});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		emailValid: function(email) {
			return /^\w+([\.\+_-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
		},
		sendPwd: function() {
			var vm= this;
			vm.$http.post('/pwd/',{"club":vm.club.id,"email":vm.club.email})
			.then( (response)=> { if(response.data.result=='ok') alert("correu enviat"); else alert("error enviant nova clau"); } )
			.catch( (error) => { console.log(error); } );
		},
		cipher: function (salt) {
		    let textToChars = text => text.split('').map(c => c.charCodeAt(0))
		    let byteHex = n => ("0" + Number(n).toString(16)).substr(-2)
		    let applySaltToChar = code => textToChars(salt).reduce((a,b) => a ^ b, code)    
		    return text => text.split('')
		        .map(textToChars)
		        .map(applySaltToChar)
		        .map(byteHex)
		        .join('')
		},
		tempPwd: function() {
			var vm= this;
			if (!vm.validPwd()) return '';
			var json=JSON.parse(vm.club.json);
			if (!json || !json.pwd) return '';
			var pwd= json.pwd;
			var email= vm.club.email;
			var dt= (new Date()).toISOString().replace(/[-\.TZ:]/g,'').substr(0,11);
			let myCipher = vm.cipher(dt)
			return myCipher(email+'|'+pwd)
		},
		validPwd: function() {
			var vm= this;
			if (!vm.club.id) return false;
			var json=JSON.parse(vm.club.json);
			return (json && json.pwd);
		},
		parseTime: function(time) {
			
			var str = time||'';
			var year = str.substring(0, 4);
			
			return new Date(year, 1, 1, 1, 1, 1);	
			
		},
		getPhoto:function(res) {
			var vm=this;
			vm.club.imatge = '/static'+res.file;
		},
	  	remove:function(row) {
			var vm=this;

			vm.$http.delete('/club/'+row.id)
	        .then(function (response) {
	        	
	            vm.getData('club');
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    },
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/club/'+vm.$route.params.clubId, { cache: false })
	        .then(function (response) {
	            vm.club = response.data[0];
	            var json= JSON.parse(vm.club.json);
	            vm.pwd= json.pwd;
	            vm.club.fundacio = vm.parseTime(response.data[0].fundacio);
	            vm.getEquips('equipsdeclub');
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getEquips: function(listName, page){
	        var vm = this;
	        var searchFilter = escape('/'+vm.club.id);
	        var searchPage = page!=null ? '/p/'+ page : '/p/0';
	        
	        vm.$http.get(listName+searchFilter+searchPage, { cache: false })
	        .then(function (response) {
	            vm.list = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
	},
	mounted: function () {
		var vm=this;
		if(vm.$route.params.clubId >= 0) {
			vm.getData();
			if(vm.club.cp!='' || vm.club.poblacio!='') vm.getGeo();
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
	},
}
</script>

<style lang="less">



</style>