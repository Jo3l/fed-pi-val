<template>
    <transition name="fade">
    	
	    <div class="test">
		
    	<h1>Generador de partides</h1>
    		
			
		<ui-switch id="trofeu" v-model="esTrofeu" >Lliga</ui-switch>

		<ui-select
                has-search
                floating-label
                placeholder="Busca l'equip visitant"
        		search-placeholder="Escriu el nom de l'equip"
                label="Equip"
                :keys="{ label: 'nom'}"
                :options="equipssel"
                v-model="equipSeleccionat"
                error="This field is required"
        		@query-change="onQueryChangeEquip"
        		@select="afegirEquip"
          ></ui-select>
          
          <div v-for="(equip,key) in equips" class="ui-button ui-button--type-secondary ui-button--color-red ui-button--icon-position-left ui-button--size-small">  
        	{{equip.nom}} <ui-icon-button icon="delete" size="small" type="secondary" @click="borraEquip(equip,key)"></ui-icon-button>
          </div>
			
{{ (equips.length>0)?(equips.length-1)*2 : 0 }}
			<div id="dates">
				<div v-for="index in (equips.length>0)?(equips.length-1)*2 : 0" v-if="equips && equips.length">
					<label>Jornada {{index}}:
						<vue-datepicker-local class="dplocal" v-model="dates[index]" :local="datePickerOptions" format="DD-MM-YYYY"></vue-datepicker-local>
					</label>
				</div>
			</div>
			
			<pre id="result">
				
				{{equips}}
				
				{{dates}}
				
			</pre>
	    </div>
    </transition>
</template>

<script>
import draggable from 'vuedraggable'
import VueDatepickerLocal from 'vue-datepicker-local'

export default {
  	components: { draggable, VueDatepickerLocal },
	name: 'Test',
	data () {
		return {
			/// http://tournamentscheduler.net
			roundrobin: {
				//3 equips, 3 partides 3 jornades
				3:{
				  0:[[0,1]],
				  1:[[0,2]],
				  2:[[1,2]]
				},
				//4 equips, 6 partides 3 jornades
				4:{
				  0:[[0,1],[2,3]],
				  1:[[0,2],[1,3]],
				  2:[[0,3],[1,2]]
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
				  0:[[0,3],[2,5],[1,4]],
				  1:[[0,2],[1,3],[4,5]],
				  2:[[0,5],[3,4],[1,2]],
				  3:[[0,1],[2,4],[3,5]],
				  4:[[0,4],[1,5],[2,3]]
				},
				//7 equips, 21 partides 7 jornades
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
				  0:[[7,2],[5,0],[4,6],[3,1]],
				  1:[[7,3],[1,4],[6,5],[0,2]],
				  2:[[7,5],[4,2],[3,0],[1,6]],
				  3:[[7,1],[6,3],[0,4],[2,5]],
				  4:[[7,0],[2,6],[5,1],[4,3]],
				  5:[[7,6],[0,1],[2,3],[5,4]],
				  6:[[7,4],[3,5],[1,2],[6,0]]
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
			esTrofeu: false,
			dates: [],
			eixida: '',
			datePickerOptions: {
				yearSuffix: '',
				monthsHead: this.$parent.$i18n.t('calendar.months'),
				dow: eval(this.$parent.$i18n.t('calendar.mondayFirst')),
        		hourTip: this.$parent.$i18n.t('calendar.hourTip'),
    			minuteTip: this.$parent.$i18n.t('calendar.minuteTip'),
        		secondTip: this.$parent.$i18n.t('calendar.secondTip'),
        		months: this.$parent.$i18n.t('calendar.monthsShort'),
    			weeks: this.$parent.$i18n.t('calendar.weekShort')
			}
		}
	},
	methods: {
		borraEquip: function(e,k) { 
			var vm= this;
			vm.equips= vm.equips.filter( (elm) => { return (elm.id!=e.id); } );
			console.log(vm.equips)
		},
		afegirEquip: function(){
			var vm= this;
			var abort= false;
			vm.equips.forEach( (eq) => { if (eq.id==vm.equipSeleccionat.id) abort=true; } )
			if (abort) return alert("Ja estava en la llista");
			vm.equips.push(vm.equipSeleccionat)
			console.log(vm.equipSeleccionat,vm.equips.length+' equips ara');
			console.log(vm.equips)
			vm.equipSeleccionat= '';
			var jornades= (vm.equips.length>0)?(vm.equips.length-1)*2 : 0;
			vm.dates.length= jornades;
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
		genera: function() {
			var vm= this;
			var l= vm.equips.length;
			var rr= vm.roundrobin[l];
			var html='';
			if (vm.esTrofeu) {
				html= 'trofeu';
				// casuistica: 2-16 equips. cas de potències de dos està clar.
				var eqs= vm.equips;
				console.log(this.seeding(l))
			} else {
				// generació de partides de lliga (tots contra tots, anada i tornada) amb taula pregenerada :
				var anades=[], tornades=[];
				var jornada= 1;
				for(var j in rr) {
					html+='<h3>jornada '+(jornada++)+'</h3><ul>';
					j= rr[j];
					for(var p in j) {
						html+= '<li>'+vm.equips[j[p][0]].nom+' - '+vm.equips[j[p][1]].nom+'</li>';
					}
					html+= '</ul>';
				}
				for(var j in rr) {
					html+='<h3>jornada '+(jornada++)+'</h3><ul>';
					j= rr[j];
					for(var p in j) {
						html+= '<li>'+vm.equips[j[p][1]].nom+' - '+vm.equips[j[p][0]].nom+'</li>';
					}
					html+='</ul>';
				}
/*
				// generació de partides de lliga (tots contra tots, anada i tornada) :
				var anades=[], tornades=[];
				for (var i= 0; i<l; i++)
					for (var j= i+1; j<l; j++) {
						anades.push(vm.equips[i].nom+'-<span style="font-weight:bold">'+vm.equips[j].nom+'</span>');
						tornades.push(vm.equips[j].nom+'-<span style="font-weight:bold">'+vm.equips[i].nom+'</span>');
					}
				html+= 'anades:<ul><li>'+anades.join('<li>')+'</ul>tornades:<ul><li>'+tornades.join('<li>')+'</ul>';
*/
			}
			document.querySelector("#result").innerHTML= html;
		},
		onQueryChangeEquip: function(query) {
            if (query.length < 3) return;

        	var vm = this;
	        vm.loading=true;
	        vm.$http.get('/equip/search/'+query, { cache: false })
	        .then(function (response) {
	            vm.equipssel = response.data;
	            vm.loading=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
            
        }
    },
	watch: {
		esTrofeu: function(n) { document.querySelector('#trofeu .ui-switch__label-text').innerHTML= n?'Trofeu':'Lliga'; },
		dates: function(n) {
			var d= new Date( n );
			console.log(d);
			for(var i=0;i<this.dates.length;i++) {
				d= d.addDays(7);
				this.dates[i]= d;
				console.log( d, this.dates );
			}
			console.dir(this.dates)
		}
	},
	mounted: function () {
	}
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

#dates > div { width:29% !important; }
#dates { display:flex; flex-flow: row wrap; align-content: space-between; }

</style>
