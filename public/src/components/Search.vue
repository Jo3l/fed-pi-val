<template>
    <transition name="fade">
	    <div>
			<div class="search-container">
				<ui-textbox :placeholder="$i18n.t('common.search')" v-model="searchText" @keydown-enter="search"></ui-textbox> <ui-icon-button @click="search" icon="search" size="normal"></ui-icon-button>
			</div>
			<div v-if="rotating" class="lds-ring"><div></div><div></div><div></div><div></div></div>
			<div class="result-container" v-if="result.length>0">
				<ul>
					<li v-for="(item, index) in result" v-if="item.nom">
						<i class="ui-icon material-icons" :title="descs[item.tipus]">{{icons[item.tipus]}}</i>
						<router-link :to="{ path: item.url }">{{item.nom}}</router-link>
					</li>
				</ul>
			</div>
			<div class="result-container" v-else>
				<span>{{$i18n.t('common.noresults')}}</span>
			</div>
	    </div>
    </transition>
</template>

<script>


export default {

  components: {},
  data () {
    return {
		searchText:null,
		rotating:false,
		result:' ',
		icons: {
			"_noticia_val":"format_quote",
			"_noticia_es":"format_quote",
			"_element_val":"menu_open",
			"_element_es":"menu_open",
			"club":"security",
			"_camins":"bookmark_border",
			"_jerarquia":"bookmark",
			"producte":"shopping_cart",
			"_partida":"sports"
		},
		descs: {
			"_noticia_val":"noticia",
			"_noticia_es":"noticia",
			"_element_val":"bloc de pàgina",
			"_element_es":"bloque de página",
			"club":"club",
			"_camins":"pàgina",
			"_jerarquia":"pàgina",
			"producte":"producte",
			"_partida":"partida"
		}
    }
  },
  head : function() {
	return this.$route.meta;
  },
  methods: {
  	isMobileDevice: function() {
	    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
	},
	search: function() {
        var vm = this;
        if(vm.searchText.length>3) {
	        vm.rotating=true;
	        vm.$http.get('/globalsearch/'+vm.searchText+'/i/'+ vm.$i18n.locale)
	        .then(function (response) {
	        	vm.result = response.data;
	        	vm.rotating=false;
	        })
	        .catch(function (error) {
	            console.log(error);
	        });	
        }
	},
  },
  created : function() {

  },
  mounted: function () {
	
  },
  watch: {

  }
}
</script>

<style lang="less">

@import "../assets/less/defines.less";

.search-container {
	display:flex;
	justify-content:center;
	width:100%;
	padding:0 15em;
	margin: 5em auto 5em auto;
	&>*{
		flex: 0 0 auto;
	}
	@media(max-width:@screenDesktop){
		padding:0 5em;
	}
	@media(max-width:@screenTablet){
		padding:0 2em;
		width: 80%;
	}
	.ui-textbox {
		width:100%;
		margin-right:2em;
	}
}
.ui-icon { color: @fedcolor; }
.result-container {
	padding: 0 14em;
	@media(max-width:@screenTablet){
		padding: 0 2em;
	}
	ul {
		list-style-type: none;
		padding:0;
		li {
			padding: 5px 0;
		    font-size: 110%;
		    border-bottom: 1px dashed #ccc;
		}
	}
}
.lds-ring {
	display: block;
    width: 100%;
    margin-left: ~"calc(50% - 7em)";
    @media(max-width:@screenTablet){
		display:none;
	}
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 10em;
  height: 10em;
  margin: 2em;
  border: 2em solid @fedcolor;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: @fedcolor transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}



</style>
