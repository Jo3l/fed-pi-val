<template>
    <transition name="fade">
	    <div>

			<h1 style="margin-bottom:0;">Club</h1>
			
            <ui-tabs type="text" ref="tabs">

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
									<ui-button color="saveForm" icon="edit" icon-position="left" size="small" type="secondary" @click="modSubscribe(props.row)" v-if="(new Date).toISOString().split('T')[0].replace(/-/g,'')<=props.row.fi.substring(0,8)" style="margin:0;display:inline">Modificar</ui-button>
									<ui-button color="saveForm" icon="delete" icon-position="left" size="small" type="secondary" @click="deSubscribe(props.row)" v-if="(new Date).toISOString().split('T')[0].replace(/-/g,'')<=props.row.fi.substring(0,8)" style="margin:0;display:inline">Esborrar</ui-button>
									<ui-button color="default" icon="print" icon-position="left" size="small" type="secondary" @click="modSubscribe(props.row,true)" v-if="(new Date).toISOString().split('T')[0].replace(/-/g,'')>props.row.fi.substring(0,8)">Imprimir</ui-button>
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
                
                <ui-tab title="Confirmacions">
					<br>
					
					<div class="vuetableContainer">
						<tablerone :tableList="confirmResult" :tableColumns="columnsResultats">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">
									<ui-button color="default" icon="check" icon-position="left" size="small" type="secondary" @click="confResultats(props.row.tag)">Confirmar</ui-button>
								</td>
							</template>
						</tablerone>
					</div>
					
                </ui-tab>
                
                <ui-tab title="Demanar registre jugador" id="nou">
                    
					<club-nou-jugador></club-nou-jugador>
                    
                </ui-tab>

                <ui-tab title="Llistat de jugadors" id="llistatjugadors">
                    
					<br>
					
					<div class="vuetableContainer">
						<tablerone :tableList="jugadorsclub" :tableColumns="columnsJugadors">
							<!--th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">
									<ui-button color="default" icon="edit" icon-position="left" size="small" type="secondary" @click="console.log(props.row)">Editar</ui-button>
								</td>
							</template-->
						</tablerone>
					</div>
                    
                </ui-tab>

				
                <ui-tab title="Dades Club">
                    
					<club-editar></club-editar>
                    
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
	
	
  		<ui-modal ref="updateEquip" size="large" :title="'Inscripció'+(readonly===false ? ' - Insertar Jugadors':'')" dismissOn="close-button">
			<div class="ui-autocomplete__content">

				<section class="overflow-hidden">
					<ui-textbox
						required
						:invalid="subscribeName.length === 0"
						error="Camp de text requerit"
					    floating-label
			            autocomplete="off"
			            label="Nom del equip a inscriure"
						type="text"
			            v-model="subscribeName"
			            :disabled="readonly === true"
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
						<tr v-for="(element,index) in equip">
							<td>{{element.numsoci}}</td>
							<td>{{element.nom}}</td>
							<td>
								<ui-icon-button v-if="readonly === false" icon="delete" size="small" type="secondary" @click="deletePlayer(element.id)"></ui-icon-button>
							</td>
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
			            v-if="readonly === false"
					 ></ui-textbox>
				</section>

				<ui-button
                    color="fedpival"
                    icon="add"
					:disabled="insertaJugador.length==0 || equip.length>=maximjugadors"
                    size="small"
                    @click="addPlayer(insertaJugador)"
			        v-if="readonly === false"
                >Afegir jugador</ui-button> ({{ equip.length }} de {{ maximjugadors }})

				<ui-button
                    color="fedpival"
                    icon="person_add"
                    size="small"
                    @click="askPlayer()"
		            v-if="readonly === false"
                >Demanar registre jugador nou</ui-button>

				<br>
				
				<ui-textbox
					required
		            error="Camp de text requerit"
		            :invalid="delegat.length === 0"
				    floating-label
		            autocomplete="off"
		            label="Delegat"
					type="text"
		            v-model="delegat"
		            :disabled="readonly === true"
		        ></ui-textbox>
		        
		        <ui-textbox
		        	required
		            error="Camp de text requerit"
		            :invalid="telefon.length === 0"
				    floating-label
		            autocomplete="off"
		            label="Telèfon delegat"
					type="text"
		            v-model="telefon"
		            :disabled="readonly === true"
		        ></ui-textbox>
		        
		        <ui-textbox
		        	required
		            error="Camp de text requerit"
		            :invalid="lloc.length === 0"
				    floating-label
		            autocomplete="off"
		            label="Lloc"
					type="text"
		            v-model="lloc"
		            :disabled="readonly === true"
		        ></ui-textbox>
		        
		        <ui-textbox
		        	required
		            :invalid="diasem.length === 0"
		            error="Camp de text requerit"
				    floating-label
		            autocomplete="off"
		            label="Dia de la setmana"
					type="text"
		            v-model="diasem"
		            :disabled="readonly === true"
		        ></ui-textbox>
		        
		        <ui-textbox
		        	required
		            error="Camp de text requerit"
		            :invalid="hora.length === 0"
				    floating-label
		            autocomplete="off"
		            label="Hora"
					type="text"
		            v-model="hora"
		            :disabled="readonly === true"
		        ></ui-textbox>
		        
				<div class="buttonGroupRight">
					
					<ui-button 
					size="small" 
		            v-if="readonly === false"
					:disabled="subscribeName=='' || equip.length<minimjugadors || hora=='' || diasem=='' || lloc=='' || telefon=='' || delegat==''" 
					@click="doModSubscribe(subscribeId)">{{ $t('common.save') }}</ui-button>
					
					<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
				</div>
				

	            
			</div>
        </ui-modal>
       
       
  		<ui-modal ref="updateResultats" size="large" title="Resultats" dismissOn="close-button">
  			<update-results v-if="partida&&partida.id" :partidaId="partida.id"></update-results>
  			<!--
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
				
				<label><strong>Delegat / Jutge:</strong></label>
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
				-->
				
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
import ClubEditar from './ClubEditar.vue';
import ClubNouJugador from './ClubNouJugador.vue';
import UpdateResults from './UpdateResults.vue';

