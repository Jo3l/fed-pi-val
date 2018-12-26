import Vue from 'vue';
import VueHead from 'vue-head'
import Router from 'vue-router'

const Login = function(resolve) {require(['./components/admin/Login.vue'], resolve)}
const PageNotFound = function(resolve) {require(['./components/PageNotFound.vue'], resolve)}

const Start = function(resolve) {require(['./components/Start.vue'], resolve)}
const News = function(resolve) {require(['./components/News.vue'], resolve)}
const SingleNews = function(resolve) {require(['./components/SingleNews.vue'], resolve)}
const Calendar = function(resolve) {require(['./components/Calendar.vue'], resolve)}
const Products = function(resolve) {require(['./components/shop/Products.vue'], resolve)}
const Product = function(resolve) {require(['./components/shop/Product.vue'], resolve)}
const clubsPublic = function(resolve) {require(['./components/ClubsPublic.vue'], resolve)}
const clubPublic = function(resolve) {require(['./components/ClubPublic.vue'], resolve)}

//cms
const SubRoutes = function(resolve) {require(['./components/SubRoutes.vue'], resolve)}

//admin
const Jugadors = function(resolve) {require(['./components/admin/Jugadors.vue'], resolve)}
const Jugador = function(resolve) {require(['./components/admin/Jugador.vue'], resolve)}
const Clubs = function(resolve) {require(['./components/admin/Clubs.vue'], resolve)}
const Club = function(resolve) {require(['./components/admin/Club.vue'], resolve)}
const Equip = function(resolve) {require(['./components/admin/Equip.vue'], resolve)}
const adminProductes = function(resolve) {require(['./components/admin/Products.vue'], resolve)}
const adminProducte = function(resolve) {require(['./components/admin/Product.vue'], resolve)}

//gestio
const GClub = function(resolve) {require(['./components/gestio/Club.vue'], resolve)}

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
				  inner: 'Pàgina oficial de la Federació de Pilota Valenciana'
				},
		    }
	    }
	  },
	  {
	    path: '/es/inicio',
	    name: 'Inicio',
	    component: Start,
	    lang:'es', 
	    meta: {...defaultHead,
	    	...{
		    	title: {
				  inner: 'Página oficial de la Federación de Pelota Valenciana'
				},
		    }
	    }
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
	    path: '/val/botiga',
	    name: 'Botiga',
	    component: Products,
	    lang:'val', 
	    meta: defaultHead
	  },
	    {
	    path: '/es/tienda',
	    name: 'Tienda',
	    component: Products,
	    lang:'es', 
	    meta: defaultHead
	  },
	  {
	    path: '/val/botiga/:slug*',
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
	    path: '/val/federacio/clubs-de-pilota-valenciana',
	    component: clubsPublic,
	    meta: defaultHead
	  },
	  {
	    path: '/es/federacion/clubs-de-pilota-valenciana',
	    component: clubsPublic,
	    meta: defaultHead
	  },
	  {
	    path: '/val/federacio/clubs-de-pilota-valenciana/:clubId',
	    component: clubPublic,
	    meta: defaultHead
	  },
	  {
	    path: '/es/federacion/clubs-de-pilota-valenciana/:clubId',
	    component: clubPublic,
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
	    component: adminProductes,
	    role: 0
	  },
	  {
	    path: '/admin/producte/',
	    name: 'Nou Producte',
	    component: adminProducte,
	    role: 0
	  },
	  {
	    path: '/admin/producte/:slug*',
	    name: 'Editar Producte',
	    component: adminProducte,
	    role: 0
	  },
	  {
	    path: '/gestio/club',
	    name: 'Editar Producte',
	    component: GClub,
	    role: 10
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
	pathToRegexOptions: { strict: true },
	base: __dirname,
    routes
});


/*
router.beforeEach( function(to, from, next) {
	  console.log('router', to.meta)
	  next()
})
*/

ga('set', 'page', router.currentRoute.path);
ga('send', 'pageview');

router.afterEach(function(to, from) {
  ga('set', 'page', to.path);
  ga('send', 'pageview');
});


export default router;
