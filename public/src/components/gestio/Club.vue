<template>
    <transition name="fade">
	    <div>

			<h1 style="margin-bottom:0;">Club</h1>
			
            <ui-tabs type="text">


                <ui-tab title="Competicions obertes">
					<br>
					<div class="vuetableContainer">
						<tablerone :tableList="inscripcions" :tableColumns="columnsInscripcions">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">
									<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="subscribe(props.row)">Fer inscripció</ui-button>
								</td>
							</template>
						</tablerone>
					</div>
                </ui-tab>

                <ui-tab title="Inscripcions">
					<br>
					<div class="vuetableContainer">
						<tablerone :tableList="equips" :tableColumns="columnsEquips">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">
									<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="modSubscribe(props.row)" v-if="(new Date).toISOString().split('T')[0].replace(/-/g,'')+'235959'<=props.row.fi">Modificar</ui-button>
								</td>
							</template>
						</tablerone>
					</div>
					
					
                </ui-tab>
				
                <ui-tab title="Resultats">
					<br>
					
					<div class="vuetableContainer">
						<tablerone :tableList="resultats" :tableColumns="columnsResultats">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">
									<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="modResultats(props.row)">Modificar</ui-button>
								</td>
							</template>
						</tablerone>
					</div>
					
                </ui-tab>
				
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
                    
                </ui-tab>
            </ui-tabs>
			
			
  		<ui-modal ref="insertNomEquip" size="normal" title="Insertar Nom del Equip" dismissOn="close-button">
			<div class="ui-autocomplete__content">
				<label class="ui-autocomplete__label">
					<input v-model="subscribeName" placeholder="Nom del equip" class="ui-autocomplete__input"><br><br>
				</label> 
				
				<div class="buttonGroupRight">
					<ui-button size="small" @click="doSubscribe()">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
				
			</div>
        </ui-modal>
	
	
  		<ui-modal ref="updateEquip" size="large" title="Inscripció - Insertar Jugadors" dismissOn="close-button">
			<div class="ui-autocomplete__content">

				<section class="overflow-hidden">
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            label="Nom de la inscripció"
								type="text"
					            v-model="subscribeName"
					 ></ui-textbox>
				</section>
			    <table class="table results">
					<thead>
						<tr>
							<th>Nº Llicència</th>
							<th>Nom</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="element in equip">
							<td>{{element.numsoci}}</td>
							<td>{{element.nom}}</td>
							<th>
								<ui-icon-button icon="delete" size="small" type="secondary" @click="deletePlayer(element.id)"></ui-icon-button>
							</th>
						</tr>
					</tbody>
				</table>
				
				<ui-alert type="warning" v-show="equip.length<minimjugadors">
	                Falt{{ ((minimjugadors-(equip?equip.length:0))==1?'a':'en') }} {{minimjugadors-(equip?equip.length:0)}} jugador{{ ((minimjugadors-(equip?equip.length:0))==1?'':'s') }} per a completar l'inscripció.
	            </ui-alert>
	            
				<section class="overflow-hidden">
					<ui-textbox
							    floating-label
					            autocomplete="off"
					            label="Nº de Llicència a inscriure"
								type="number"
					            v-model="insertaJugador"
					 ></ui-textbox>
				</section>


				<ui-button
                    color="fedpival"
                    icon="add"
					:disabled="insertaJugador.length==0"
                    size="small"
                    @click="addPlayer(insertaJugador)"
                >Afegir jugador</ui-button>


				<div class="buttonGroupRight">
					<ui-button size="small" :disabled="equip.length<minimjugadors" @click="doModSubscribe(subscribeId)">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
				

	            
			</div>
        </ui-modal>
       
       
  		<ui-modal ref="updateResultats" size="large" title="Resultats" dismissOn="close-button">
				<section class="score">
					<p>Data: {{partida.date}}</p>
					<p v-if="partida.lloc">Lloc: {{partida.lloc}}</p>
					<div class="escuts">
						<div>
							<div class="escut" :style="'background-image:url('+ partida.imatge_local +');'"></div>
							<label>{{partida.nom_inscripcio_local}}</label>
							<input class="bigScore" type="number" min="0" max="999" v-model="partida.resultatlocal">
						</div>
						<div class="separator">VS</div>
						<div>
							<div class="escut" :style="'background-image:url('+ partida.imatge_visitant +');'"></div>
							<label>{{partida.nom_inscripcio_visitant}}</label>
							<input class="bigScore" type="number" min="0" max="999" v-model="partida.resultatvisitant">
						</div>
						
					</div>
				</section>
				
				<section class="flex50">
				    <table class="table">
				    	<caption>Local</caption>
						<thead>
							<tr>
								<th>Nº Llicència</th>
								<th>Nom</th>
								<th>Participa</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="element in equipLocal">
								<td>{{element.numsoci}}</td>
								<td>{{element.nom.toLowerCase()}}</td>
								<th>
									<ui-switch v-model="element.juga" @click="element.juga==!element.juga"></ui-switch>
									<ui-icon-button v-if="false" icon="delete" size="small" type="secondary" @click="deletePlayerPartida(element.id)"></ui-icon-button>
								</th>
							</tr>
						</tbody>
					</table>
					
				    <table class="table">
				    	<caption>Visitant</caption>
						<thead>
							<tr>
								<th>Nº Llicència</th>
								<th>Nom</th>
								<th>Participa</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="element in equipVisitant">
								<td>{{element.numsoci}}</td>
								<td>{{element.nom.toLowerCase()}}</td>
								<th>
									<ui-switch v-model="element.juga" @click="element.juga==!element.juga"></ui-switch>
									<ui-icon-button v-if="false" icon="delete" size="small" type="secondary" @click="deletePlayerPartida(element.id)"></ui-icon-button>
								</th>
							</tr>
						</tbody>
					</table>
				</section>
				

				<section class="add-local-visitant" v-if="false">
					<ui-button
	                    color="fedpival"
	                    icon="add"
						:disabled="insertaJugador.length==0"
	                    size="small"
	                    @click="addPlayerLocal(insertaJugador)"
	                >Afegir jugador Local</ui-button>
	                
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            label="Nº de Llicència a inscriure"
						type="number"
			            v-model="insertaJugador"
					 ></ui-textbox>
					 
					<ui-button
	                    color="fedpival"
	                    icon="add"
						:disabled="insertaJugador.length==0"
	                    size="small"
	                    @click="addPlayerVisitant(insertaJugador)"
	                >Afegir jugador Visitant</ui-button>
				</section>
				<br>
				
				<label><strong>Delegat:</strong></label>
				<div class="triple-flex">
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            label="Nom"
						type="text"
			            v-model="partida.nomDelegat"
					 ></ui-textbox>
					
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            label="Num llicència"
						type="text"
			            v-model="partida.llicenciaDelegat"
					 ></ui-textbox>
					 
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            label="Telèfon o e-mail"
						type="text"
			            v-model="partida.contacteDelegat"
					 ></ui-textbox>
				 </div>
				 
				<section class="overflow-hidden">
		            <ui-textbox
		                enforce-maxlength
		                floating-label
		                help=""
		                label="Comentari de la partida:"
		                placeholder="Inserta un comentari"
		                :multi-line="true"
		                :maxlength="2048"
		                v-model="partida.comentari"
		            ></ui-textbox>
				</section>	
				<br>
				<div class="buttonGroupRight">
					<ui-button size="small" @click="guardaPartida(partida)">{{ $t('common.save') }}</ui-button>
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>

        </ui-modal>
        
        <ui-modal ref="error" title="">
            {{error.response && error.response.data.error?error.response.data.error:error.response}}
	        <div slot="footer">
	            <ui-button @click="closeModal('error')">Ok</ui-button>
	        </div>
        </ui-modal>
        
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
			error:{},
			resultats:[],
		    inscripcions:[],
		    equips:[],
		    subscribeName:'',
		    subscribeId:'',
		    currChamp:'',
		    partida: {},
		    equip:[],
		    equipLocal:[],
		    equipVisitant:[],
		    buscadorJugador:[],
		    insertaJugador:'',
		    minimjugadors:0,
		    columnsInscripcions:[
	            {
	                label: 'Competició',
	                field: 'fullName',
	                html: false,    
	            },
	            {
	                label: 'Inici Inscripció',
	                field: 'inici',
	                html: false,  
	            },
	            {
	                label: 'Fi Inscripció',
	                field: 'fi',
	                html: false,  
	            }
	        ],
		    columnsEquips:[
		    	{
	                label: 'Competició',
	                field: 'nomCompeticio',
	                html: false,    
	            },
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            }
	        ],
		    columnsResultats:[
		    	{
	                label: 'Data',
	                field: 'data',
	                html: false,    
	            },
	            {
	                label: 'Local',
	                field: 'nom_inscripcio_local',
	                html: false,    
	            },
	            {
	                label: 'Visitant',
	                field: 'nom_inscripcio_visitant',
	                html: false,    
	            },
	            {
	                label: '',
	                field: 'resultatlocal',
	                html: false,    
	            },
	            {
	                label: '',
	                field: 'resultatvisitant',
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
			editorOptions: VuePellEditorConfig(this.openModal),
		}
	},
	methods: {
		refreshTab: function(tab) {
			console.log(tab)	
		},
		addPlayer: function(player){
			var vm = this;
			console.log(player)
			for(var i = 0; i < vm.equip.length; i++) {
			    if( parseInt(vm.equip[i].numsoci) == parseInt(player) ) {
			    	vm.error={};
			    	vm.error.response = {};
			    	vm.error.response.data = {};
			    	vm.error.response.data.error="Ja existeix el jugador";
					vm.$refs.error.open();
			        return false;
			    }
			}
			
			vm.$http.get('/soci/'+player)
	        .then(function (response) {
				vm.equip.push(response.data);
	        })
	        .catch(function (error) {
	            vm.error=error;
				vm.$refs.error.open();
				console.log(error);
	        });
	        
			
			/*
				vm.$http.post('/inscrits/'+vm.subscribeId,{"jugador":player})
		        .then(function (response) {
		        	vm.getJugadorsEquip(vm.subscribeId);
		        })
		        .catch(function (error) {
		        	vm.error=error;
					vm.$refs.error.open()
		            console.log(error);
		        });
		    */

		},
		deletePlayer: function(player){
			var vm = this;
			for(var i = 0; i < vm.equip.length; i++) {
			    if(vm.equip[i].id == player) {
			        vm.equip.splice(i, 1);
			        break;
			    }
			}

			/*
			vm.$http.delete('/inscrits/'+vm.subscribeId+'/'+player)
	        .then(function (response) {
	        	vm.getJugadorsEquip(vm.subscribeId);
	        })
	        .catch(function (error) {
	        	vm.error=error;
				vm.$refs.error.open()
	            console.log(error);
	        });
	        */

		},
		deletePlayerPartida: function(player){
			var vm = this;

				vm.$http.delete('/participa/'+vm.partida.id+'/'+player)
		        .then(function (response) {
		        	vm.getJugadorsPartida(vm.partida.id);
		        })
		        .catch(function (error) {
		        	vm.error=error;
					vm.$refs.error.open()
		            console.log(error);
		        });

		},
		closeModal: function(ref) {
            this.$refs[ref].close();
        },
	    closeAllModals: function() {
	    	var vm=this;
		    for (var key in vm.$refs) {
	        	vm.$refs[key].close();
	        }
	    },
	    modResultats:function(partida){
			var vm=this;
			vm.getInfoPartida(partida.id);
			vm.$refs.updateResultats.open()
	    },
	    modSubscribe:function(champ){
			var vm=this;
			vm.currChamp=champ.competicio;
			vm.minimjugadors=champ.minimjugadors;
			vm.subscribeName = champ.nom;
			vm.subscribeId = champ.id;
			vm.getJugadorsEquip(champ.id);
			vm.$refs.updateEquip.open()
	    },
		subscribe: function(champ){
			var vm=this;
			vm.currChamp=champ.id;
			vm.minimjugadors=champ.minimjugadors;
			console.log(champ);
			vm.$refs.updateEquip.open();
			//vm.$refs.insertNomEquip.open()
		},
		doModSubscribe:function(id){
			var vm=this;
			vm.$http.post('/equip'+(id?'/'+id:''), {"nom":vm.subscribeName, "club":vm.$store.getters.userId, "competicio":vm.currChamp, "id":(id?id:null)})
			.then( function(response) {
				
				if(response.data.exception){
					vm.error=response.data.error;
					vm.$refs.error.open()
					console.log(vm.error);
				} else {
					
					vm.$http.post('/inscrits/'+(id?id:response.data[0].id), vm.equip)
					.then( function(response) {
						vm.closeAllModals();
						window.location.reload();
					})
					.catch( function (error) { 
						vm.error=error;
						vm.$refs.error.open()
						console.log(vm.error);
						console.log(error);
					} );
					

				}
				

			})
			.catch( function (error) { 
				vm.error=error;
				vm.$refs.error.open()
				console.log(vm.error);
				console.log(error);
			} );
		},
		doSubscribe: function(){
			var vm=this;
			vm.$http.post('/equip', {"nom":vm.subscribeName, "club":vm.$store.getters.userId, "competicio":vm.currChamp})
			.then( function(response) {
				
				if(response.data.exception){
					vm.error=response.data.error;
					vm.$refs.error.open()
					console.log(vm.error);
				} else {
					vm.closeAllModals();
					vm.getInscripcions();
					vm.getEquips(vm.$store.getters.userId);
					vm.insertaJugador=null;
				}
				

			})
			.catch( function (error) { 
				vm.error=error;
				vm.$refs.error.open()
				console.log(vm.error);
				console.log(error);
			} );
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
	            vm.getEquips(vm.$store.getters.userId);
	            vm.getResultats(vm.$store.getters.userId);
	            vm.getInscripcions();
	            
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
		getInscripcions: function() {
	        var vm = this;

	        vm.$http.get('/inscripcions', { cache: false })
	        .then(function (response) {
	        	var inscripcions=response.data;
	        	for (var key = 0; key < inscripcions.length; key++){
				  inscripcions[key].inici = vm.parseTime(inscripcions[key].inici).toString('d/M/yyyy');
				  inscripcions[key].fi = vm.parseTime(inscripcions[key].fi).toString('d/M/yyyy');
				  inscripcions[key].fullName = inscripcions[key].fullName.substring(2);
				  inscripcions[key].minimjugadors = inscripcions[key].minimjugadors;
				}

	            vm.inscripcions = inscripcions;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getEquips: function(id){
	        var vm = this;
	        vm.$http.get('/equipsdeclub/'+id, { cache: false })
	        .then(function (response) {
	        	var inscripcions=response.data;
	        	for (var key = 0; key < inscripcions.length; key++){
				  if(inscripcions[key].path) inscripcions[key].path = inscripcions[key].path.substring(2);
				  inscripcions[key].nomCompeticio = inscripcions[key].cami[vm.$i18n.locale];
				}
				
	            vm.equips = Object.assign({}, inscripcions);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getResultats: function(id){
	        var vm = this;
	        vm.$http.get('/partides/'+id, { cache: false })
	        .then(function (response) {
	        	var resul=response.data;
	        	for (var key = 0; key < resul.length; key++){
				  resul[key].data = vm.parseTime(resul[key].data).toString('d/M/yyyy');
				}
				vm.resultats=resul;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getJugadorsEquip:function(idInscripcio){
	        var vm = this;
	        vm.$http.get('/inscrits/'+idInscripcio, { cache: false })
	        .then(function (response) {
	            vm.equip = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getJugadorsPartida:function(idPartida){
	        var vm = this;
	        vm.$http.get('/participa/'+idPartida, { cache: false })
	        .then(function (response) {
	            vm.equipLocal = response.data.local;
	            vm.equipVisitant = response.data.visitant;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getInfoPartida:function(id){
	        var vm = this;
	        vm.$http.get('/partida/'+id, { cache: false })
	        .then(function (response) {
	        	var res = response.data[0];
	        	res.date = vm.parseTime(res.data).toString('d/M/yyyy');
	        	res.imatge_local = res.imatge_local ? res.imatge_local : '/static/img/shield.png';
	        	res.imatge_visitant = res.imatge_visitant ? res.imatge_visitant : '/static/img/shield.png';
	        	res.resultatlocal = res.resultatlocal ? res.resultatlocal : 0;
	        	res.resultatvisitant = res.resultatvisitant ? res.resultatvisitant : 0;
	            vm.partida = res;
	            vm.getJugadorsPartida(id);
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		addPlayerLocal: function(numsoci) {
			var vm=this;
			vm.$http.post('/participa/'+vm.partida.id,{"numsoci":numsoci, "equip":vm.partida.local})
	        .then(function (response) {
	        	vm.getInfoPartida(vm.partida.id);
	        })
	        .catch(function (error) {
	        	vm.error=error;
				vm.$refs.error.open()
	            console.log(error);
	        });
		}, 
		addPlayerVisitant: function(numsoci){
			var vm=this;
			vm.$http.post('/participa/'+vm.partida.id,{"numsoci":numsoci, "equip":vm.partida.visitant})
	        .then(function (response) {
	        	vm.getInfoPartida(vm.partida.id);
	        })
	        .catch(function (error) {
	        	vm.error=error;
				vm.$refs.error.open()
	            console.log(error);
	        });
		}, 
		guardaPartida: function() {
			var vm=this;
			vm.$http.post('/partida/'+vm.partida.id, {
				"resultatlocal":vm.partida.resultatlocal,
				"resultatvisitant":vm.partida.resultatvisitant,
				"comentari":vm.partida.comentari,
				"nomDelegat":vm.partida.nomDelegat,
				"llicenciaDelegat":vm.partida.llicenciaDelegat,
				"contacteDelegat":vm.partida.contacteDelegat
			})			
	        .then(function (response) {

				vm.$http.post('/participa/'+vm.partida.id,{"local":vm.equipLocal, "visitant":vm.equipVisitant})
		        .then(function (response) {
					vm.getData()
	        		vm.closeAllModals();
		        })
		        .catch(function (error) {
		        	vm.error=error;
					vm.$refs.error.open()
		            console.log(error);
		        });
	        	

	        })
	        .catch(function (error) {
	        	vm.error=error;
				vm.$refs.error.open()
	            console.log(error);
	        });
		}
	},
	mounted: function () {
		var vm=this;
		if(vm.$store.getters.isAuthenticated) vm.getData();
		if(vm.club.cp!='' || vm.club.poblacio!='') vm.getGeo();
		
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticated) {
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

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.flex50 {
	
	display:flex;
	justify-content:space-between;
	
	@media(max-width:@screenTablet) {
		display:initial;
	}
	
	table {
		width:49%;
		@media(max-width:@screenTablet) {
			width:100%;
		}
		td {text-transform:Capitalize;}
		caption{
			text-align:center;
			color:black;
			font-weight:bolder;
			text-transform:uppercase;
		}
	}
	
}

.triple-flex {
	display: flex;
    justify-content: space-between;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;
	@media(max-width:@screenTablet) {
		display:block;
	}
}

.add-local-visitant {
	display: flex;
    justify-content: space-between;
    align-items: center;
    @media(max-width:@screenTablet){
    	flex-direction:column;
    	& > * {margin:20px;}
    }
}

.overflow-hidden {
	overflow:hidden;
}
		
.score {
	p{text-align:center;}
	.escuts {
		display: flex;
		justify-content: space-around;
	    width: 100%;
	    &>div{
	    	display: flex;
		    flex-direction: column;
		    justify-content: center;
		    align-items: center;

		    .escut {
			    width: 7vw;
			    height: 8.2vw;
			    background-size: cover;
			    background-position: center;
			    max-width: 100px;
			    max-height: 120px;
			    margin-bottom:10px;
		    }
		    
		    label {
		    	font-size:2em;
		    	font-weight:bolder;
			    @media(max-width:@screenTablet){
			    	font-size:1em;
			    }
		    }
		    input.bigScore {
			    text-align: center;
			    padding: 10px 10px 10px 20px;
			    margin: 10px;
			    background-color: white;
			    font-size: 4em;
			    border: 1px dashed #87212e;
			    font-weight: bolder;
			    width: 70%;
			    border-radius: 5px;
			}
	    }
	    .separator {
	    	font-size:3em;
	    	font-style: italic;
	    	font-weight:bolder;
		    @media(max-width:@screenTablet){
		    	font-size:1em;
		    }
	    }

	}
	
}
</style>