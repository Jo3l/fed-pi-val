<template>
    <transition name="fade">

		<div v-if="!acabat" class="generator">
			<h3>Generador de partides</h3>
		
			
			<ui-switch id="trofeu" v-model="esTrofeu" @change="genera()"> {{esTrofeu?'Trofeu':'Lliga'}}</ui-switch>
			
			<ui-switch id="tornades" v-model="tornades" @change="genera()"> Generar tornades</ui-switch>
			
			<ui-select
			    has-search
			    floating-label
			    placeholder="Busca l'equip"
				search-placeholder="Escriu el nom de l'equip"
			    label="Equip"
			    :keys="{ label: 'nom'}"
			    :options="equipssel"
			    v-model="equipSeleccionat"
			    error="Camp obligatori"
				@query-change="onQueryChangeEquip"
				@select="afegirEquip"
				v-if="true"
			></ui-select>
			
			<table class="equips">
				<caption>Equips Inscrits</caption>
				<thead>
					<tr>
						<th></th>
						<th>Club</th>
						<th>Equip</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<draggable style="display:contents" v-model="equips" @end="genera()">
					<tr v-for="(equip,key) in equips" :class="!equip.nom?'sepa':''">
						<td><ui-icon icon="unfold_more"></ui-icon></td>
						<td>{{equip.nomclub}}</td>
						<td>{{equip.nom}}</td>
						<th>
							 <ui-icon-button icon="delete" size="small" type="secondary" @click="borraEquip(equip,key)"></ui-icon-button>
						</th>
					</tr>
				</draggable>
				</tbody>
			</table>

		<br/>
		<ui-button color="fedpival" icon="done_all" @click="afegirSeparador">Afegir un separador de grup</ui-button>
			
			<div v-if="false" v-for="(equip,key) in equips" class="ui-button ui-button--type-secondary ui-button--color-red ui-button--icon-position-left ui-button--size-small">  
			{{equip.nom}} <ui-icon-button icon="delete" size="small" type="secondary" @click="borraEquip(equip,key)"></ui-icon-button>
			</div>

			<br><br>
			<!--h2 v-if="esTrofeu && equips.length==16">Octaus de final</h2>
			<h2 v-if="esTrofeu && equips.length==8">Quarts de final</h2>
			<h2 v-if="esTrofeu && equips.length==4">Semifinals</h2>
			<h2 v-if="esTrofeu && equips.length==2">Final</h2-->
			<h2 v-if="esTrofeu">Trofeu</h2>
			<h2 v-else>Lliga</h2>
			<h3 v-if="esTrofeu && ![2,4,8,16,32,64].includes(equips.length)"><ui-icon icon="sentiment_dissatisfied"></ui-icon> El número actual d'equips no permet emparellaments per a un trofeu</h3> 
			<div id="lesdates">
				
				<div v-if="jornades && jornades.length" v-for="(jor,index) in jornades" class="jornada">
					
					<ui-datepicker
						v-if="jor?true:false"
		                placeholder="$i18n.t('calendar.dateTip')"
		                :start-of-week="datePickerOptions.dow"
		                v-model="jor.data" 
		                :lang="datePickerOptions"
		                @input="inpChangedForCode(index,jor.data)"
		            >Jornada {{ index+1 }}:</ui-datepicker>
					
					<div v-if="jor?true:false">
						<div v-if="jor.enfrontaments.length" v-for="(j,index) in jor.enfrontaments">
							<span class="local">{{ j[0].nom }}</span> - <span class="visitant">{{ j[1].nom }}</span>
							<br/>
						</div>
					</div>
					
				</div>
				
			</div>
			
			<br/><br/>
	
			<ui-button v-if="jornades.length>0 && jornades[0]" color="fedpival" icon="done_all" @click="enviar">Generar aquestes partides</ui-button>

	    </div>
    </transition>
</template>

<script>
import draggable from 'vuedraggable'

