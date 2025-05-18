import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      // Ruta de inicio de la aplicación
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      // Ruta para la vista de Podcast, carga diferida para optimización
      path: '/podcast',
      name: 'podcast',
      component: () => import('../views/PodcastView.vue'),
    },
    {
      // Ruta para la vista de Eventos, se carga de forma perezosa
      path: '/eventos',
      name: 'eventos',
      component: () => import('../views/EventosView.vue'),
    },
    {
      // Ruta para la vista de Servicios, carga diferida
      path: '/servicios',
      name: 'servicios',
      component: () => import('../views/ServiciosView.vue'),
    },
  ],
})

export default router
