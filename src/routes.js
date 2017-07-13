import Start from './components/Start.vue'
import News from './components/News.vue'
import Calendar from './components/Calendar.vue'

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
  },
  {
    path: '/es/inicio',
    name: 'Inicio',
    component: Start,
  },
  {
    path: '/val/noticia',
    name: 'Noticia_val',
    component: News,
  },
  {
    path: '/es/noticia',
    name: 'Noticia_es',
    component: News,
  },
  {
    path: '/val/calendari',
    name: 'calendari',
    component: Calendar,
  },
    {
    path: '/es/calendario',
    name: 'calendario',
    component: Calendar,
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
  }
];