export default {
  	components: { VuePellEditor, 'vue-core-image-upload': VueCoreImageUpload, 'tablerone':Table, 'paginate': Paginate, ClubEditar, ClubNouJugador, UpdateResults },
	data () {
		return {
			error:{},
			resultats:[],
		    inscripcions:[],
		    equips:[],
		    jugadorsclub:[],
		    subscribeName:'',
		    subscribeId:'',
		    readonly:false,
		    currChamp:'',
		    partida: {},
		    equip:[],
		    equipLocal:[],
		    equipVisitant:[],
		    buscadorJugador:[],
		    insertaJugador:'',
		    minimjugadors:0,
		    maximjugadors:99,
		    hora:'', 
		    diasem:'', 
		    lloc:'', 
		    telefon:'', 
		    delegat:'',
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
		    columnsJugadors:[
		    	{
	                label: 'Número soci',
	                field: 'numsoci',
	                html: false,    
	            },
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
			confirmResult:[],
		    /*club:{
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
		    },*/
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
		askPlayer: function(e) {
			this.closeAllModals();
			this.$refs.tabs.setActiveTab('nou')
			console.log(e);
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
	        	if (vm.$refs[key].close) vm.$refs[key].close();
	        }
	    },
	    modResultats:function(partida){
			var vm=this;
			console.log(partida)
			vm.getInfoPartida(partida.id);
			vm.$refs.updateResultats.open();
	    },
	    deSubscribe:function(champ, readonly=false){
	    	var vm=this;
	    	vm.$http.delete('/eliminaequip/'+champ.id);
			vm.getInscripcions();
			vm.getEquips(vm.$store.getters.userId);
	    },
	    modSubscribe:function(champ, readonly=false){
			var vm=this;
			vm.readonly= readonly;
			vm.currChamp= champ.competicio;
			vm.minimjugadors= champ.minimjugadors;
			vm.maximjugadors= champ.maximjugadors;
			console.log(champ,1)
			vm.subscribeName = champ.nom;
			vm.subscribeId = champ.id;
			vm.delegat= champ.delegat || '';
			vm.telefon= champ.telefon || '';
			vm.lloc= champ.lloc || '';
			vm.diasem= champ.diasem || '';
			vm.hora= champ.hora || '';
			vm.getJugadorsEquip(champ.id);
			vm.$refs.updateEquip.open()
	    },
		subscribe: function(champ){
			var vm=this;
			vm.currChamp=champ.id;
			vm.equip= []
			vm.minimjugadors=champ.minimjugadors;
			vm.maximjugadors=champ.maximjugadors;
			console.log(champ,2);
			vm.$refs.updateEquip.open();
			//vm.$refs.insertNomEquip.open()
		},
		doModSubscribe:function(id){
			var vm=this;
			vm.$http.post('/equip'+(id?'/'+id:''), {"nom":vm.subscribeName, "club":vm.$store.getters.userId, "competicio":vm.currChamp, "id":(id?id:null), "hora":vm.hora, "diasem":vm.diasem, "lloc":vm.lloc, "telefon":vm.telefon, "delegat":vm.delegat } )
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
					//window.location.reload();
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
		/*setPoblacio:function(){
			var vm = this;
			var poblacio = vm.club.cp.split(" - ");
			vm.club.cp = poblacio[0];
			vm.club.poblacio = poblacio[1];
		},*/
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/club/'+vm.$store.getters.userId, { cache: false })
	        .then(function (response) {
	            //vm.club = response.data[0];
	            //vm.club.fundacio = vm.parseTime(response.data[0].fundacio);
	            vm.getEquips(vm.$store.getters.userId);
	            vm.getResultats(vm.$store.getters.userId);
	            vm.getInscripcions();
	            vm.getJugadors(vm.$store.getters.userId);
	            vm.getConfirmList();
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
				  inscripcions[key].maximjugadors = inscripcions[key].maximjugadors;
				}

	            vm.inscripcions = inscripcions;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getConfirmList: function(){
	        var vm = this;
	        vm.$http.get('/partidesaconfirmar', { cache: false })
	        .then(function (response) {
	        	vm.confirmResult=response.data;
	        	console.log(response.data)
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		confResultats: function(tag){
			var vm = this;
	        vm.$http.get('/confirmapartida/'+tag, { cache: false })
	        .then(function (response) {
	            vm.getConfirmList();
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
		getJugadors:function(id){
	        var vm = this;
	        vm.$http.get('/jugadorsdeclub/'+id, { cache: false })
	        .then(function (response) {
	            vm.jugadorsclub = response.data;
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
		vm.$eventHub.$on('closeallmodals', this.closeAllModals);
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
