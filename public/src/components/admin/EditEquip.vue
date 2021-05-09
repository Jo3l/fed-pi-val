<template>

	<div class="content">

		<section class="overflow-hidden insert">
			<ui-textbox
				required
				:minlength="1"
				error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Nom del equip"
				type="text"
	            v-model="equip.nom"
	            :disabled="readonly === true"
			 ></ui-textbox>
		</section>

		<div class="insert grey">
		<h3>Jugadors</h3>
		<table class="table results">
			<thead>
				<tr>
					<th>Nº Llicència</th>
					<th>Nom</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(element,index) in jugadors">
					<td>{{element.numsoci}}</td>
					<td>{{element.nom}}</td>
					<td>
						<ui-icon-button v-if="readonly === false" icon="delete" size="small" type="secondary" @click="deletePlayer(element.id)"></ui-icon-button>
					</td>
				</tr>
			</tbody>
		</table>

		<ui-alert type="warning" v-show="jugadors && jugadors.length<minimjugadors">
            Falt{{ ((minimjugadors-(jugadors?jugadors.length:0))==1?'a':'en') }} {{minimjugadors-(jugadors?jugadors.length:0)}} jugador{{ ((minimjugadors-(jugadors?jugadors.length:0))==1?'':'s') }} per a completar l'inscripció.
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
			:disabled="insertaJugador.length==0 || jugadors.length>=maximjugadors"
            size="small"
            @click="addPlayer(insertaJugador)"
	        v-if="readonly === false"
        >Afegir jugador</ui-button> ({{ jugadors.length }} de {{ maximjugadors }})
		</div>

		<div class="insert">
			<ui-textbox
				id="jutge"
				required
	            error="Numero de soci del jutge requerit"
			    floating-label
	            autocomplete="off"
	            label="Jutge"
				type="number"
	            v-model="equip.jutge"
	            :disabled="readonly === true"
	            @keyup.native="searchJudge()"
	        ></ui-textbox>
	        
	        <span id="nomjutge" v-text="jutgenom"></span>
			
			<ui-textbox
				required
	            error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Delegat"
				type="text"
	            v-model="equip.delegat"
	            :disabled="readonly === true"
	        ></ui-textbox>
	        
	        <ui-textbox
	        	required
	            error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Telèfon delegat"
				type="text"
	            v-model="equip.telefon"
	            :disabled="readonly === true"
	        ></ui-textbox>
	        
	        <ui-textbox
	        	required
	            error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Lloc"
				type="text"
	            v-model="equip.lloc"
	            :disabled="readonly === true"
	        ></ui-textbox>
	        
	        <ui-textbox
	        	required
	            error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Dia de la setmana"
				type="text"
	            v-model="equip.diasem"
	            :disabled="readonly === true"
	        ></ui-textbox>
	        
	        <ui-textbox
	        	required
	            error="Camp de text requerit"
			    floating-label
	            autocomplete="off"
	            label="Hora"
				type="text"
	            v-model="equip.hora"
	            :disabled="readonly === true"
	        ></ui-textbox>
        </div>
		<div>
			
			<ui-button 
			size="small" 
            v-if="readonly === false"
			:disabled="equip.nom=='' || (jugadors && jugadors.length<minimjugadors) || equip.hora=='' || equip.diasem=='' || equip.lloc=='' || equip.telefon=='' || equip.jutge=='' || equip.delegat==''" 
			@click="doModSubscribe(subscribeId)">{{ $t('common.save') }}</ui-button>
			
		</div>
		

        
	</div>

</template>

<script>

export default {
  	components: {},
	data () {
		return {
			error:{},
		    inscripcions:[],
		    jugadorsclub:[],
		    jugadorsjainscrits:[],
		    subscribeId:'',
		    readonly:false,
		    equip:{},
		    jugadors:[],
		    buscadorJugador:[],
		    insertaJugador:'',
		    minimjugadors:0,
		    maximjugadors:99,
		    jutgenom:'',
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


		}
	},
	methods: {
		getData: function(id){
	        var vm = this;
	        
	        vm.$http.get('equip/'+id, { cache: false })
	        .then(function (response) {
	            vm.equip = response.data[0];
				vm.getJugadorsJaInscrits();
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	        vm.$http.get('/inscrits/'+id, { cache:false } )
	        .then(r=>vm.jugadors=r.data)
	        .catch(function (error) { console.log(error); });
	        
		},
		updateTeam: function() {
			var vm=this;
			vm.$http.post('/equip/'+vm.equip.id, {"nom":vm.equip.nom, "hora":vm.equip.hora, "diasem":vm.equip.diasem, "lloc":vm.equip.lloc, "telefon":vm.equip.telefon, "jutge":vm.equip.jutge, "delegat":vm.equip.delegat } )
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
						// ací donava un error de la api però la inscripció la acabava fent
						// ho canvie per a que recarregue la página igual que quan té éxit :
						//vm.closeAllModals();
						//window.location.reload();
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
		searchJudge: function() {
			var vm = this;
			vm.$http.get('/soci/'+vm.equip.jutge)
	        .then(function (response) {
				vm.jutgenom= response.data.nom;
	        })
		},
		getJugadorsJaInscrits:function(competicio) {
	        var vm = this;
	        vm.$http.get('/jugadorsinscrits/'+vm.equip.competicio, { cache: false })
	        .then(function (response) {
	        	vm.jugadorsjainscrits= response.data;
	        });
		},
		addPlayer: function(player){
			var vm = this;
			if (vm.jugadorsjainscrits.indexOf(player)>=0) {
			    	vm.error={};
			    	vm.error.response = {};
			    	vm.error.response.data = {};
			    	vm.error.response.data.error="Jugador ja inscrit en altre equip per a aquesta competició";
					vm.$refs.error.open();
			        return false;
			}
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
		},
		deletePlayer: function(player){
			var vm = this;
			for(var i = 0; i < vm.equip.length; i++) {
			    if(vm.equip[i].id == player) {
			        vm.equip.splice(i, 1);
			        break;
			    }
			}
		},
		
	},
	mounted: function () {
		var vm=this;
		vm.getData(vm.$route.params.equipId);
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
	}
}
</script>

<style lang="less">

@import "../../assets/less/defines.less";



</style>
      