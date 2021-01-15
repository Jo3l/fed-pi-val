<template>
    <transition name="fade">
					<div class="shopping-cart" >
					    <div class="shopping-cart-header">
					      <ui-icon icon="shopping_cart"></ui-icon><span class="badge">{{countCart}}</span>
					      <div class="shopping-cart-total">
					        <span class="lighter-text">Total:</span>
					        <span class="main-color-text">{{cartTotalPrice}}€</span>
					      </div>
					    </div>
				    	<swiper class="shopping-cart-items" :options="swiperOptionThumbs">
				    		<div v-if="cart.length==0" class="swiper-slide empty">{{$t('cart.emptyCart')}}</div>
						    <swiper-slide v-for="item in cart" v-else>
						      	<router-link :to="{ path: '/'+$i18n.locale+'/'+$t('cart.shop')+'/'+item.fullProduct.content[$i18n.locale].slug }">
						        <picture :style="'background-image:url('+ getProductImage(item) +');'"><span>{{getProductType(item)}}</span></picture>
						        <span class="item-name">{{item.fullProduct.content[$i18n.locale].name}}</span>
						        <span class="item-price">{{getProductPrice(item)}}€</span>
						        <span class="item-quantity">{{$t('cart.quantity')}}: <input class="item-quantity" type="number" v-model="item.quantity"> </span>
						        </router-link>
						        <div class="modifier">
						        	<ui-icon-button color="fedpival" icon="add" size="small" type="secondary" @click="$store.dispatch('increaseProductToCart', item)"></ui-icon-button>
						        	<ui-icon-button color="fedpival" icon="remove" size="small" type="secondary" @click="$store.dispatch('removeProductToCart', item)"></ui-icon-button>
						        	<ui-icon-button color="fedpival" icon="clear" size="small" type="secondary" @click="$store.dispatch('deleteProductToCart', item)"></ui-icon-button>
						        </div>
						    </swiper-slide>
						    
					    	<ui-icon-button icon="expand_less" type="primary" class="swiper-button-prev cart" slot="button-prev" v-if="cart.length>0"></ui-icon-button>
							<ui-icon-button icon="expand_more" type="primary" class="swiper-button-next cart" slot="button-next" v-if="cart.length>0"></ui-icon-button>
						</swiper>

						<ui-button icon="shopping_cart" :class="cart.length>0 && cartTotalPrice>=10?'checkout':'checkout disabled'" color="fedpival" :disabled="cart.length<=0 || cartTotalPrice<10" @click="openModal('buyModal')">{{$i18n.t('cart.buy')}} <span v-if="cartTotalPrice<10" style="white-space:nowrap">...minim 10€!</span></ui-button>

				        <ui-modal size="large" ref="buyModal" :title="$i18n.t('cart.customerData')">
				        	
				        	<div class="done" v-if="resultDone">
				        		<p>{{resultDone}}</p>
				        		<ui-button @click="eraseCart()" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
				        	</div>

							<h3>{{$i18n.t('cart.info')}}:</h3>
							<p style="white-space: pre-wrap;">{{$i18n.t('cart.shippingInfo')}} </p>
							<details ref="mesinfo"><summary><ui-button color="fedpival" @click="$refs['mesinfo'].open=!$refs['mesinfo'].open;">Més informació</ui-button></summary><div style="margin:0 1em; padding:1em; box-shadow:0 0 5px 2px black;">

<p ><b><span
lang=ES >INFORMACIÓN BÁSICA SOBRE
PROTECCIÓN DE DATOS</span></b></a></p>

