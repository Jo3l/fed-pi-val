<template>
    <transition name="fade">
		<div class="admin-shop" v-if="loaded">
			<div class="product">
				
				<h1>Producte: {{product.content[$i18n.locale].name}}</h1>
				
				<ui-switch v-model="destacada">
		                Destacat: <code>{{ destacada ? 'Si' : 'No' }}</code>
		        </ui-switch>
		            
				<fieldset v-for="lang in $i18n.messages">
					<h2>{{lang.common.label}}</h2>
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            :label="'Nom '+lang.common.short"
						type="text"
			            v-model="product.content[lang.common.short].name"
			        ></ui-textbox>
	
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Categoria"
						type="text"
			            v-model="product.content[lang.common.short].category"
			        ></ui-textbox>
	
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Descripció"
						type="text"
			            v-model="product.content[lang.common.short].description"
			        ></ui-textbox>
		
					<ui-textbox
					    floating-label
			            autocomplete="off"
			            error="This field is required"
			            label="Descripció curta"
						type="text"
			            v-model="product.content[lang.common.short].shortDescription"
			        ></ui-textbox>
			        
			        <fieldset>
			        	<h3>Opcions d'enviament:</h3>
			        	<template v-for="(option, key) in product.content[lang.common.short].shippingOptions">
			        	<div class="flexed">
				        	<ui-icon-button @click="deleteItem(product.content[lang.common.short].shippingOptions, key)" icon="delete" size="small"></ui-icon-button>
							<ui-textbox
							    floating-label
					            autocomplete="off"
					            error="This field is required"
					            label="opció"
								type="text"
					            v-model="product.content[lang.common.short].shippingOptions[key]"
					        ></ui-textbox>
				        </div>
				        </template>
				        <div class="flexed" @click="push(product.content[lang.common.short].shippingOptions,'')"><ui-icon-button icon="add" size="small"></ui-icon-button><span>Afegir opció</span></div>
			        </fieldset>
	
				</fieldset>
	
		        <fieldset>
		        	<h2>Preu d'enviament:</h2><h4>Els preus corresponen en ordre als anteriorment introduïts</h4>
		        	<template v-for="(type, key) in product.shipping.amount">
		        	<div class="flexed">
			        	<ui-icon-button @click="deleteItem(product.shipping.amount, key)" icon="delete" size="small"></ui-icon-button>
						<ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Preu d'enviament"
							type="number"
				            v-model="product.shipping.amount[key]"
				        ></ui-textbox>
			        </div>
			        </template>
			        <div class="flexed" @click="push(product.shipping.amount, 1 )"><ui-icon-button icon="add" size="small"></ui-icon-button><span>Afegir opció</span></div>
		        </fieldset>
	
		        <fieldset>
		        	<h2>Tipus:</h2>
		        	<template v-for="(type, key) in product.types">
		        	<div class="flexed">
			        	<ui-icon-button @click="deleteItem(product.types, key)" icon="delete" size="small"></ui-icon-button>
						<ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Nom del tipus"
							type="text"
				            v-model="product.types[key].name"
				        ></ui-textbox>
				        <ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Preu en euros"
							type="number"
				            v-model="product.types[key].price.amount"
				        ></ui-textbox>
				        <ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Preu antic en euros"
							type="number"
				            v-model="product.types[key].price.oldPrice"
				        ></ui-textbox>
			        </div>
			        </template>
			        <div class="flexed" @click="push(product.types, {'name':'','price':{'amount':1,'oldPrice':1}} )"><ui-icon-button icon="add" size="small"></ui-icon-button><span>Afegir opció</span></div>
		        </fieldset>
		            
		        <fieldset>
		        	<h2>Imatges:</h2>
		        	<template v-for="(type, key) in product.images">
		        	<div class="flexed">
			        	<ui-icon-button @click="deleteItem(product.images, key)" icon="delete" size="small"></ui-icon-button>
				        
				        <ui-textbox
						    floating-label
				            autocomplete="off"
				            error="This field is required"
				            label="Url imatge"
							type="text"
				            v-model="product.images[key].img"
				        ></ui-textbox>
				        
						<picture><img v-if="product.images[key].img && product.images[key].img.length>5" :src="product.images[key].img"></picture>

					    <vue-core-image-upload
						    :text="$i18n.t('image.uploadAndCut')"
						    class="uploader"
							crop="local"
							cropRatio="1:1"
							compress=50
							:key=key
							:maxWidth=600
							:maxHeight=600
						    url="/api/static/uploadimgproducte"
							@imageuploaded="getPhoto"
						    :data="{do:'uploadimgproducte','key':key}"
						    extensions="jpg,jpeg"
						    inputAccept	="image/jpg,image/jpeg"
						>
						</vue-core-image-upload>
					
			        </div>
			        </template>
			        <div class="flexed" @click="push(product.images, {img:'',thumb:''} )"><ui-icon-button icon="add" size="small"></ui-icon-button><span>Afegir Imatge</span></div>
		        </fieldset>
		        
		    	<ui-button color="saveForm" icon="save" size="small" type="secondary" @click="saveForm(product)">Desar</ui-button>

			</div>


   
		</div>

    </transition>
