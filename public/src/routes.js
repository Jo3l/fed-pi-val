import Vue from 'vue';
import VueHead from 'vue-head'
import Router from 'vue-router'

import Login from './components/admin/Login.vue'
import Start from './components/Start.vue'
import News from './components/News.vue'
import SingleNews from './components/SingleNews.vue'
import Calendar from './components/Calendar.vue'
import Cart from './components/Cart.vue'

import SubRoutes from './components/SubRoutes.vue'
import PageNotFound from './components/PageNotFound.vue'

import Jugadors from './components/admin/Jugadors.vue'
import Jugador from './components/admin/Jugador.vue'
import Clubs from './components/admin/Clubs.vue'
import Club from './components/admin/Club.vue'
import Equip from './components/admin/Equip.vue'

import defaultHead from './config/defaultHeader'

Vue.use(VueHead)
Vue.use(Router)

export default new Router({
	mode: 'history',	
	base: __dirname,
    routes: [
	  {
	    path: '/',
	    component: Start,
	    redirect: '/val/inici'
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
	    meta: Object.assign(defaultHead, {
	    	title: {
			  inner: 'Yeeeeessssss'
			},
	    }),
	  },
	  {
	    path: '/es/inicio',
	    name: 'Inicio',
	    component: Start,
	    lang:'es'
	  },
	  {
	    path: '/val/noticies',
	    name: 'Noticies',
	    component: News,
	    lang:'val',
	    redirect: '/val/noticies/0'
	  },
	  {
	    path: '/es/noticias',
	    name: 'Noticias',
	    component: News,
	    lang:'es',
	    redirect: '/val/noticies/0'
	  },
	  {
	    path: '/val/noticies/:page',
	    name: 'NoticiesPagina',
	    component: News,
	    meta: Object.assign(defaultHead, {
	    	title: {
			  inner: 'Yeeeeessssss'
			},
	    })
	  },
	  {
	    path: '/es/noticias/:page',
	    name: 'NoticiasPagina',
	    component: News,
	  },
	  {
	    path: '/val/noticia',
	    name: 'Nova Noticia',
	    component: SingleNews,
	  },
	  {
	    path: '/es/noticia',
	    name: 'Nueva Noticia',
	    component: SingleNews,
	  },
	  {
	    path: '/val/noticia/:slug',
	    name: 'Noticia',
	    component: SingleNews,
	  },
	  {
	    path: '/es/noticia/:slug',
	    name: 'Noticia-es',
	    component: SingleNews,
	  },
	  {
	    path: '/val/calendari',
	    name: 'Calendari',
	    component: Calendar,
	    lang:'val',
		meta: Object.assign(defaultHead, {
		    	title: {
				  inner: 'Cal cal'
				},
		    }),
		},
	    {
	    path: '/es/calendario',
	    name: 'Calendario',
	    component: Calendar,
	    lang:'es'
	  },
	  {
	    path: '/val/tenda',
	    name: 'Tenda',
	    component: Cart,
	    lang:'val'
	  },
	    {
	    path: '/es/tienda',
	    name: 'Tienda',
	    component: Cart,
	    lang:'es'
	  },
	  {
	    path: '/val/competicions',
	    props: {propDisable:'competicions'},
	    name: 'Competicions',
	    component: SubRoutes,
	    lang:'val'
	  },
	  {
	    path: '/es/competiciones',
	    props: {propDisable:'competicions'},
	    name: 'Competiciones',
	    component: SubRoutes,
	    lang:'es',
	  },
	  {
	    path: '/val/federacio',
	    props: {propDisable:'competicions'},
	    name: 'Federació',
	    component: SubRoutes,
	    lang:'val'
	  },
	  {
	    path: '/es/federacion',
	    props: {propDisable:'competicions'},
	    name: 'Federación',
	    component: SubRoutes,
	    lang:'es'
	  },
	  {
	    path: '/val/competicions/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Competicions node',
	    component: SubRoutes,
	  },
	  {
	    path: '/es/competiciones/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Competiciones nodo',
	    component: SubRoutes,
	  },
	  {
	    path: '/val/federacio/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Federació node',
	    component: SubRoutes,
	  },
	  {
	    path: '/es/federacion/:slug*',
	    props: {propDisable:'competicions'},
	    name: 'Federación nodo',
	    component: SubRoutes,
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
	    path: '/forbidden',
	    name: 'forbidden',
	    component: Start
	  },
	  {
	    path: '/login',
	    name: 'login',
	    component: Login,
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
	    path: '/404',
	    name: '404',
	    component: PageNotFound
	  },
	  { path: "*", redirect: '/404'}
	]
});