<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 
 >
 <tr>
  <td  colspan=2 valign=top >
  <p align=center ><b><span lang=ES>INFORMACIÓN BÁSICA
  SOBRE PROTECCIÓN DE DATOS</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top ><b><span lang=ES  ><br clear=all
  >
  </span></b>
  <p ><b><span lang=ES>Responsable</span></b></p>
  </td>
  <td  valign=top >
  <p ><span lang=ES>FEDERACION DE PILOTA VALENCIANA</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES>Finalidad</span></b></p>
  </td>
  <td  valign=top >
  <p class=MsoNormal><span lang=ES >Tratamos datos de
  carácter personal con la finalidad de: responder a las solicitudes
  formuladas, gestionar el alta como usuario en la página web, enviar la
  información que nos sea solicitada, así como cualquier prospección comercial
  que pueda ser del interés para el usuario. Por último, tratamos sus datos para
  llevar a cabo la gestión del proceso de compra que el interesado ha realizado
  a través de la cesta de compra de la web.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES>Legitimación</span></b></p>
  </td>
  <td  valign=top >
  <p ><span lang=ES >La base legal para el tratamiento
  de sus datos es el consentimiento del interesado en un formulario digital</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES>Destinatarios</span></b></p>
  </td>
  <td  valign=top >
  <p ><span lang=ES >Los datos se comunicarán a otras
  entidades privadas o públicas, para fines administrativos y para la ejecución
  de su solicitud. Podrán además ser comunicados a la administración y/o tribunales
  para cumplir con la legislación vigente.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES>Derechos</span></b></p>
  </td>
  <td  valign=top >
  <p ><span lang=ES>Tiene derecho a acceder, rectificar y suprimir los
  datos, así</span></p>
  <p ><span lang=ES>como otros derechos, como se explica en la información
  adicional.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES>Información adicional</span></b></p>
  </td>
  <td  valign=top >
  <p ><span lang=ES>Puede consultar la información adicional y detallada</span></p>
  <p ><span lang=ES>sobre Protección de Datos en<b><span >:  <i>enlace a INFORMACION ADICIONAL</i></span></b></span></p>
  </td>
 </tr>
</table>

<p ><span lang=ES >&nbsp;</span></p>
<p ><b><span lang=ES >&nbsp;</span></b></p>

<p ><b><span lang=ES >INFORMACIÓN ADICIONAL SOBRE PROTECCIÓN DE DATOS</span></b></p>

<p ><span lang=ES
>&nbsp;</span></p>

