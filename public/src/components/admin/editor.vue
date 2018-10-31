<template>

		<div class="editor">
			
		<h1>Editor de productes</h1>
		<!-- select id,
json_extract(json,'$.content.name.*') as nom,json_extract(json,'$.categoria') as cat from producte -->

		<div v-for="prod in productes" class="ui-button ui-button--type-secondary ui-button--color-red ui-button--icon-position-left ui-button--size-small">  
		{{prod.id}} 
			<!--ui-icon-button icon="delete" size="small" type="secondary" @click="borraEquip(equip,key)"></ui-icon-button-->
		</div>

		<!--ui-button color="fedpival" icon="done_all" @click="enviar">editar</ui-button-->

		</div>

</template>

<style lang="less">
</style>

<script>
import draggable from 'vuedraggable'

export default {
  	components: { draggable },
	name: 'productEditor',
	data () {
		return {
			productes: [{id:1,destacado:false,json:''},{id:2,destacado:false,json:''}],
			inici: new Date(),
		}
	},
	created: function(query) {
		console.log(123);
    	var vm = this;
		vm.$http.get('/producte')
		.then( (resp) => {
			console.log(window.vm=vm)
			resp.data.forEach( (p) => vm.$data.productes.push(p) );
		});
		return;
        if (query.length < 3) return;
    	var vm = this;
        vm.loading=true;
        vm.$http.get('/equip/search/'+query, { cache: false })
        .then(function (response) {
        })
        .catch(function (error) {
            console.log(error);
        });
    },
	methods: {
        enviar: function() {
        	var vm= this;
        	//vm.$http.post('/equip/genera', JSON.stringify(vm.jornades) );
        }
    },
	watch: {
		esTrofeu: function(n) { document.querySelector('#trofeu .ui-switch__label-text').innerHTML= n?'Trofeu':'Lliga'; }
	},
	mounted: function () {
	}
}

</script>