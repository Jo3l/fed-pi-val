import Vue from 'vue';
import VueHead from 'vue-head'
import Router from 'vue-router'

const Login = function(resolve) {require(['./components/admin/Login.vue'], resolve)}
const PageNotFound = function(resolve) {require(['./components/PageNotFound.vue'], resolve)}

const Start = function(resolve) {require(['./components/Start.vue'], resolve)}
const News = function(resolve) {require(['./components/News.vue'], resolve)}
const SingleNews = function(resolve) {require(['./components/SingleNews.vue'], resolve)}
const Calendar = function(resolve) {require(['./components/Calendar.vue'], resolve)}
const Cart = function(resolve) {require(['./components/Cart.vue'], resolve)}
const Product = function(resolve) {require(['./components/shop/Product.vue'], resolve)}

const SubRoutes = function(resolve) {require(['./components/SubRoutes.vue'], resolve)}

const Jugadors = function(resolve) {require(['./components/admin/Jugadors.vue'], resolve)}
const Jugador = function(resolve) {require(['./components/admin/Jugador.vue'], resolve)}
const Clubs = function(resolve) {require(['./components/admin/Clubs.vue'], resolve)}
const Club = function(resolve) {require(['./components/admin/Club.vue'], resolve)}
const Equip = function(resolve) {require(['./components/admin/Equip.vue'], resolve)}

const Productes = function(resolve) {require(['./components/admin/Products.vue'], resolve)}


//const Producte = function(resolve) {require(['./components/admin/Product.vue'], resolve)}

//import Login from './components/admin/Login.vue'
//import Start from './components/Start.vue'

//import News from './components/News.vue'
//import SingleNews from './components/SingleNews.vue'
//import Calendar from './components/Calendar.vue'

//import Cart from './components/Cart.vue'
//import Product from './components/Product.vue'

//import SubRoutes from './components/SubRoutes.vue'

//import PageNotFound from './components/PageNotFound.vue'

//import Jugadors from './components/admin/Jugadors.vue'
//import Jugador from './components/admin/Jugador.vue'
//import Clubs from './components/admin/Clubs.vue'
//import Club from './components/admin/Club.vue'
//import Equip from './components/admin/Equip.vue'



import Test from './components/Test.vue'
import productEditor from './components/admin/editor.vue'
import Trofeu from './components/Tournament.vue'

import defaultHead from './config/defaultHeader'

Vue.use(VueHead)
Vue.use(Router)