<table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 
 >
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Quién es el responsable del
  tratamiento de sus datos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Identidad:</span></b><span
  lang=ES > </span><span lang=ES>FEDERACION DE PILOTA
  VALENCIANA</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >CIF:</span></b><span lang=ES
  > </span><span lang=ES>G-46374351</span></p>
  </td>
 </tr>
 <tr >
  <td  valign=top >
  <p class=MsoNormal><b><span lang=ES >Dirección postal:</span></b><span
  lang=ES >&nbsp; </span><span lang=ES>C/ Marqués de San
  Juan, 32 bajo B - 46015 - Valencia </span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Teléfono:</span></b><span
  lang=ES >&nbsp; </span><span lang=ES>963 74 95 58</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p class=MsoNormal><b><span lang=ES >Correo electrónico:</span></b><span
  lang=ES >&nbsp; </span><a href="mailto:info@fedpival.es"><span
  lang=ES>info@fedpival.es</span></a><span lang=ES> </span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Con qué finalidad tratamos sus
  datos personales?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p class=MsoNormal><span lang=ES >En </span><span lang=ES>FEDERACION
  DE PILOTA VALENCIANA</span><span lang=ES > </span><span lang=ES >tratamos datos de
  carácter personal con la finalidad de: responder a las solicitudes
  formuladas, gestionar el alta como usuario en la página web, enviar la
  información que nos sea solicitada, así como cualquier prospección comercial
  que pueda ser del interés para el usuario. Por último, tratamos sus datos
  para llevar a cabo la gestión del proceso de compra que el interesado ha
  realizado a través de la cesta de compra de la web.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Cómo dejar de recibir
  comunicaciones comerciales</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p class=MsoNormal><span lang=ES >De conformidad con lo
  establecido en la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de
  la Información y del Comercio Electrónico, en el caso de que el usuario desee
  dejar de recibir comunicaciones informativas o promocionales por parte de </span><span
  lang=ES>FEDERACION DE PILOTA VALENCIANA</span><span lang=ES >, puede solicitar la baja del servicio enviando un correo electrónico
  a la siguiente dirección: </span><a href="mailto:info@fedpival.es"><span
  lang=ES>info@fedpival.es</span></a></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Por cuánto tiempo conservaremos
  sus datos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p class=MsoNormal><span lang=ES >Los datos personales
  proporcionados se conservarán mientras se mantenga la relación </span><span
  lang=ES>administrativa y no se solicite su supresión por el interesado. Una
  vez concluida la relación administrativa <span >se
  conservarán en estado de bloqueo durante el plazo legal establecido en
  cumplimiento de las obligaciones legales y poder hacer frente a las posibles
  responsabilidades o requerimientos de las Administraciones Públicas y/o
  Tribunales.  Durante la suscripción al envío de nuestras comunicaciones,
  hasta el momento que solicita el usuario la baja del servicio.</span></span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Cuál es la legitimación para el
  tratamiento de sus datos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p class=MsoNormal><span lang=ES >La base legal para el
  tratamiento de sus datos es el consentimiento del interesado en un formulario
  digital.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >&nbsp;</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿A qué destinatarios se
  comunicarán sus datos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >Los datos se comunicarán a otras
  entidades privadas o públicas, para fines administrativos y para la ejecución
  de su solicitud. Podrán además ser comunicados a la administración y/o
  tribunales para cumplir con la legislación vigente.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES > </span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Cuáles son sus derechos cuando
  nos facilita sus datos? </span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >Cualquier persona tiene derecho a
  obtener confirmación sobre si en </span><span lang=ES>FEDERACION DE PILOTA
  VALENCIANA</span><span lang=ES > estamos tratando datos
  personales que les conciernan, o no. Las personas interesadas tienen derecho
  a acceder a sus datos personales, así como a solicitar la rectificación de
  los datos inexactos o, en su caso, solicitar su supresión cuando, entre otros
  motivos, los datos ya no sean necesarios para los fines que fueron recogidos.
  Tiene derecho a solicitar tutela de la Agencia Española de Protección de
  Datos. En determinadas circunstancias y por motivos relacionados con su
  situación particular, los interesados podrán oponerse al tratamiento de sus
  datos. FEDERACION DE PILOTA VALENCIANA dejará de tratar los datos, salvo por
  motivos legítimos imperiosos, o el ejercicio o la defensa de posibles
  reclamaciones.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >&nbsp;</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Cómo ejercer sus derechos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >Pueden ejercer sus derechos
  remitiendo escrito, adjuntando copia de documento oficial que le identifique
  y concretando el derecho o derechos que desea ejercer, de cualquiera de los
  medios siguientes:</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Correo electrónico:</span></b><span
  lang=ES > </span><a href="mailto:info@fedpival.es"><span
  lang=ES>info@fedpival.es</span></a><span lang=ES> </span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >&nbsp;</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >¿Cómo hemos obtenido sus datos?</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >Los datos personales que tratamos
  en </span><span lang=ES>FEDERACION DE PILOTA VALENCIANA</span><span lang=ES
  > </span><span lang=ES >proceden
  del interesado.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >&nbsp;</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Obtenemos el consentimiento
  cuando:</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >El interesado crear una cuenta de
  usuario en nuestra web, marca la casilla destinada a la suscripción a
  nuestras comunicaciones o cumplimenta un formulario en formato electrónico.</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >&nbsp;</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Las categorías de datos que se
  tratan son:</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Datos identificativos:</span></b><span
  lang=ES > nombre y apellidos, DNI</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Datos de contacto:</span></b><span
  lang=ES > teléfono, dirección postal, Correo electrónico</span></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><b><span lang=ES >Datos de características
  personales:</span></b></p>
  </td>
 </tr>
 <tr>
  <td  valign=top >
  <p ><span lang=ES >No se tratan datos especialmente
  protegidos.</span></p>
  </td>
 </tr>
