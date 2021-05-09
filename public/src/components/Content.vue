<template>

	    <div id="content">
		
		<!-- aço es public -->
		<div class="nodeContentEditable" v-if="!$store.getters.isAuthenticatedWithRole(1)">

				<div v-bind:class="{ nodeContentElement:true, autenticated: !$store.getters.isAuthenticatedWithRole(1) }" v-for="(element, key) in nodeContent">
					
					<img v-if="element.tipus == 'I'" :src="element.url" class="wide">

					<article v-if="element.tipus == 'H'">
						<h2 v-if="nodeContent[key].titol !== null">{{nodeContent[key].titol}}</h2>
						<div v-html="nodeContent[key].contingut"></div>
					</article>

					<a v-if="element.tipus == 'F'" :href="element.url"> <ui-icon icon="attach_file"></ui-icon> <strong>{{element.titol}}</strong> </a>
					
					<div v-if="element.tipus == 'M'" class="mapa">
						<iframe :src="'/map.html?'+ element.json"></iframe>
					</div>
					
					<div class="partida" v-if="element.tipus == 'J' && ( !element.json || JSON.parse(element.json).public != false)">
					<table class="table results">
						<thead>
							<tr>
								<th></th>
								<th></th>
								<th>Equip</th>
								<th>Punts</th>
								<th title="Partits jugats">PJ</th>
								<th title="Partits guanyats">PG</th>
								<th title="Partits perduts">PP</th>
								<th title="Jocs a favor">JF</th>
								<th title="Jocs en contra">JC</th>
							</tr>
						</thead>
						<tbody v-for="(group,groupindex) in nodeContent[key].ranking" v-if="nodeContent[key].ranking && Object.keys(nodeContent[key].ranking).length > 0">
							<tr :class="[(groupindex%2)?'odd':'even']">
								<th rowspan="99" >Grup {{String.fromCharCode(65+parseInt(groupindex))}}</th>
							</tr>
							<tr v-for="(rank, index) in group"  :class="[element.selected?'selected':'', (groupindex%2)?'odd':'even']">
								<td><button class="ui-icon-button ui-icon-button--type-primary ui-icon-button--color-default ui-icon-button--size-mini"><div class="ui-icon-button__icon"><span>{{index+1}}</span></div></button></td>
								<td>{{rank.nom}}</td>
								<td>{{rank.punts}} 
									<ui-icon v-if="rank.sancions<0" style="color:red;position:absolute; margin-left:6px;" :title="'sancionat amb '+rank.sancions+' punts'" icon="info_outline"></ui-icon>
								</td>
								<td>{{rank.pj}}</td>
								<td>{{rank.pg}}</td>
								<td>{{rank.pp}}</td>
								<td>{{rank.jf}}</td>
								<td>{{rank.jc}}</td>
							</tr>
						</tbody>
					</table>
					<hr/>
					<table class="table results">
						<thead>
							<tr>
								<th>{{$i18n.t('common.group')}}</th>
								<th>{{$i18n.t('common.date')}}</th>
								<th>{{$i18n.t('common.local')}}</th>
								<th>Res. {{$i18n.t('common.local')}}</th>
								<th>Res. {{$i18n.t('common.visitor')}}</th>
								<th>{{$i18n.t('common.visitor')}}</th>
							</tr>
						</thead>
						<tbody v-if="'partides' in nodeContent[key]">
							<tr v-for="element in nodeContent[key].partides" v-if="nodeContent[key].partides && nodeContent[key].partides.length > 0" :class="[(element.grup%2)?'odd':'even']">
								<td>{{ element.grup?String.fromCharCode(65+parseInt(element.grup)):'' }}</td>
								<td>{{ parseTime(element.data).toString('d/M/yyyy') }}</td>
								<td :style="element.local.id==0?'color:lightgray':''">{{element.local.nom}}</td>
								<td><span class="no-print">{{element.resultatlocal}}</span></td>
								<td><span class="no-print">{{element.resultatvisitant}}</span></td>
								<td :style="element.visitant.id==0?'color:lightgray':''">
									{{element.visitant.nom}}
									<ui-icon v-if="element.confirmavisitant!=1" style="color:#f80;position:absolute; margin-left:6px;" title="Resultat no confirmat pel visitant" icon="info_outline"></ui-icon>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
					
					
				<!-- llistat -->
				<div class="buscador" v-if="listOn(element.tipus)">
					<pre>{{searchList}}</pre>
				</div>
				<!-- llistat -->
					
				</div>


		</div>
		<!-- aço es admin -->
		<div class="nodeContentEditable" v-if="$store.getters.isAuthenticatedWithRole(0)">
			
			<table class="table results" v-if="!containsCompetitionNode && teams">
				<thead>
					<tr>
						<th>Equip</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr  v-for="(element, key) in teams" >
						<td>{{element.nom}}</td>
						<td>
							<ui-button @click="$router.push('/admin/editequip/'+element.id);" size="small" icon="edit"> editar </ui-button>
						</td>
					</tr>
				</tbody>
			</table>

			<draggable v-model="nodeContent"  :options="{handle:'.drag',chosenClass:'floating'}" @end="setOrder()" preventOnFilter="true">
				<div class="nodeContentElement" v-for="(element, key) in nodeContent" v-if="true || nodeContent!=null">
					
					<!-- drag -->
					<i class="remove" @click="removeContent(element)"></i>
					<i class="drag"></i>
					<!-- drag -->
					
					<!-- imatge -->
					<img class="teaserImg wide" v-if="element.tipus == 'I' && element.url" :src="element.url">
					<div class="buttonContainer" v-if="element.tipus == 'I'">
	                	<ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', element, element.url, 'img')">{{$i18n.t('image.selectImage')}}</ui-button>
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- imatge -->
					
					<!-- mapa -->
					
					<div v-if="element.tipus == 'M'" class="mapa">
						<ui-button color="blueButtonToRight" icon="map" size="small" type="secondary" @click="nodeContent[key].json = dataMap">{{$i18n.t('common.thisLocation')}}</ui-button>
						<input v-model="nodeContent[key].json">
						<iframe :src="'/map.html?'+ nodeContent[key].json + '&admin'"></iframe>
					</div>
					<div class="buttonContainer" v-if="element.tipus == 'M'">
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- mapa -->
					
					<!-- Html -->
					<article v-if="element.tipus == 'H'">
						<input v-model="nodeContent[key].titol">
						    <VuePellEditor 
						        :actions="editorOptions" 
						        :content="nodeContent[key].contingut" 
						        v-model="nodeContent[key].contingut"
						        :styleWithCss="false"
						        placeholder="..."
						    />
					</article>

					<div class="buttonContainer" v-if="element.tipus == 'H'">
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- Html -->
					
					<!-- Partida -->
					<div class="partida" v-if="element.tipus == 'J'">
						<ui-checkbox :value="isCompetitionVisible" @change="toggleVisibleCompetition(element)">Competició visible</ui-checkbox>
						<div class="buttonContainer">
							<ui-button color="red" icon="delete" size="small" type="secondary" @click="deleteAllMatches()">Esborrar totes les partides...</ui-button>
							<ui-button color="red" icon="save" size="small" type="secondary" @click="generator=true">Generador Campionat/Copa</ui-button>
						</div>
						<table class="table results">
							<thead>
								<tr>
									<th></th>
									<th></th>
									<th>Equip</th>
									<th>Punts</th>
									<th title="Partits jugats">PJ</th>
									<th title="Partits guanyats">PG</th>
									<th title="Partits perduts">PP</th>
									<th title="Jocs a favor">JF</th>
									<th title="Jocs en contra">JC</th>
								</tr>
							</thead>
							<!--tbody>
								<tr v-for="(rank,index) in nodeContent[key].ranking" v-if="nodeContent[key].ranking && nodeContent[key].ranking.length > 0" :class="[element.selected?'selected':'', (element.grup%2)?'odd':'even']">
									<td><button class="ui-icon-button ui-icon-button--type-primary ui-icon-button--color-default ui-icon-button--size-mini"><div class="ui-icon-button__icon"><span>{{index+1}}</span></div></button></td>
									<td>{{rank.nom}}</td>
									<td>{{rank.punts}}</td>
									<td>{{rank.pj}}</td>
									<td>{{rank.pg}}</td>
									<td>{{rank.pp}}</td>
									<td>{{rank.jf}}</td>
									<td>{{rank.jc}}</td>
								</tr>
							</tbody-->
							<tbody v-for="(group,groupindex) in nodeContent[key].ranking" v-if="nodeContent[key].ranking && nodeContent[key].ranking.length > 0">
								<tr :class="[(groupindex%2)?'odd':'even']">
									<th rowspan="99" >Grup {{String.fromCharCode(65+parseInt(groupindex))}}</th>
								</tr>
								<tr v-for="(rank, index) in group"  :class="[element.selected?'selected':'', (groupindex%2)?'odd':'even']">
									<td><button class="ui-icon-button ui-icon-button--type-primary ui-icon-button--color-default ui-icon-button--size-mini"><div class="ui-icon-button__icon"><span>{{index+1}}</span></div></button></td>
									<td>{{rank.nom}}</td>
									<td>{{rank.punts}} 
										<ui-icon v-if="rank.sancions<0" style="color:red;position:absolute; margin-left:6px;" :title="'sancionat amb '+rank.sancions+' punts'" icon="info_outline"></ui-icon>
									</td>
									<td>{{rank.pj}}</td>
									<td>{{rank.pg}}</td>
									<td>{{rank.pp}}</td>
									<td>{{rank.jf}}</td>
									<td>{{rank.jc}}</td>
								</tr>
							</tbody>
						</table>
						<hr/>
						<table class="table results">
							<thead>
								<tr>
									<th>{{$i18n.t('common.group')}}</th>
									<th>{{$i18n.t('common.date')}}</th>
									<th>{{$i18n.t('common.local')}}</th>
									<th>Res. {{$i18n.t('common.local')}}</th>
									<th>Res. {{$i18n.t('common.visitor')}}</th>
									<th>{{$i18n.t('common.visitor')}}</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody v-if="'partides' in nodeContent[key]">
								<tr v-for="element in nodeContent[key].partides" v-if="nodeContent[key].partides && nodeContent[key].partides.length > 0" :class="[element.selected?'selected':'', (element.grup%2)?'odd':'even']">

									<td>{{ String.fromCharCode(65+parseInt(element.grup)) }}</td>
									<td>{{ parseTime(element.data).toString('d/M/yyyy') }}</td>
									<td :style="element.local.id==0?'color:lightgray':''">{{element.local.nom}}</td>
									<td><span class="no-print">{{element.resultatlocal}}</span></td>
									<td><span class="no-print">{{element.resultatvisitant}}</span></td>
									<td :style="element.visitant.id==0?'color:lightgray':''">{{element.visitant.nom}}</td>
									<td>
										<ui-icon-button icon="edit" size="small" type="secondary" @click="editMatch(element)"></ui-icon-button>
										<ui-icon-button icon="delete" size="small" type="secondary" @click="deleteMatch(element)"></ui-icon-button>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="buttonContainer" v-if="'partides' in nodeContent[key] && [2,4,8,16,32].indexOf(nodeContent[key].partides.length)>0">
							<ui-button v-if="jugades(key)" color="red" icon="call_merge" size="small" type="secondary" @click="nextPhase(key)">Anular equips perdedors per generar partides de nova fase</ui-button>
							<span v-else><ui-icon icon="warning"></ui-icon> El número de partides jugades no permet generar la següent fase</span>
						</div>

						<div class="form" v-if="generator">
							<match-generator :nodeId="element.jerarquia" :blockId="element.id"></match-generator>
						</div>
					</div>
					<!-- Partida -->
					
					<!-- Arxiu -->
					<ui-textbox
							    floating-label
							    v-if="element.tipus == 'F'"
				                autocomplete="off"
				                error="This field is required"
				                label="Etiqueta"
								type="text"
				                v-model="element.titol"
				    ></ui-textbox>
					<span v-if="element.tipus == 'F'">{{element.url}}</span>
					<div class="buttonContainer" v-if="element.tipus == 'F'">
						<ui-button color="blueButtonToRight" icon="cloud_upload" size="small" type="secondary" @click="openModal('uploadModal', element, element.url, 'pdf')">{{$i18n.t('common.uploadPdf')}}</ui-button>
						<ui-button color="blueButtonToRight" icon="save" size="small" type="secondary" @click="saveBlock(element)">{{$i18n.t('common.save')}}</ui-button>
					</div>
					<!-- Arxiu -->
					
					
					<!-- llistat -->
					<div class="buscador" v-if="listOn(element.tipus)">
						<pre>{{element.tipus}}</pre>
					</div>
					<!-- llistat -->
					
				</div>
				

			</draggable>
		</div>
		
		<div class="flexWrap" v-if="$store.getters.isAuthenticatedWithRole(0)">
				<hr>
                <ui-icon-button @click="addContentHtml" tooltip="Insertar Contenido" size="small" icon="add" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentFile" tooltip="Insertar archivo" size="small" icon="attach_file" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentImage" tooltip="Insertar imagen" size="small" icon="photo" type="secondary"></ui-icon-button>
                <ui-icon-button @click="addContentMap" tooltip="Insertar Mapa" size="small" icon="map" type="secondary"></ui-icon-button>
                <ui-icon-button v-if="disable && !gameOn" @click="addContentPartida" tooltip="Insertar Resultado" size="small" icon="assignment" type="secondary"></ui-icon-button>
                <ui-icon-button v-if="" @click="taula_competicio" tooltip="Veure tabla de competicions per club" size="small" icon="view_module" type="secondary"></ui-icon-button>
                <ui-icon-button v-if="" @click="taula_inscrits" tooltip="Veure tabla d'inscripcions per club" size="small" icon="view_quilt" type="secondary"></ui-icon-button>
                <ui-icon-button v-if="" @click="taula_calendari" tooltip="Veure calendari de partides" size="small" icon="view_agenda" type="secondary"></ui-icon-button>
        </div>
        
        <ui-modal size="largeSquare" ref="uploadModal" title="Media Manager">
			<filemanager ref="upload" v-bind:pselected="selected"></filemanager>
			<div slot="footer">
                <ui-button @click="acceptModal('uploadModal')" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
                <ui-button @click="closeModal('uploadModal')">{{$i18n.t('modal.cancel')}}</ui-button>
            </div>
        </ui-modal>



	    </div>
    
