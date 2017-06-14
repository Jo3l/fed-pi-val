import Start from './components/Start.vue'
import News from './components/News.vue'

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
        path: 'goal/:id',
        name: 'goal',
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