</table>
</div>
							
							</details>
							
							<br>
							<br>
							
							<a href="mailto:botiga@fedpival.es">botiga@fedpival.es</a></p>
						
							<br>
							
							<ui-radio-group
				                name="payment"
				                :options="shipping"
				                v-model="order.payment"
				            >{{$i18n.t('cart.payType')}}</ui-radio-group>
				            
							<br>
						    
							<div class="clientData">
								<div class="form">
									
						            <ui-textbox
						                floating-label
						                label="Nom"
						                placeholder="Pose el seu nom"
						                v-model="order.name"
						                :invalid="order.name.length<4"
						            ></ui-textbox>
		
						            <ui-textbox
						                floating-label
						                label="Cognoms"
						                placeholder="Pose els seus cognoms"
						                v-model="order.surname"
						                :invalid="order.surname.length<4"
						            ></ui-textbox>
		
						            <ui-textbox
						                floating-label
						                label="Adreça completa"
						                placeholder="Pose l'adreça completa on es va a enviar"
						                v-model="order.address"
						                :invalid="order.address.length<4"
						            ></ui-textbox>
		
						            <ui-textbox
						                floating-label
						                label="Codi postal"
						                placeholder="Pose el seu codi postal"
						                type="number"
						                v-model="order.cp"
						                :invalid="isNaN(order.cp)||order.cp.toString().length!=5"
						            ></ui-textbox>
						            
						            <ui-textbox
						                floating-label
						                label="Població"
						                placeholder="Pose la població on es va a enviar"
						                v-model="order.city"
						                :invalid="order.city.length<3"
						            ></ui-textbox>
		
						            <ui-textbox
						            	floating-label
						                icon-position="right"
						                icon="phone"
						                label="Telèfon"
						                type="number"
						                placeholder="Pose el seu nº de telèfon"
						                v-model="order.tel"
						                :invalid="isNaN(order.tel)||order.tel.toString().length<9"
						            ></ui-textbox>
						            
						            <ui-textbox
						            	floating-label
						                help=""
						                icon-position="right"
						                icon="mail"
						                label="Email"
						                placeholder="Pose la seua adreça de correu electrònic"
						                type="email"
						                v-model="order.email"
						                :invalid="$store.getters.validate({string:order.email,type:'email'})"
						            ></ui-textbox>
						            
						            <ui-textbox
						                enforce-maxlength
						                help="Maxim 256 caracters"
						                label="Comentari"
						                placeholder="Si vols afegir cap comentari, escriu ací"
						                :multi-line="true"
						                :maxlength="256"
						                v-model="order.comentari"
						            ></ui-textbox>
						            
								</div>
								<div class="list">
									
							    	<div class="shopping-cart-items final">
									    <div v-for="item in cart" class="swiper-slide">
									        <span class="item-name">{{item.fullProduct.content[$i18n.locale].name}} [{{getProductType(item)}}]</span>
									        <span class="item-price">{{getProductPrice(item)}}€</span>
									        <span class="item-quantity">{{$t('cart.quantity')}}: {{item.quantity}}</span>
									    </div>

									</div>
									<span v-if="!isBlackFriday || (isBlackFriday && cartTotalPrice < 30)" class="finalPrice">+Despeses d'enviament 8,90€</span><br/><br/>
									<span class="finalPrice"><strong>Total {{ (parseFloat(cartTotalPrice)+( (!isBlackFriday || (isBlackFriday && cartTotalPrice < 30) )?8.9:0)).toFixed(2) }}€</strong></span>
								</div>
				            </div>
				            
							<div slot="footer" v-if="!resultDone">
				                <ui-button @click="buy()" :loading="buyButtonDisable" :disabled="buyButtonDisable" color="fedpival">{{$i18n.t('modal.ok')}}</ui-button>
				                <ui-button @click="closeModal('buyModal')">{{$i18n.t('modal.cancel')}}</ui-button>
				            </div>
				        </ui-modal>

    <form ref="tpvform" method="POST">
          <input ref="version" type="hidden" name="Ds_SignatureVersion" />
          <input ref="parameters" type="hidden" name="Ds_MerchantParameters" />
          <input ref="signature" type="hidden" name="Ds_Signature" />
    </form>

					 </div>
    </transition>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