</template>

<script>
import VueCoreImageUpload from 'vue-core-image-upload'

export default {
  name: 'Product',
  components: { 'vue-core-image-upload': VueCoreImageUpload },
  data () {
    return {
    	loaded: false,
    	destacada:false,
    	id:null,
        product: '',
        newProduct:{
				types    : [
						{
								name  : "",
								price : {
										amount   : 1,
										oldPrice : 1,
								}
						}
				],
				images   : [
				 {
								img   : "",
								thumb : "",
						}
				],
				content  : {
						es  : {
								name             : "",
								category		 : "",
								slug             : "",
								details          : "",
								description      : "",
								shortDescription : "",
								shippingOptions  : [
								 " "
								]
						},
						val : {
								name             : "",
								category		 : "",
								slug             : "",
								details          : "",
								description      :  "",
								shortDescription : "",
								shippingOptions  : [
								 ""
								]
						},
				},
				shipping : {
						amount : [
						 1
						],
				}
		},
        
    }
  },
  methods: {
  	getPhoto:function(res, data) {
  		var vm=this;
		var key = parseInt(data.key); //filed.split('-')[1];
		vm.product.images[key].img = '/static'+res.file;
		vm.product.images[key].thumb = '/static'+res.file;
	},
  	saveForm:function(json){
  		var vm=this;
  		var form={};
  		
  		form.id=vm.id||'';
  		form.categoria=vm.categoria;
    	form.destacada=vm.destacada;
    	form.json = JSON.stringify(json);
  		
  		vm.$http.post('/producte/'+form.id, form)
	        .then(function (response) {
	        	console.log(form);
	            vm.$router.push({ path: `/admin/productes/`});
	            
	        })
	        .catch(function (error) {
	            console.log(error);
	        });
  	},
  	push:function(data, push){
  		data.push(push);
  	},
  	deleteItem:function(array, index) {
  		array.splice(index, 1);
  	},
  	getData: function(apiUrl) {
        var vm = this;
        this.$http.get(apiUrl,{cache:false})
        .then(function (response) {
        	vm.id = response.data[0].id;
        	vm.destacada = (response.data[0].destacada == 'true'||response.data[0].destacada == 1);
        	vm.product = response.data[0].json;
            vm.loaded=true;
        })
        .catch(function (error) {
            console.log(error);
        });
        
    },
    updateData:function(data){
    	console.log(data);
    }
  },
  watch: {
  	
  },
  mounted: function() {
	  		var vm=this;
			if(vm.$route.params.slug) {
				vm.getData('producte/slug/'+vm.$route.params.slug);
			} else {
				vm.product = vm.newProduct;
            	vm.loaded=true;
			}
	
  },
  created: function() {
	    if (!this.$store.getters.isAuthenticatedWithRole(0)) {
	      this.$router.push({ path: `/` });
	    }
  },
  beforeRouteUpdate (to, from, next) {
		this.getData('producte/slug/'+to.params.slug);
		next();
  }
	
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";



.product{
	padding: 1em 2em;
	fieldset {
		margin:10px 0;
	    padding: 15px;
	    border: 1px solid @fedcolor;
	    .flexed{
	    	display:flex;
	    	border-bottom:1px dashed @fedcolor;
	    	margin-bottom:15px;
	    	&:last-of-type{
	    		border:none;
	    	}
	    	& > .ui-textbox{width:29%; margin-right:20px;margin-top: 2%;}
	    	button{
	    		margin:10px;
	    	}
	    	span{
	    		margin-top:16px;
	    		cursor:pointer;
	    	}
	    	picture{
	    		img{width:150px;height:auto;margin-bottom:20px;}
	    	}
	    	.uploader{
	    		margin-left:20px;
	    	}
	    }
	}
}


</style>