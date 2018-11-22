<template>
    <transition name="fade">
	    <div>

			<div style="position: absolute;right: 20px;margin-top: -15px;"><ui-fab color="normal" icon="keyboard_backspace" @click="goBack()" size="small"></ui-fab></div>

			<h1 style="margin-bottom:0;">{{club.nom}}</h1>
			
            <ui-tabs type="text">
                <ui-tab :title="$i18n.t('common.clubData')">
                    
					<div class="contentFlex formulari">
						<div class="left50">
					        	<p><strong>{{$i18n.t('common.name')}}:</strong> {{club.nom}}</p>
					        	<p><strong>{{$i18n.t('common.president')}}:</strong> {{club.president}}</p>
					        	<p><strong>{{$i18n.t('common.secretary')}}:</strong> {{club.secretari}}</p>
					        	<p><strong>Email:</strong> <a :href="'mailto:'+club.email">{{club.email}}</a></p>
					        	<p><strong>{{$i18n.t('common.funDate')}}:</strong> {{club.fundacio}}</p>
					        	<p><strong>{{$i18n.t('common.place')}}:</strong> {{club.poblacio}}</p>
					        </div>
					        <div class="left50">
					        
							<img v-if="club.imatge==null" class="jugador" src="/static/img/shield.png" style="opacity: 0.15;">
							<progressive-img v-else class="jugador" :src="club.imatge" fallback="/static/img/shield.png" style="opacity: 0.15;"/>
							
						</div>
						
		
					</div>
                    
                </ui-tab>

                <ui-tab :title="$i18n.t('common.teams')">
					<div class="vuetableContainer">
						<tablerone :tableList="list" :tableColumns="columns">
							<th slot="headActions"></th>
							<template slot="actions" scope="props">
								<td class="actions">

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

            </ui-tabs>
			
	    </div>
    </transition>
</template>

<script>

import Table from './custom/Table.vue';
import Paginate from 'vuejs-paginate'

export default {
  	components: { 'tablerone':Table, 'paginate': Paginate},
	data () {
		return {
		    list:{},
		    columns:[
	            {
	                label: this.$i18n.t('common.name'),
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
				  secretari:null
		    }
		}
	},
	methods: {
		goBack:function(){
    		window.history.back();
		},
		clickCallback: function(pageNum) {
	    	console.log(pageNum);
	    	var vm=this;
	    	vm.getEquips('equipsdeclub', pageNum);
	    },
		getPhoto:function(res) {
			var vm=this;
			vm.club.imatge = '/static'+res.file;
		},
		getData: function(){
	        var vm = this;
	        
	        vm.$http.get('/club/'+vm.$route.params.clubId, { cache: false })
	        .then(function (response) {
	            vm.club = response.data[0];
	            vm.club.fundacio = response.data[0].fundacio;
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
		}
		
	},
	created: function() {

	}
}
</script>

<style lang="less">



</style>