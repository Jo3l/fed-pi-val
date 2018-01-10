import Start from './components/Start.vue'
import News from './components/News.vue'
import SingleNews from './components/SingleNews.vue'
import Calendar from './components/Calendar.vue'
import Cart from './components/Cart.vue'
import Clubs from './components/Clubs.vue'
import Tournament from './components/Tournament.vue'
import Form from './components/Form.vue'
import SubRoutes from './components/SubRoutes.vue'
import PageNotFound from './components/PageNotFound.vue'

export const routes = [
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
    lang:'val'
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
  },
  {
    path: '/es/noticias/:page',
    name: 'NoticiasPagina',
    component: News,
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
/*
  {
    path: '/val/noticia/edit/:slug',
    name: 'NoticiaEdit',
    component: function(resolve) {
             require(['./components/EditSingleNews.vue'], resolve);
        },
  },
  {
    path: '/es/noticia/edit/:slug',
    name: 'NoticiaEdit-es',
    component: function(resolve) {
             require(['./components/EditSingleNews.vue'], resolve);
        },
  },
*/
  {
    path: '/val/calendari',
    name: 'Calendari',
    component: Calendar,
    lang:'val'
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
    name: 'Competicions',
    props: {node:'competicions'},
    component: SubRoutes,
    lang:'val'
  },
  {
    path: '/es/competiciones',
    name: 'Competiciones',
    props: {node:'competiciones'},
    component: SubRoutes,
    lang:'es'
  },  {
    path: '/val/federacio',
    name: 'Federacio',
    component: SubRoutes,
    lang:'val'
  },
  {
    path: '/es/federacion',
    name: 'Federación',
    component: SubRoutes,
    lang:'es'
  },
  {
    path: '/val/*',
    name: 'nodes',
    component: SubRoutes,
  },
  {
    path: '/es/*',
    name: 'nodos',
    component: SubRoutes,
  },
  {
    path: '/forbidden',
    name: 'forbidden',
    component: Start
  },
  {
    path: '/login',
    name: 'login',
    component: Start,
  },
  {
    path: '/404',
    name: '404',
    component: PageNotFound
  },
  { path: "*", redirect: '/404'}
];