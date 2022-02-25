<template>
	<div style="padding: 1em 2em;">
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
						</th>
					</tr>
				</tbody>
			</table>
		</section>
		

		<section class="add-local-visitant" v-if="$store.getters.isAuthenticatedWithRole(0)">
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
		<br/>

		<section class="flex50" v-if="$store.getters.isAuthenticatedWithRole(0)">
			<div>
				<label><strong>Sanció equip local:</strong></label>
	            <ui-select
	                help="Selecciona els punts a sancionar"
	                :options="['0','-1','-2','-3','-4']"
	                v-model="partida.sanciolocal"
	                type="basic" />
			</div>
			<div>
				<label><strong>Sanció equip visitant:</strong></label>
	            <ui-select
	                help="Selecciona els punts a sancionar"
	                :options="['0','-1','-2','-3','-4']"
	                v-model="partida.sanciovisitant"
	                type="basic" />
			</div>
		</section> 

		<br/>
		
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
        
        <section>
        	
        	<a :href="actaImg" target="_blank"><img style="width:48px;aspect-ratio:1;" :src="actaImg" /><br/>{{actaImg}}</a>
			<vue-core-image-upload
			    text="Pujar Imatge Acta"
			    class="uploader"
				compress="50"
			    url="/api/static/uploadacta"
				@imageuploaded="getPhotoActa"
			    :data="{do:'uploadacta'}"
			    extensions="jpg,jpeg"
			    inputAccept	="image/jpg,image/jpeg"
			>
			</vue-core-image-upload>
		</section>	
        <section>
        	
        	<a :href="equipsImg" target="_blank"><img style="width:48px;aspect-ratio:1;" :src="equipsImg" /><br/>{{equipsImg}}</a>
			<vue-core-image-upload
			    text="Pujar Imatge Equip"
			    class="uploader"
				compress="50"
			    url="/api/static/uploadimgequip"
				@imageuploaded="getPhotoEquips"
			    :data="{do:'uploadequip'}"
			    extensions="jpg,jpeg"
			    inputAccept	="image/jpg,image/jpeg"
			>
			</vue-core-image-upload>
		</section>	
		<br>
		<div class="buttonGroupRight">
			<ui-button size="small" @click="guardaPartida(partida)">{{ $t('common.save') }}</ui-button>
			<ui-button color="red" size="small"  @click="closeAllModals()">{{ $t('common.cancel') }}</ui-button>
		</div>
	</div>
</template>

<script>

import VueCoreImageUpload from 'vue-core-image-upload'

export default {
  	components: { 'vue-core-image-upload': VueCoreImageUpload},
  	props: ['partidaId'],
	data () {
		return {
			//partidaId: this.$route.params.partidaId,
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
			loading:false,
		    noResults:false,
			poblacions: [],
			mapa: null,
			actaImg: null,
			equipsImg: null
		}
	},
	methods: {
	    closeAllModals: function() {
	    	var vm=this;
			if(!vm.$route.params.partidaId) {
				vm.$eventHub.$emit('closeallmodals', {});
			} else {
				vm.$router.go(-1)
			}
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
		getPhotoActa:function(res) {
			var vm=this;
			vm.actaImg = '/static'+res.file;
		},
		getPhotoEquips:function(res) {
			var vm=this;
			vm.equipsImg = '/static'+res.file;
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
	        	console.log(response.data)
	        	res.date = vm.parseTime(res.data).toString('d/M/yyyy');
	        	res.imatge_local = res.imatge_local ? res.imatge_local : '/static/img/shield.png';
	        	res.imatge_visitant = res.imatge_visitant ? res.imatge_visitant : '/static/img/shield.png';
	        	res.resultatlocal = res.resultatlocal ? res.resultatlocal : 0;
	        	res.resultatvisitant = res.resultatvisitant ? res.resultatvisitant : 0;
	        	res.sanciolocal = res.sanciolocal ? res.sanciolocal : 0;
	        	res.sanciovisitant = res.sanciovisitant ? res.sanciovisitant : 0;
				vm.actaImg= res.actaimg;				
				vm.equipsImg= res.equipsimg;				
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
				"sanciolocal":vm.partida.sanciolocal,
				"sanciovisitant":vm.partida.sanciovisitant,
				"comentari":vm.partida.comentari,
				"nomDelegat":vm.partida.nomDelegat,
				"llicenciaDelegat":vm.partida.llicenciaDelegat,
				"contacteDelegat":vm.partida.contacteDelegat,
				"actaimg":vm.actaImg,
				"equipsimg":vm.equipsImg
			})
	        .then(function (response) {

				vm.$http.post('/participa/'+vm.partida.id,{"local":vm.equipLocal, "visitant":vm.equipVisitant})
		        .then(function (response) {

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
		if(vm.$store.getters.isAuthenticated) vm.getInfoPartida(vm.partidaId||vm.$route.params.partidaId);
		
	},
	created: function() {
	    if (!this.$store.getters.isAuthenticated) {
	      this.$router.push({ path: `/` });
	    }
	}
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
		    	max-width: 75%;
    			text-align: center;
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
