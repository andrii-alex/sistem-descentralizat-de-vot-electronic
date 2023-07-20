import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'client-welcome',
    component: () => import("@/pages/client/Welcome.vue")
  },
  {
    path: '/admin',
    name: 'admin-login',
    component: () => import("@/pages/admin/Login.vue")
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import("@/pages/admin/Dashboard.vue")
  },
  {
    path: '/form',
    name: 'form',
    component: () => import("@/pages/client/validation/FormPage.vue")
  },
  {
    path: '/registered-form',
    name: 'registered-form',
    component: () => import("@/pages/client/validation/RegisteredPage.vue")
  },
  {
    path: '/evuid/:userToken',
    name: 'evuid-generator',
    component: () => import("@/pages/client/evuid/EvuidGenerator.vue")
  },
  {
    path: '/vote',
    name: 'vote',
    component: () => import("@/pages/client/voting/Voting.vue")
  },
  {
    // Catch-all route
    path: '*',
    name: 'not-found',
    component: () => import("@/pages/NotFound.vue")
  }
]

const router = new VueRouter({
  routes
})

export default router
