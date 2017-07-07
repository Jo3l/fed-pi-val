import Start from './components/Start.vue'
import News from './components/News.vue'
import Calendar from './components/Calendar.vue'

export const routes = [
  {
    path: '/',
    component: Start,
    redirect: '/inici',
    children: [
      {
        path: 'inici',
        name: 'inici',
        component: Start,
      },
      {
        path: 'goal/:id/notifications',
        name: 'goal-notifications',
        component: Start,
      },
      {
        path: 'goal/:id/edit',
        name: 'goal-edit',
        component: Start,
      },
    ],
  },
  {
    path: '/noticia',
    name: 'Noticia',
    component: News,
    children: [
      {
        path: ':id',
        name: 'noticia-id',
        component: News,
      },
      {
        path: ':id/edit',
        name: 'news-edit',
        component: News,
      }
    ]
  },
  {
    path: '/calendari',
    name: 'calendari',
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
  },
];