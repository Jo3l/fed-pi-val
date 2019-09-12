<template>
    <transition name="fade">
		<div class="success">
			<br>
			<h2>Gràcies per comprar a la tenda de la Fedració!</h2>
			<br>
			<dl>
			  <dt>Nº comanda: {{datos.comanda}}</dt>
			  <dt>Nom: {{datos.name}}</dt>
			  <dt>Adreça: {{datos.address}}</dt>
			  <dt>Codi Postal: {{datos.cp}}</dt>
			  <dt>Telèfon: {{datos.tel}}</dt>
			</dl>
			
			<br>
			
			<router-link :to="{ path: '/'+$i18n.locale+'/'+$i18n.t('cart.shop') }"><ui-button raised size="normal">Continuar comprant</ui-button></router-link>
			
			<!--<pre>{{datos}}</pre>-->

		</div>

    </transition>
</template>

<script>


export default {
  name: 'Success',
  components: { },
  data () {
    return {
		datos:{
				  "name": "test alfons",
				  "address": "adreca",
				  "cp": "12345",
				  "tel": "834783738",
				  "email": "al@vlc.wiki",
				  "cart": [
				    {
				      "name": "5 m",
				      "fullProduct": {
				        "types": [
				          {
				            "name": "5 m",
				            "price": {
				              "amount": 6.2,
				              "oldPrice": 6.2
				            }
				          },
				          {
				            "name": "10 m",
				            "price": {
				              "amount": 10.7,
				              "oldPrice": 10.7
				            }
				          }
				        ],
				        "images": [
				          {
				            "img": "/static/productes/2019/06/tesamoll.jpg",
				            "thumb": "/static/productes/2019/06/tesamoll.jpg"
				          }
				        ],
				        "content": {
				          "es": {
				            "name": "Tesa-Dur",
				            "slug": "tesa-dur",
				            "details": "Tesa-Dur",
				            "category": "Protecciones para las Manos",
				            "description": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros.",
				            "shippingOptions": [
				              "Envio",
				              "Recogida en nuestra sede"
				            ],
				            "shortDescription": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros."
				          },
				          "val": {
				            "name": "Tesa-Dur",
				            "slug": "tesa-dur",
				            "details": "Tesa-Dur",
				            "category": "Proteccions per a les Mans",
				            "description": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres.",
				            "shippingOptions": [
				              "Enviament",
				              "Recollida a la seu"
				            ],
				            "shortDescription": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres."
				          }
				        },
				        "shipping": {
				          "amount": [
				            "8.9",
				            0
				          ]
				        }
				      },
				      "quantity": 1
				    },
				    {
				      "name": "10 m",
				      "fullProduct": {
				        "types": [
				          {
				            "name": "5 m",
				            "price": {
				              "amount": 6.2,
				              "oldPrice": 6.2
				            },
				            "fullProduct": {
				              "types": [
				                {
				                  "name": "5 m",
				                  "price": {
				                    "amount": 6.2,
				                    "oldPrice": 6.2
				                  }
				                },
				                {
				                  "name": "10 m",
				                  "price": {
				                    "amount": 10.7,
				                    "oldPrice": 10.7
				                  }
				                }
				              ],
				              "images": [
				                {
				                  "img": "/static/productes/2019/06/tesamoll.jpg",
				                  "thumb": "/static/productes/2019/06/tesamoll.jpg"
				                }
				              ],
				              "content": {
				                "es": {
				                  "name": "Tesa-Dur",
				                  "slug": "tesa-dur",
				                  "details": "Tesa-Dur",
				                  "category": "Protecciones para las Manos",
				                  "description": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros.",
				                  "shippingOptions": [
				                    "Envio",
				                    "Recogida en nuestra sede"
				                  ],
				                  "shortDescription": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros."
				                },
				                "val": {
				                  "name": "Tesa-Dur",
				                  "slug": "tesa-dur",
				                  "details": "Tesa-Dur",
				                  "category": "Proteccions per a les Mans",
				                  "description": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres.",
				                  "shippingOptions": [
				                    "Enviament",
				                    "Recollida a la seu"
				                  ],
				                  "shortDescription": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres."
				                }
				              },
				              "shipping": {
				                "amount": [
				                  "8.9",
				                  0
				                ]
				              }
				            }
				          },
				          {
				            "name": "10 m",
				            "price": {
				              "amount": 10.7,
				              "oldPrice": 10.7
				            }
				          }
				        ],
				        "images": [
				          {
				            "img": "/static/productes/2019/06/tesamoll.jpg",
				            "thumb": "/static/productes/2019/06/tesamoll.jpg"
				          }
				        ],
				        "content": {
				          "es": {
				            "name": "Tesa-Dur",
				            "slug": "tesa-dur",
				            "details": "Tesa-Dur",
				            "category": "Protecciones para las Manos",
				            "description": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros.",
				            "shippingOptions": [
				              "Envio",
				              "Recogida en nuestra sede"
				            ],
				            "shortDescription": "Espuma especial para proteger las manos con una protecciu00f3n dura, mayor al tesa-moll y el tesa-blau. Formato de 5 y 10 metros."
				          },
				          "val": {
				            "name": "Tesa-Dur",
				            "slug": "tesa-dur",
				            "details": "Tesa-Dur",
				            "category": "Proteccions per a les Mans",
				            "description": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres.",
				            "shippingOptions": [
				              "Enviament",
				              "Recollida a la seu"
				            ],
				            "shortDescription": "Escuma especial per a protegir les mans amb una protecciu00f3 dura,  major al tesa-moll i el tesa-blau. Format de 5 i 10 metres."
				          }
				        },
				        "shipping": {
				          "amount": [
				            "8.9",
				            0
				          ]
				        }
				      },
				      "quantity": 1
				    }
				  ],
				  "comanda": "19000005",
				  "authcode": "151175",
				  "preu": "25.8"
				}
    }
  },
  methods: {

  },
  mounted: function() {
	  	var vm=this;
	  	
	  	
		vm.$http.post('/pagat', vm.$route.query)
        .then(function (response) {
        	
        	//vm.datos=response.data;
            
        })
        .catch(function (error) {
            console.log(error);
        });
        
  }

}
</script>

<style lang="less" scoped>

@import "../../assets/less/defines.less";

.success {
	padding: 1em 2em;
}
</style>