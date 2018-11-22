<template>
    <transition name="fade">
    <div id="news">
    	
    	<h1>{{ $t('common.newss') }} <ui-button v-if="$store.getters.isAuthenticatedWithRole(0)" color="blueButtonToRight" icon="save" size="small" type="secondary" @click="$router.push({ path:'/'+$i18n.locale+'/noticia'})">{{ $t('common.newsNew') }}</ui-button></h1>
    	
    	<nav class="noflex">

    		<div class="opener" @click="viewTags=!viewTags">
    			
    			<span v-if="busca!='all'">{{$i18n.t('common.filterWith')}} <strong>{{busca}}</strong>. {{$i18n.t('common.clickFilter')}}.</span>
    			<span v-else>{{$i18n.t('common.clickCloud')}}</span>
	    		<span :class="'ui-icon '+(viewTags?'':'collapse')">
	    			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
	    				<path d="M7.406 7.828L12 12.422l4.594-4.594L18 9.234l-6 6-6-6z"></path>
	    			</svg>
	    		</span>
    		</div>
	    	<ui-collapsible header="Tags" :open="viewTags">
		    	<div class="filterButtons">
		    		<ui-button type="flat" color="primary" :raised="'all'==busca?true:false" @click="buscaStr('all')">{{$i18n.t('common.all')}}</ui-button>
		    		<ui-button type="flat" color="primary" :raised="tag==busca?true:false" v-for="tag in tags" @click="buscaStr(tag)">{{tag}}</ui-button>
				</div> 
	        </ui-collapsible>
    	</nav>

		
    	<nav>
			<ui-icon-button @click="decPage" icon="chevron_left" type="primary"></ui-icon-button>
			<strong class="textPositioned">{{ $t('common.page') }}: {{ pagina }}</strong>
			<ui-icon-button @click="incPage" icon="chevron_right" type="primary"></ui-icon-button>
		</nav>
		
		<NewsCarousel :pagina=pagina :busca=busca></NewsCarousel>
		
    	<nav>
			<ui-icon-button @click="decPage" icon="chevron_left" type="primary"></ui-icon-button>
			<strong class="textPositioned">{{ $t('common.page') }}: {{ pagina }}</strong>
			<ui-icon-button @click="incPage" icon="chevron_right" type="primary"></ui-icon-button>
		</nav>
		
    </div>
    </transition>
</template>

<script>

import NewsCarousel from './NewsCarousel.vue';

export default {
	name: 'News',
  	components: {'NewsCarousel' : NewsCarousel},
	data () {
		return {
		    pagina: 0,
		    busca:'all',
		    viewTags:false,
		    tags:[],
		    any:'',
		    mes:''
		}
	},
	head : function() {
		return this.$route.meta;
	},
	methods: {
		loadState:function() {
		  try {
		    var serializedState = localStorage.getItem('fedpival_filtre');
		    if (serializedState === null) {
		      return { busca : 'all' }
		    }
		    return JSON.parse(serializedState);
		  } catch (err) {
		    return undefined;
		  }
		},
		saveState: function(state) {
		  try {
		    var serializedState = JSON.stringify(state);
		    localStorage.setItem('fedpival_filtre', serializedState);
		  } catch (err) {
		    console.error('Error: ' + err);
		  }
		},
		buscaStr:function(busca){
			var vm=this;
			vm.busca = busca;
			vm.viewTags = false;
			vm.saveState( {'busca' : vm.busca } );
		},
	  	getTags: function() {
	
	        var vm = this;
	        vm.$http.get('/tags/noticia')
	        .then(function (response) {
	        	vm.tags = response.data.tags;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
	        
	    },
		incPage: function() {
			this.pagina++;
			var cami= location.pathname.split('/');
			this.$router.push({ path: `/${cami[1]}/${cami[2]}/${this.pagina}` });
		},
		decPage: function() {
			if(this.pagina >= 1) this.pagina--;
			var cami= location.pathname.split('/');
			this.$router.push({ path: `/${cami[1]}/${cami[2]}/${this.pagina}` });
		},
	},
	beforeMount: function () {
		if(this.$route.params.page>0) this.pagina = this.$route.params.page;
		this.getTags();
		this.busca=this.loadState().busca;
	},
	beforeRouteUpdate (to, from, next) {
		next();
	}
}
</script>

<style lang="less">


#news{

	nav {
	    padding: 20px 60px;
	    display: flex;
	    &.noflex{display:block;}
	    justify-content: space-between;
	    .opener{
	    	strong{text-transform:capitalize;}
	    	display:block;
	    	cursor:pointer;
	    	.ui-icon{
	    		vertical-align: middle;
	    		&.collapse {
	    			svg{transform:rotate(180deg);}
	    		}
	    	}
	    }
	    .ui-collapsible__header {display:none!important;}
	    .ui-collapsible__body {border:none;}
	}
	
	.filterButtons{ text-align: center; }
}

</style>