export default {
  	components: { draggable },
  	props: ['nodeId', 'blockId'],
	name: 'generator',
	data () {
		return {
			/// http://tournamentscheduler.net
			roundrobin: {
				1: {},
				2:{
					0:[[0,1]]
				},
				//3 equips, 3 partides 3 jornades
				3:{
				  0:[[0,1]],
				  1:[[0,2]],
				  2:[[1,2]]
				},
				//4 equips, 6 partides 3 jornades
				4:{
				  0:[[0,1],[2,3]],
				  1:[[0,3],[1,2]],
				  2:[[0,2],[1,3]]
				},
				//5 equips, 10 partides 5 jornades
				5:{
				  0:[[0,1],[2,3]],
				  1:[[0,2],[1,4]],
				  2:[[0,3],[2,4]],
				  3:[[0,4],[1,3]],
				  4:[[1,2],[3,4]]
				},
				//6 equips, 15 partides 5 jornades
				6:{
				  0:[[0,1],[2,3],[4,5]],
				  1:[[5,0],[1,2],[3,4]],
				  2:[[0,4],[3,1],[2,5]],
				  3:[[0,3],[4,2],[5,1]],
				  4:[[2,0],[1,4],[3,5]]
				},
				//7 equips, 21 partides 7 jornades
				'6b':{
				  0:[[1,0],[3,2],[5,4]],
				  1:[[0,5],[2,1],[4,3]],
				  2:[[4,0],[1,3],[5,2]],
				  3:[[3,0],[2,4],[1,5]],
				  4:[[0,2],[4,1],[5,3]]
				},
				7:{
				  0:[[0,1],[2,3],[4,6]],
				  1:[[0,3],[2,5],[1,6]],
				  2:[[3,5],[0,6],[2,4]],
				  3:[[1,3],[5,6],[0,4]],
				  4:[[0,5],[1,4],[2,6]],
				  5:[[3,4],[0,2],[1,5]],
				  6:[[3,6],[1,2],[4,5]]
				},
				8:{
				  0:[[0,1],[2,3],[4,5],[6,7]],
				  1:[[1,2],[3,4],[5,6],[7,0]],
				  2:[[0,3],[2,5],[4,7],[6,1]],
				  3:[[1,4],[3,6],[5,0],[7,2]],
				  4:[[0,2],[4,6],[1,3],[5,7]],
				  5:[[6,0],[2,4],[3,5],[7,1]],
				  6:[[4,0],[2,6],[5,1],[7,3]]
				},
				'8b':{
				  0:[[1,0],[3,2],[5,4],[7,6]],
				  1:[[2,1],[4,3],[6,5],[0,7]],
				  2:[[3,0],[5,2],[7,4],[1,6]],
				  3:[[4,1],[3,6],[5,0],[7,2]],
				  4:[[0,2],[4,6],[1,3],[5,7]],
				  5:[[6,0],[2,4],[3,5],[7,1]],
				  6:[[4,0],[2,6],[5,1],[7,3]]
				},
				9:{
				  0:[[1,4],[0,3],[7,2],[6,8]],
				  1:[[1,7],[6,0],[2,4],[3,5]],
				  2:[[1,6],[8,7],[2,0],[5,4]],
				  3:[[1,8],[2,6],[3,7],[5,0]],
				  4:[[1,3],[5,2],[4,8],[0,7]],
				  5:[[1,0],[6,4],[8,5],[2,3]],
				  6:[[1,2],[3,8],[5,6],[4,7]],
				  7:[[1,5],[4,3],[0,8],[7,6]],
				  8:[[0,4],[7,5],[6,3],[8,2]]
				},
				10:{
				  0:[[2,6],[4,5],[7,1],[0,8],[9,3]],
				  1:[[2,9],[3,0],[8,7],[1,4],[5,6]],
				  2:[[2,8],[1,3],[5,9],[6,0],[4,7]],
				  3:[[2,5],[6,1],[4,8],[7,3],[0,9]],
				  4:[[2,4],[7,6],[0,5],[9,1],[3,8]],
				  5:[[2,0],[9,7],[3,4],[8,6],[1,5]],
				  6:[[2,7],[0,4],[9,6],[3,5],[8,1]],
				  7:[[2,1],[5,8],[6,3],[4,9],[7,0]],
				  8:[[2,3],[8,9],[1,0],[5,7],[6,4]]
				}
			},
			inici: new Date(),
			loading:false,
			equipSeleccionat: '',
			equips: [],
			equipssel: [],
			equipsnosel: [],
			jornades:[],
			esTrofeu: false,
			tornades:true,
			acabat:false,
			eixida: '',
		    columnsEquips:[
		    	{
	                label: 'Club',
	                field: 'nomclub',
	                html: false,    
	            },
	            {
	                label: 'Nom',
	                field: 'nom',
	                html: false,    
	            }
	        ],
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
		borraEquip: function(e,k) { 
			if (!confirm('Se eliminarà aquest equip d\'aquesta competició. OK?')) return;
			var vm= this;
			vm.$http.delete('/eliminaequip/'+e.id, { cache: false })
			.then(function (response) {
				vm.equips= vm.equips.filter( (elm) => { return (elm.id!=e.id); } );
				vm.equipsnosel.forEach( (a,b)=>{ 
					if (a.id==e.id) vm.equipsnosel.splice(b,1); 
				} )
				vm.equipssel.push(e);				
				this.genera();
			})
			.catch(function (error) {
			    console.log(error);
			});
		},
		afegirSeparador: function() {
			var vm= this;
			vm.equips.push(0);
			this.genera();			
		},
		afegirEquip: function(){
			var vm= this;
			var abort= false;
			vm.equips.forEach( (eq) => { if (eq.id==vm.equipSeleccionat.id) abort=true; } )
			if (abort) return alert("Ja estava en la llista");
			vm.equips.push(vm.equipSeleccionat)
			vm.equipssel.forEach( (a,b)=>{ 
				if (a.nom==vm.equipSeleccionat.nom) vm.equipssel.splice(b,1); 
				vm.equipsnosel.push(vm.equipSeleccionat);				
			} )
			vm.equipSeleccionat= '';
			var numjornades= vm.roundrobin[vm.equips.length].length || 1;
			vm.jornades.length= numjornades*2;
			this.genera();
		},
		seeding: function(numPlayers){
			function nextLayer(pls){
				var out=[];
				var length = pls.length*2+1;
				pls.forEach(function(d){
					out.push(d);
					out.push(length-d);
				});
				return out;
			}
			var rounds = Math.log(numPlayers)/Math.log(2)-1;
			var pls = [1,2];
			for(var i=0;i<rounds;i++){
				pls = nextLayer(pls);
			}
			return pls;
		},
		trofeu: function(eqs) {
			// casuistica: 2-16 equips. cas de potències de dos està clar.
			var vm= this;
			var l= eqs.length;
			var grup= eqs[0].grup;
			var enfrontaments= this.seeding(l)
			if (enfrontaments.length!=l) return console.error('num equips incorrecte ',l,'<>',enfrontaments.length);
			var jornada= vm.jornades.length;
			var jorobj= { data: new Date(), enfrontaments: [] }
			var tornades= [];
			while (enfrontaments.length) {
				var a= enfrontaments.pop();
				var b= enfrontaments.pop();
				jorobj.enfrontaments.push( [ eqs[a-1], eqs[b-1] ] );
				if (vm.tornades) tornades.push( [ eqs[b-1], eqs[a-1] ] ); // si hem d'afegir les tornades...
			}
			jorobj.grup= grup;
			vm.jornades[jornada]= jorobj;
			jornada++;
			if (vm.tornades) vm.jornades[jornada] = { data: new Date(), enfrontaments: tornades, grup: grup };
		},
		lliga: function(eqs) {
			// generació de partides de lliga (tots contra tots, anada i tornada) amb taula pregenerada :
			var vm= this;
			var grup= eqs[0].grup;
			var l= eqs.length;
			var rr= vm.roundrobin[l];
			var anades=[], tornades=[];
			var jornada= vm.jornades.length;
			for (var j in rr) {
				if (!vm.jornades[jornada]) vm.jornades[jornada]=[];
				j= rr[j];
				var jorobj= { data: new Date(), enfrontaments:[] }
				for (var p in j) {
					jorobj.enfrontaments.push([ eqs[j[p][0]],eqs[j[p][1]] ])
				}
				jorobj.grup= grup;
				vm.jornades[jornada]= jorobj;
				jornada++;
			}
			if (vm.tornades) { // si hem d'afegir les tornades...
				for(var j in rr) {
					if (!vm.jornades[jornada]) vm.jornades[jornada]=[];
					j= rr[j];
					var jorobj= { data: new Date(), enfrontaments:[] }
					for(var p in j) {
						jorobj.enfrontaments.push([ eqs[j[p][1]],eqs[j[p][0]] ])
					}
					jorobj.grup= grup;
					vm.jornades[jornada]= jorobj;
					jornada++;
				}
			}
		},
		genera: function() {
			var vm= this;
			var html='';
			vm.jornades= [];
			vm.ultimagenera= new Date();
			var grups= [];
			var eqs= [...vm.equips]; /// copia equips per a preparar els grups amb separadors
			var s= [];
			var id_grup=0;
			while (eqs.length) {
				var elm = eqs.shift()
				if (elm) {
					elm.grup= id_grup;
					s.push(elm);
				} else {
					if (s.length) {
						grups.push(s);
						s= [];
						id_grup++;
					}
				}
			}
			if (s.length) grups.push(s);
			//console.info(grups)
			grups.forEach( (grup) => {
				if (vm.esTrofeu) {
					this.trofeu(grup); //vm.equips);
				} else {
					this.lliga(grup); //vm.equips);
				}
			} );
			vm.ultimagenera= new Date();
			
			this.inpChangedForCode(0,new Date(),true)
		},
        inpChangedForCode: function(idx,d,forza=false) { 
        	var vm = this;
			if (!forza && "ultimagenera" in vm) {
				var timeDiff = Math.abs( (new Date()).getTime() - vm.ultimagenera.getTime() );
				var diff = Math.ceil(timeDiff / (1000)); 
				// si menos de 3 segons, no re-execute
				if (diff<3) return; //massa prompte
			}
			vm.ultimagenera= new Date();
        	if (!d) return console.log('no hi ha data');
        	for( var i= idx+1; i < this.jornades.length; i++ ) if (this.jornades[i].data) this.jornades[i].data= null;
        	var inid= new Date( d );
        	var d= new Date( d );
        	var grup= this.jornades[0].grup;
			for( var i= idx+1; i < this.jornades.length; i++ ) {
				if (grup!= this.jornades[i].grup) {
					d= new Date(inid);
					d.addDays(-7);
					grup= this.jornades[i].grup;
					console.log('canvi grup',grup,d)
				}
				vm.jornades[i].data= (new Date(d.addDays(7).toString())).toString();
			}
			vm.jornades.push(vm.jornades.pop());
			console.dir(vm.jornades)
        },
		onQueryChangeEquip: function(query) {
            if (query.length < 3) return;
        	var vm = this;
	        vm.loading=true;
	        vm.$http.get('/equip/search/'+query, { cache: false })
	        .then(function (response) {
	            vm.equipssel = response.data;
	            vm.equipssel.push({id:0,nom:'__Descans A__'});
	            vm.equipssel.push({id:0,nom:'__Descans B__'});
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
        },
        enviar: function() {
        	var vm= this;
        	vm.jornades.forEach(function(a){ 
        		a.datacurta= (new Date(a.data)).toISOString().substring(0,10).replace(/-/g,'');
        		//window.enf= a.enfrontaments;
        		if (a.enfrontaments) a.enfrontaments= a.enfrontaments.filter(function(a){ return a[0].id!=0 && a[1].id!=0 });
        	})
        	vm.jornades.bloc= vm.blockID;
        	vm.jornades.node= vm.nodeId;
        	vm.$http.post('/equip/genera?node='+vm.nodeId+'&bloc='+vm.blockId, JSON.stringify(vm.jornades) )
        	.then(function (response) {
	        	vm.acabat= true;
	        	vm.$eventHub.$emit('generator-bool');
	        })
	        .catch(function (error) {
	            console.log(error);
	        });

        },
        getEquips: function() {
	        var vm = this;
	        vm.$http.get('/inscripcionsdecompeticio/'+vm.nodeId, { cache: false })
	        .then(function (response) {
	            
	            vm.equips = response.data;
				vm.genera(); // afegit per a que genere partides amb els equips ja existents al iniciar el component
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });

        }
        
    },
	watch: {
		esTrofeu: function(n) { document.querySelector('#trofeu .ui-switch__label-text').innerHTML= n?'Trofeu':'Lliga'; }
	},
	mounted: function () {
		this.getEquips();
	}
}

</script>

<style lang="less" scoped>

@import "../assets/less/defines.less";
	
.generator {
	width:100%;
	border:1px dashed @fedcolor;
	padding:10px;
}
	
.test { margin: 0 20px; text-align:center; }

.local, .visitant { 
    border-radius: .125rem;
    border: none;
    cursor: default;
    display: inline-block;
    font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica, Arial, sans-serif;
    font-size: .875rem;
    font-weight: 600;
    padding: 8px;
    margin: 6px 0;
    text-transform: uppercase;
}

.local { background-color:#ffe5dd; }
.visitant { background-color:#ffc5c5; }

.jornada { padding-bottom:4px; text-align:center; }
.sepa { background:red; }
.sepa td:after { content:"_____"; }

.equips {
	width:100%;
	cursor:grab;
	&:active {
		cursor: grabbing;
	}
	caption{
	    text-align: center;
	    font-size: 1.1em;
	    font-weight: bolder;
	}
	tr{
		border: 1px solid #e2e2e2;
		padding:10px;
	}
	th {
		padding:10px!important;
	}
}
	
</style>