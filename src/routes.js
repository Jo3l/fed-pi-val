import Start from './components/Start.vue'
import News from './components/News.vue'
import Calendar from './components/Calendar.vue'
import Cart from './components/Cart.vue'
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