import 'swiper/dist/css/swiper.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
  components: { swiper, swiperSlide },
  data () {
    return {
    	resultDone:'',
    	isBlackFriday: false,
    	buyButtonDisable: false,
    	shipping : [
		    {
		        label: this.$t('cart.cashOnDelivery'),
		        value: 'cash-on-delivery'
		    },
		    {
		        label: this.$t('cart.bankTransfer'),
		        value: 'bank-transfer'
		    },
		    {
		        label: this.$t('cart.onlinePay'),
		        value: 'online-pay'
		    }
		],
    	order:{
    		name:'',
    		surname:'',
    		address:'',
    		cp:'',
    		city:'',
    		tel:'',
    		email:'',
    		comentari:'',
    		payment:'online-pay'
    	},
        swiperOptionThumbs: {
          direction: 'vertical',
          spaceBetween: 10,
          slidesPerView: 3,
          touchRatio: 0.2,
          navigation: {
            nextEl: '.swiper-button-next.cart',
            prevEl: '.swiper-button-prev.cart'
          }
        },
    }
  },

  computed: {
    ...mapGetters({
    	countCart: 'countCart',
    	cart: 'cart',
    	checkoutStatus: 'checkoutStatus',
    	cartTotalPrice :'cartTotalPrice',
    	validate:'validate'
    })
  },
  methods: {
    openModal: function(ref) {
    	var vm = this;
		vm.order.cart = vm.cart;
        vm.$refs[ref].open();
    },
    closeModal: function(ref) {
    	var vm = this;
        vm.$refs[ref].close();
    },
    eraseCart: function(ref) {
    	var vm = this;
    	vm.resultDone = '';
    	vm.order={name:'', surname:'', address:'', cp:'', city:'', tel:'', email:''};
		vm.$store.dispatch('deleteCart');
        vm.closeModal('buyModal');
    },
    buy: function(cart){
		
		if(document.querySelectorAll('.is-invalid:not(.is-disabled)').length>0) {
			document.querySelector('.is-invalid:not(.is-disabled) input').focus()
			this.resultDone = '';
			return false;
		}

		var vm = this;
		vm.order.cart = vm.cart;
		vm.buyButtonDisable = true;
		vm.$http.post('/comprar', vm.order)
		.then(function (response) {
    			if(vm.order.payment==='online-pay') {
    				vm.$refs.version.value= response.data.params.Ds_SignatureVersion;
					vm.$refs.parameters.value= response.data.params.Ds_MerchantParameters;
					vm.$refs.signature.value= response.data.params.Ds_Signature;
					vm.$refs.tpvform.action= response.data.url;
					vm.buyButtonDisable = false;
    				vm.$refs.tpvform.submit()
    			} else {
    				vm.buyButtonDisable = false;
    				window.location.href = '/'+vm.$i18n.locale+'/'+vm.$i18n.t('cart.shop')+'/'+vm.$i18n.t('cart.buyed')+'/'+vm.order.payment;
    			}

				/*setTimeout(function(){
					vm.resultDone = vm.$i18n.t('cart.success');
				}, 500);*/
        })
        .catch(function (error) {
        		vm.resultDone = vm.$i18n.t('cart.fail');
            	console.log(error);
        });
	        

    },
  	getType:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].name==item.name) {
		  	var type = item.fullProduct.types[i];
		  	type.fullProduct = JSON.parse( JSON.stringify( item ) );
		  	return type;
		  }
		}
		return false;
  	},
	getProductImage:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].name==item.name) {
		  	for (var j = 0, len = item.fullProduct.images.length; j < len; j++) {
		  		if(item.fullProduct.images[j].tag == item.fullProduct.types[i].imgTag){
		  			return item.fullProduct.images[j].img;
		  		}
		  	}
		  }
		}
		return false;
	},
	getProductPrice:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].name==item.name && item.fullProduct.types[i].price) {
		  	
		  	return item.fullProduct.types[i].price.amount;
		  	
		  }
		}
		return false;
	},
	getProductType:function(item){
  		for (var i = 0, len = item.fullProduct.types.length; i < len; i++) {
		  if(item.fullProduct.types[i].name==item.name) {
		  
		  	return item.fullProduct.types[i].name;
		  	
		  }
		}
		return false;
	}
  },
  watch: {

  },
  mounted: function() {
  }
}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.item-quantity {
	width:2em;
}