</template>

<script>

import draggable from 'vuedraggable'
import VuePellEditor from 'vue-pell-editor'
import VuePellEditorConfig from '../config/pelleditor'
import FileManager from './FileManager.vue'
import MatchGenerator from './MatchGenerator.vue'


export default {
  	components: { draggable, VuePellEditor, 'filemanager':FileManager, MatchGenerator },
  	props: ['nodeId', "disableBlock"],
	data () {
		return {
			dataMap:'',
			isCompetitionVisible: false,
			generator:false,
			selected:{},
			loading:false,
			selectedCancel:'',
			codeOn: VuePellEditor.viewcode || false,
			places:[],
			teams:[],
			textbox_lloc: false,
			textbox_local: false,
			textbox_visitant: false,
			textbox_resultatvisitant: false,
			textbox_resultatlocal: false,
			mapbox:{
				style: 'mapbox://styles/mapbox/light-v9',
				center: [-96, 37.8],
				zoom: 3,
				accessToken:'sk.eyJ1IjoiYWxzYW5hbiIsImEiOiJjam9pd2FqeG8wY2c1M3BwZmVodGZpN3ExIn0.Zq0TtD1l9B5Vl3GV6BFLKg',
				geolocateControl:{
				  show: true,
				  position: 'top-left'
				},
				scaleControl:{
				  show: true,
				  position: 'top-left'
				},
				fullscreenControl:{
				  show: true,
				  position: 'top-left'
				}
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
			searchList:[],
			newOrder:[],
			nodeContent:[],
			newGame:{},
			local:[],
			visitant:[],
			buscadorJugadorLocal:[],
			buscadorJugadorVisitant:[],
			jugadorLocal:'',
			jugadorVisitant:'',
			equipLocal:[],
			equipVisitant:[],
		}
	},
	methods: {
		jugades(key) {
			if (!'partides' in this.nodeContent[key]) return false;
			var partides= this.nodeContent[key].partides;
			if ([2,4,8,16,32].indexOf(partides.length)<0) return false;
			var res= true;
			// si cada partida té resultat distint de zero (jugada (>0) o amb equip nul(-1) )
			return partides.reduce( (prev,curr)=>prev && (curr.resultatlocal+curr.resultatvisitant)!=0 , true );
		},
		deletePlayer:function(player, source){
			var vm = this;
			var partida = vm.newGame;
			
			if(source=="local"){
					vm.$http.delete('/participa/'+partida.id+'/'+player.id)
			        .then(function (response) {
			            vm.equipLocal = response.data.filter(function(obj){ return obj.equip === partida.local.id});
			        })
			        .catch(function (error) {
			            console.log(error);
			        });
			}
			if(source=="visitant"){
					vm.$http.delete('/participa/'+partida.id+'/'+player.id)
			        .then(function (response) {
						vm.equipVisitant = response.data.filter(function(obj){ return obj.equip === partida.visitant.id});
			        })
			        .catch(function (error) {
			            console.log(error);
			        });
			}
		},
		addPlayer: function(player, destination){
			var vm = this;
			var partida = vm.newGame;
			var jugador = {'equip':player.equip, 'nom':player.nom, 'cognoms':player.cognoms, 'id':player.id};
			

			if(destination=="local" && player){
				
				//vm.equipLocal.push(jugador);

				player.equip = partida.local.id;
				vm.$http.post('/participa/'+partida.id, player)
		        .then(function (response) {
		        	vm.equipLocal = response.data.filter(function(obj){ return obj.equip === partida.local.id});
		        	vm.jugadorLocal = '';
		        })
		        .catch(function (error) {
		            console.log(error);
		        });

			}
			
			if(destination=="visitant" && player){
				
				//vm.equipVisitant.push(player);

				player.equip = partida.visitant.id;
				vm.$http.post('/participa/'+partida.id, player)
		        .then(function (response) {
					vm.equipVisitant = response.data.filter(function(obj){ return obj.equip === partida.visitant.id});
					vm.jugadorVisitant = '';
		        })
		        .catch(function (error) {
		            console.log(error);
		        });

			}

		},
		onQueryChangePlayerLocal: function(query) {
            if (query.length < 3) {
                return;
            }
            
        	var vm = this;
	        vm.loading=true;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('/jugador/search/'+query, { cache: auth })
	        .then(function (response) {
	            vm.buscadorJugadorLocal = response.data;
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
            
        },
		onQueryChangePlayerVisitant: function(query) {
            if (query.length < 3) {
                return;
            }
            
        	var vm = this;
	        vm.loading=true;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('/jugador/search/'+query, { cache: auth })
	        .then(function (response) {
	            vm.buscadorJugadorVisitant = response.data;
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
            
        },
		onQueryChangeLocal: function(query) {
            if (query.length < 3) {
                return;
            }
            
        	var vm = this;
	        vm.loading=true;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('/equip/search/'+query, { cache: auth })
	        .then(function (response) {
	            vm.local = response.data;
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
            
        },
		onQueryChangeVisitant: function(query) {
            if (query.length < 3) {
                return;
            }
            
        	var vm = this;
	        vm.loading=true;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        vm.$http.get('/equip/search/'+query, { cache: auth })
	        .then(function (response) {
	            vm.visitant = response.data;
	            vm.loading=false;
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
		addNewGame: function(content) {
			var vm=this;
	        for(var i = 0; i < content.length; i++) {

				if(content[i].tipus == 'J') {
					vm.newGame = { 
						data:new Date(),
						lloc:'',
						local:'',
						visitant:'',
						resultatvisitant:'0',
						resultatlocal:'0',
						jerarquia:vm.nodeId,
						registreid: content[i].id};
				}
			}
		},
		openModal:function(ref, object, cancel, tipo) {
			this.$refs.upload.activate(tipo);
			this.selectedCancel = cancel;
			this.selected=object;
            this.$refs[ref].open();
        },
        acceptModal:function(ref) {

        	if(window.recoverFocus) window.recoverFocus.focus(); //recuperem el focus definit al arxiu de config del pelleditor.
        	VuePellEditor.components.pell.exec('insertImage', this.selected.url);
        	this.selected={};
            this.$refs[ref].close();
        },
        closeModal:function(ref) {
        	this.selected.url = this.selectedCancel;
        	this.selected={};
            this.$refs[ref].close();
        },
		saveContent: function(element) {

			var vm = this;
	        
	        vm.$http.post('/node/'+this.nodeId, element)
	        .then(function (response) {

	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
        openModalMatch(ref) {

            this.$refs[ref][0].open();
        },
        closeModalMatch(ref) {
            this.$refs[ref][0].close();
        },		
		editMatch: function(element) {
			var vm=this;
			vm.$router.push('/admin/partida/'+element.id);
			return;
		},
		getNode: function(){
			
	        var vm = this;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);

	    	vm.$http.get('/node/'+vm.nodeId, { cache: auth })
	        .then(function (response) {

	            vm.nodeContent = response.data;
	            vm.addNewGame(vm.nodeContent);
				var competitionNode = vm.nodeContent.find(node => node.tipus == "J") || {};
				vm.isCompetitionVisible = !competitionNode.json || JSON.parse(competitionNode.json).public == true;
				
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getPlaces: function(){
	        var vm = this;
	        vm.$http.get('trinquet')
	        .then(function (response) {
	            vm.places = response.data;
	            vm.places.unshift({id:null, nom:'---'});
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getTeams: function(){
	        var vm = this;
	        vm.$http.get('inscripcionsdecompeticio/'+vm.nodeId)
	        //vm.$http.get('equip')
	        .then(function (response) {
	            vm.teams = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		getList: function(listName) {
	        var vm = this;
	        //var url = objSearch.valor ? listName+'/search/'+objSearch.camp+'/'+objSearch.valor : listName;
	        var auth = !this.$store.getters.isAuthenticatedWithRole(0);
	        
	        vm.$http.get(listName, { cache: auth })
	        .then(function (response) {
	            vm.searchList = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		toggleVisibleCompetition: function(element){
			var vm = this;

	        vm.$http.post('/togglepartides/'+element.id, {public:!vm.isCompetitionVisible})
	        .then(function (response) {
				
				vm.isCompetitionVisible = !vm.isCompetitionVisible
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
		},
		/* /// sense us
		resetMatch: function() {
			var vm = this;
			
			var blockPartida = vm.nodeContent.find(function (obj) { return obj.tipus === 'J'; });

			if(blockPartida.partides!=null) {
				blockPartida.partides.forEach(function(element) {
				  element.selected=undefined;
				});
			}
			
			vm.addNewGame(vm.nodeContent);

			vm.textbox_lloc=false;
			vm.textbox_local=false;
			vm.textbox_visitant=false;
			vm.textbox_resultatvisitant=false;
			vm.textbox_resultatlocal=false;
			
			vm.closeModalMatch('matchedit');
			
		},
		*/
		saveBlock: function(block){
			
			var vm = this;

	        vm.$http.post('/node/'+vm.nodeId, block)
	        .then(function (response) {
	        	
	            vm.nodeContent = response.data;
	            vm.addNewGame(vm.nodeContent);
	
	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		deleteMatch: function(element) {
			
			if (!confirm("COMPTE !!! \n¿Estas totalment segur de voler esborrar aquesta partida?")) return;

			var vm= this;

			vm.$http.delete('/partida/'+element.id)
	        .then(function (response) {
	            
	            vm.getNode();
        		vm.setOrder(true);
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	        
		},
		deleteAllMatches() {
			if(!confirm("estas segur d'esborrar totes les partides?")) return;
			if(!confirm("estas absolutament segur de voler esborrar totes les partides i resultats per poder tornar-les a generar?")) return;
			var vm=this;
			var blockPartida = vm.nodeContent.find(function (obj) { return obj.tipus === 'J'; });
			if (blockPartida) blockPartida.partides.forEach( ({id,...args})=>{
				vm.$http.delete('/partida/'+id).catch( err=>console.log(err) );
			} )
			blockPartida.partides= [];
			alert('Partides anul.lades...')
		},
		removeContent: function(element) {

			var vm=this;

			if (!confirm("Confirma acció d'esborrar")) return;
			
  			vm.$http.delete('/node/'+vm.nodeId+'/element/'+element.id)
	        .then(function (response) {
	            vm.nodeContent = response.data;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });

		},
		setOrder: function(initial){
			var vm=this;
			
			vm.newOrder = [];
			
			if(vm.nodeContent) {
			    for (var node = 0; node < vm.nodeContent.length; node++){
						vm.newOrder.push( { 'id': vm.nodeContent[node].id, 'ordre' : node } );
			    }
			}
			
			if(!initial) {
				vm.$http.post('/node/'+vm.nodeId+'/ordre', vm.newOrder)
		        .then(function (response) {
		        	
		            vm.nodeContent = response.data;
		
		        })
		        .catch(function (error) {
		            console.log(error);
		        });
			}
			
		},
		auth:function() {
			return this.$store.getters.isAuthenticated && this.$store.getters.role>=10;
		}, 
		addContentImage: function() {
			this.saveBlock({ tipus:'I', url:'' });
		},
		addContentMap: function() {
			this.saveBlock({ tipus:'M', json:'' });
		},
		addContentHtml: function() {
			this.saveBlock({ tipus:'H', titol:'', contingut:'' });
		},
		addContentFile: function() {
			this.saveBlock({ tipus:'F', titol:'', url:'' });
		},
		addContentPartida: function() {
			//this.saveBlock({ tipus:'J', partides: [], newGame:{ data:new Date(), lloc:'', local:'',	visitant:'', resultatvisitant:'', resultatlocal:'', blocId:''} });
			this.saveBlock({ tipus:'J', partides: []});
		},
	    listOn: function(nom) {
	    	var vm = this;
	    	
	    	var tipos = ['jugadors','equips','clubs'];
	    	
	    	if(!tipos.includes(nom)) return false;
	    	
	      	for(var i = 0; i < vm.nodeContent.length; i++) {
				if(vm.nodeContent[i].tipus==nom) {

					if(vm.searchList.length == 0) {
						
						vm.getList(vm.nodeContent[i].tipus);
						
					}
					return true;
					
				}

			}
			return false;
	    },
	    generatore: function(){
	    	var vm = this;
	    	vm.generator = !vm.generator;
	    	
	    	vm.getNode();
        	vm.setOrder(true);
        	
	    },
	    setMap:function(map){
	    	var vm=this;
	    	vm.dataMap = map.lat+','+map.lng;
	    },
	    taula_inscrits:function() {
	    	window.open('/api/resuminscrits/'+this.nodeContent[0].jerarquia)
	    },
	    taula_competicio:function() {
	    	window.open('/api/resumnode/'+this.nodeContent[0].jerarquia)
	    },
	    taula_calendari:function() {
	    	window.open('/api/resumcalendari/'+this.nodeContent[0].jerarquia)
	    },
	    nextPhase(key) {

	    	/* procediment de creació del bloc de la següent fase */
	    	/*  1. cree un nou bloc de contingut J */
	    	/*  2. seleccione equips guanyadors */
	    	
	    	var guanyadors= [];
	    	var vm= this;
		    if (!'partides' in vm.nodeContent[key]) return false;
	    	vm.nodeContent[key].partides.forEach( pt=> guanyadors.push( (pt.resultatlocal>pt.resultatvisitant) ? parseInt(pt.local.id) : parseInt(pt.visitant.id) ) );
			vm.$http.post('/nextphase/'+vm.nodeContent[key].id, guanyadors.join(',') )
	        .then(function (response) {
	            //vm.$router.go(); // reload
	            vm.generator= true;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	    	
	    },
		
	},
	mounted: function () {
    	if(this.$store.getters.isAuthenticatedWithRole(0)) {
			this.getTeams();
			this.getPlaces();
    	}
		

    	var vm=this;
		vm.getNode();
    	vm.$eventHub.$on('setMapData', vm.setMap);
    	
	},
	watch: { 
      	nodeId: function(newVal, oldVal) {
		  this.teams = [];
          this.getNode();
		  this.getTeams();
          this.setOrder(true);
          this.generator=false;
        },
        authOn: function(){
        	if(this.$store.getters.isAuthenticatedWithRole(0)) {
				this.getTeams();
				this.getPlaces();
        	}

        }
	},
	 computed: {
	 	disable: function() {
	 		var root = this.$route.path.split('/')[2];
	 		var vm=this;
	 		return root == vm.disableBlock;
	 	},
	 	containsCompetitionNode: function() {
	 		return this.nodeContent.filter( e=>e.tipus=='J' ).length>0;
	 	},
	 	authOn: function() {
	 		return this.$store.getters.isAuthenticatedWithRole(0);
	 	},
	    gameOn: function () {
	    	var vm = this;
	      	for(var i = 0; i < vm.nodeContent.length; i++) {
				if(vm.nodeContent[i].tipus=="J") {
					return true;
				}

			}
			return false;
	    }
	    
	},
	created() {
    	this.$eventHub.$on('generator-bool', this.generatore);
	},
	beforeDestroy() {
	    this.$eventHub.$off('generator-bool');
	}
}
</script>


<style lang="less">

@import "../assets/less/defines.less";

.partida{
	width:100%;
	h2 {padding:0;margin:0!important;}
	

	.form {
		display:flex;
		flex-wrap:wrap;
		&>label {
			max-width: ~"calc(50% - 20px)";
	    	margin-right: 20px;
	    	min-width: 220px;
	    	width:100%;
	    	color: rgba(0,0,0,.54);
	    	@media(max-width:@screenTablet) {
	    		max-width:initial;
			}
		}
		h3{display:block;width:100%;}
	}
	.ui-textbox, .ui-autocomplete, .ui-select{
		max-width: ~"calc(50% - 20px)";
    	margin-right: 20px;
    	min-width: 220px;
    	width:100%;
    	
    	@media(max-width:@screenTablet) {
    		max-width:initial;
		}
    	
	}
	section {
		display: flex;
    	width: 49%;
		.ui-select{
			max-width: ~"calc(90% - 20px)";
		}
		button{
			margin-top: 20px;
		}
	}
	table {
		td, th {
		    padding: .5rem 0 0 0!important;
		    text-align:center;
		    @media(max-width:@screenTablet) {
	    		font-size:0.8em;
			}
		}
		
		tr.selected {
		    background-color: #e9e9e9;
		}


	}
}

.datepicker {
    display: block!important;
    margin-top: -3px;
        input {
        	color:#232323!important;
			width: 100%!important;
		    font-family: 'Rambla', cursive!important;
		    display: block!important;
		    font-size: 2em!important;
		    border: none!important;
		    border-bottom: 1px dashed #ccc!important;
		    margin-bottom:0.5em!important;
		    min-height: 42px!important;
		}
}

.pell-content {
	height:initial!important;
	min-height:100px;
	border-bottom: 1px dashed #ccc;
	img{
		max-width:100%;
	}
}

.nodeContentEditable, .eventSelected, .news {

	h2 {
		font-size: 2em;
    	margin: 0.5em 0 0 0;
    	font-family: 'Rambla', cursive;
    	width: 100%;
    	display: block;
	}
	
	h4 {
		font-size: 1em;
    	margin: 0.5em 0 0 0;
    	font-family: 'Rambla', cursive;
    	width: 100%;
    	display: block;
	}

	.nodeContentElement{
			padding: 10px;
		    margin-left: -10px;
		    position: relative;
		    align-items: center;
		    box-sizing: border-box;
		    margin: 10px 0;
		    border: 1px solid @fedcolor;
			@media print {    
		    	border:0;
			}
		    &.autenticated {
			margin:20px 0;
			border:none;
			padding:0;
		}
		&.floating {
		    border: 2px dashed #87212e;
		    opacity:0.7;
		    .drag{
				background-color:#87212e;
			}
		}
		
		a { text-decoration:underline; }
		
		.mapa {
			iframe {
				width:100%;
				height:400px;
				border: 1px solid @fedcolor;
			}
		}
		
		.teaserImg, &>img {
		    max-width: 100%;
		    max-height: 480px;
		    margin: 0 auto 20px auto;

		    &.wide {
		    	margin:0;
		    	max-width:initial;
		    	object-fit: cover;
			    max-height: 480px;
			    width: 100%;
			    object-position: 50% 50%;
		    }
		    
		}
		
		.ql-editor {
			font-family: 'Yantramanav', cursive;
	    	font-size: 1.4em;
		}
		.wysiwyg {
		    margin: 0px;
		}
		#quill-container {
	    	height: initial;
		}
		.ql-container.ql-snow {
	    	border: none;
		}
		article {
			width:100%;
			& * {
				max-width:100%;
			}
		}
		textarea, input {
		    width: 100%;
		    font-family: 'Rambla', cursive;
		    display: block;
		    font-size: 2em;
		    border: none;
		    border-bottom: 1px dashed #ccc;
		    margin-bottom:0.5em;
		    min-height: 42px;
		}
		div#quill-container {
		    border: 1px dashed #cccccc;
		    border-top: none;
		}
		picture {
			width: 100%;
		    display: block;
		    margin: 0 auto;
		    position: relative;
		    overflow: hidden;
		    height: 100vw;
		    max-height: 480px;
		    background-size: cover;
		    background-position: 50% 33%;
		    margin-bottom: 20px;
		    transition: max-height 1s ease;
		    cursor:pointer;
		}
		
	}
	
	.drag {
	    cursor: n-resize;
	    width: 10px;
	    height: 10px;
	    margin-right: 10px;
	    margin-left: -20px;
	    border: 1px solid #87212e;
	    position:absolute;
	    top: -1px;
    	@media print {    
	    	visibility:hidden;
		}
	    &:hover{
	    	border: 2px solid #87212e;
	    }
	}
	.remove {
		cursor: pointer;
	    width: 10px;
	    height: 10px;
	    margin-right: 10px;
	    margin-left: -40px;
	    position: absolute;
    	@media print {    
	    	visibility:hidden;
		}
	    top: -1px;
		&:hover {
		    &:before, &:after {	width: 2px; }
		}
	    &:before, &:after {
		    position: absolute;
		    left: 5px;
		    content: ' ';
		    height: 10px;
		    width: 1px;
		    background-color: #87212e;
		}
		&:before {
		  transform: rotate(45deg);
		}
		&:after {
		  transform: rotate(-45deg);
		}
	}
}

.subForm {
    width: 100%;
    display: flex;
    justify-content: space-between;
    flex-wrap:wrap;
    border: 1px dashed #cccccc;
    padding: 20px;
    margin-bottom:20px;
    table {
    	width:49%;
    }

}

.results tr.odd {
	background-color: #ececec;
	@media print {
		-webkit-print-color-adjust: exact; 
	}
}

</style>