const routes = [
	  {
	    path: '/',
	    component: Start,
	    redirect: '/val/inici',
	  },
	  {
	    path: '/val',
	    component: Start,
	    redirect: '/val/inici',
	  },
	  {
	    path: '/es',
	    component: Start,
	    redirect: '/es/inicio',
	  },
	  {
	    path: '/val/inici',
	    name: 'Inici',
	    component: Start,
	    lang:'val',
	    meta: {...defaultHead,
	    	...{
		    	title: {
				  inner: 'aço es l´inici'
				},
		    }
	    }
	  },
	  {
	    path: '/es/inicio',
	    name: 'Inicio',
	    component: Start,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/noticies',
	    name: 'Noticies',
	    component: News,
	    lang:'val',
	    redirect: '/val/noticies/0', 
	    meta: defaultHead
	  },
	  {
	    path: '/es/noticias',
	    name: 'Noticias',
	    component: News,
	    lang:'es',
	    redirect: '/es/noticias/0', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/noticies/:page',
	    name: 'NoticiesPagina',
	    component: News,
	    meta: {...defaultHead,
		    ...{
			    	title: {
					  inner: 'Noticies'
					},
			    }
		    }
	  },
	  {
	    path: '/es/noticias/:page',
	    name: 'NoticiasPagina',
	    component: News, 
	    meta: defaultHead
	  },
	  {
	    path: '/val/noticia',
	    name: 'Nova Noticia',
	    component: SingleNews, 
	    meta: defaultHead
	  },
	  {
	    path: '/es/noticia',
	    name: 'Nueva Noticia',
	    component: SingleNews, 
	    meta: defaultHead
	  },
	  {
	    path: '/val/noticia/:slug',
	    name: 'Noticia',
	    component: SingleNews, 
	    meta: defaultHead
	  },
	  {
	    path: '/es/noticia/:slug',
	    name: 'Noticia-es',
	    component: SingleNews, 
	    meta: defaultHead
	  },
	  {
	    path: '/val/calendari',
	    name: 'Calendari',
	    component: Calendar,
	    lang:'val',
	    meta: {...defaultHead,
		    ...{
			    	title: {
					  inner: 'Calendari'
					},
			    }
		    }
		},
	    {
	    path: '/es/calendario',
	    name: 'Calendario',
	    component: Calendar,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/tenda',
	    name: 'Tenda',
	    component: Cart,
	    lang:'val', 
	    meta: defaultHead
	  },
	    {
	    path: '/es/tienda',
	    name: 'Tienda',
	    component: Cart,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/tenda/:slug*',
	    name: 'Producte',
	    component: Product, 
	    meta: defaultHead
	  },
	  {
	    path: '/es/tienda/:slug*',
	    name: 'Producto',
	    component: Product, 
	    meta: defaultHead
	  },
	  {
	    path: '/val/cistella',
	    name: 'Cistella',
	    component: Cart,
	    meta: defaultHead
	  },
	    {
	    path: '/es/carrito',
	    name: 'Carrito',
	    component: Cart,
	    meta: defaultHead
	  },
	  {
	    path: '/val/competicions',
	    props: {propDisable:'competicions'},
	    name: 'Competicions',
	    component: SubRoutes,
	    lang:'val', 
	    meta: defaultHead
	  },
	  {
	    path: '/es/competiciones',
	    props: {propDisable:'competicions'},
	    name: 'Competiciones',
	    component: SubRoutes,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/federacio',
	    props: {propDisable:'competicions'},
	    name: 'Federació',
	    component: SubRoutes,
	    lang:'val', 
	    meta: defaultHead
	  },
	  {
	    path: '/es/federacion',
	    props: {propDisable:'competicions'},
	    name: 'Federación',
	    component: SubRoutes,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/competicions/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Competicions node',
	    component: SubRoutes, 
	    meta: defaultHead
	  },
	  {
	    path: '/es/competiciones/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Competiciones nodo',
	    component: SubRoutes, 
	    meta: defaultHead
	  },
	  {
	    path: '/val/federacio/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Federació node',
	    component: SubRoutes, 
	    meta: defaultHead
	  },
	  {
	    path: '/es/federacion/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Federación nodo',
	    component: SubRoutes, 
	    meta: defaultHead
	  },
	  /*
	  {
	    path: '/val/*',
	    name: 'nodes',
	    component: SubRoutes
	  },
	  {
	    path: '/es/*',
	    name: 'nodos',
	    component: SubRoutes,
	  },
	  */
	  {
	    path: '/test',
	    name: 'test',
	    component: Test, 
	    meta: defaultHead
	  },
	  {
	    path: '/admin/editor',
	    name: 'editor',
	    component: productEditor, 
	    role: 0	    
	  },
	  {
	    path: '/trofeu',
	    name: 'trofeu',
	    component: Trofeu, 
	    meta: defaultHead
	  },
	  {
	    path: '/login',
	    name: 'login',
	    component: Login, 
	    meta: defaultHead
	  },
	    {
	    path: '/admin/jugadors',
	    name: 'Jugadors',
	    component: Jugadors,
	    role: 0
	  },
	    {
	    path: '/admin/jugador/:jugadorId',
	    name: 'Jugador',
	    component: Jugador,
	    role: 0
	  },
	    {
	    path: '/admin/club/:clubId',
	    name: 'Club',
	    component: Club,
	    role: 0
	  },
	    {
	    path: '/admin/jugador/',
	    name: 'Nou Jugador',
	    component: Jugador,
	    role: 0
	  },
	    {
	    path: '/admin/club/',
	    name: 'Nou Club',
	    component: Club,
	    role: 0
	  },
	    {
	    path: '/admin/club/:clubId/equip/:equipId',
	    name: 'Equip',
	    component: Equip,
	    role: 0
	  },
	  {
	    path: '/admin/club/:clubId/equip',
	    name: 'Nou Equip',
	    component: Equip,
	    role: 0
	  },
	    {
	    path: '/admin/clubs',
	    name: 'Clubs',
	    component: Clubs,
	    role: 0
	  },
	    {
	    path: '/admin/productes',
	    name: 'Productes',
	    component: Productes,
	    role: 0
	  },
	  {
	    path: '/404',
	    name: '404',
	    component: PageNotFound
	  },
	  { path: "*", redirect: '/404'}
	];

const router = new Router({
	mode: 'history',	
	base: __dirname,
    routes
});


/*
router.beforeEach( function(to, from, next) {
	  console.log('router', to.meta)
	  next()
})
*/

export default router;