input[type=number].item-quantity::-webkit-inner-spin-button, 
input[type=number].item-quantity::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}

.done {
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(255,255,255,1);
    left: 0;
    top: 0;
    z-index: 99;
    padding: 23%;
}

.clientData{
	display:flex;
	.form{width:50%;}
	.list{width:45%;margin-left:5%;border-left: 1px solid #e0e0e0;}
}
.finalPrice{
	text-align: right;
    width: 100%;
    display: block;
    padding: 30px;
}
.shopping-cart {
    margin: 20px;
    position: absolute;
    background: white;
    width: 320px;
    border-radius: 3px;
    padding: 20px 0 0 0;
    z-index: 99;
    box-shadow: 0 10px 45px rgba(0, 0, 0, 0.2);
    right: 22px;
    top: 66px;
    max-height: 400px;
    @media(max-width:@screenMobile) {
	    margin: 0px;
	    position: absolute;
	    width: 100%;
	    box-shadow: 0 10px 45px rgba(0, 0, 0, 0.2);
	    top: 90px;
	    left: 0px;
	    max-height: 400px;
	}

	.checkout{
	    width: 100%;
	    border-radius: 0px 0px 3px 3px;
		span{
			display: block;
		    padding: 15px;
		    font-size: 125%;
		    text-transform: uppercase;
		}
	    .ui-button__icon span {
	    	display:contents;
	    }
	}
    .swiper-button-next.cart{
		bottom: 10px;
    	top: auto;
    	right: 0px!important;
    	left:initial!important;
    }
    .swiper-button-prev.cart{
    	top:30px;
    	bottom:auto;
    	right: 0px;
    	left:initial!important;
    }
    
	.shopping-cart-header {
		border-bottom: 1px solid #E8E8E8;
		padding: 0 20px 15px 20px;
		.shopping-cart-total {
		  float: right;
		}
	}
  
	.shopping-cart-items {
	    padding-top: 20px;
	    padding-left: 0;
		list-style: none;
	    //overflow: auto;
	    max-height: 280px;
	    //min-height: 275px;
	    position: relative;
		border-bottom: 1px solid #E8E8E8;
		&.final{
			border-bottom:none;
			overflow:auto;
			max-height: 360px;
		}
		

	    
	    .swiper-slide {
	    	padding:0 30px 0 20px;
	    	min-height: 74px;
	    	&.empty{
	    		 min-height:12px;
	    	}
	    	&:hover{
	    		picture{
	    			box-shadow: 0px 3px 0px #6d111d;
	    		}
	    	}
	    	picture{
				width: 70px;
			    height: 70px;
			    background-size: cover;
			    background-position: center;
			    display: inline-block;
			    float: left;
			    margin-right: 10px;
			    border-radius: 5px;
			    border: 1px dashed #6d111d;
			    box-sizing: content-box;
			    position:relative;
			    span{
			    	color: white;
				    text-shadow: 1px 1px 1px black;
				    font-weight: bolder;
				    position:absolute;
				    bottom:0;
				    right:3px;
				    word-break: break-all;
				    overflow:hidden;
			    }
	    	}
	    }
	    img {
	      float: left;
	      margin-right: 12px;
	    }
	    .item-name {
			padding-top: 3px;
		    font-size: 16px;
		    white-space: nowrap;
		    overflow: hidden;
		    display: block;
		    text-overflow: ellipsis;
	    }
	    .item-price {
	      color: @fedcolor;
	      margin-right: 8px;
	    }
	    .item-quantity {
	      color: @fedcolor;
	      text-transform:capitalize;
	      border:0;
	    }
	}

    &:after {
		bottom: 100%;
		left: 89%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
		border-bottom-color: white;
		border-width: 8px;
		margin-left: -8px;
	}
	.badge {
	    background-color: @fedcolor;
	    border-radius: 10px;
	    color: white;
	    display: inline-block;
	    font-size: 12px;
	    line-height: 1;
	    padding: 4px 6px 2px 6px;
	    text-align: center;
	    vertical-align: middle;
	    white-space: nowrap;
	}
}

</style>