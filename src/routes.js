import Start from './components/Start.vue'
import News from './components/News.vue'
import SingleNews from './components/SingleNews.vue'
import Calendar from './components/Calendar.vue'
import Cart from './components/Cart.vue'
import Clubs from './components/Clubs.vue'
import Tournament from './components/Tournament.vue'
import Form from './components/Form.vue'
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
    path: '/val/noticia',
    name: 'Noticies',
    component: News,
    lang:'val'
  },
  {
    path: '/es/noticia',
    name: 'Noticias',
    component: News,
    lang:'es'
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
    path: '/val/clubs',
    name: 'Clubs',
    component: Clubs,
    lang:'val'
  },
  {
    path: '/es/clubs',
    name: 'Clubs-es',
    component: Clubs,
    lang:'es'
  },
  {
    path: '/val/torneig',
    name: 'Torneig',
    component: Tournament,
    lang:'val'
  },
  {
    path: '/es/torneo',
    name: 'Torneo',
    component: Tournament,
    lang:'es'
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
    path: '/val/form',
    name: 'ProvaFormulari',
    component: Form,
    lang:'val'
  },
  {
    path: '/404',
    name: '404',
    component: PageNotFound
  },
  { path: "*", redirect: '/404'}
];