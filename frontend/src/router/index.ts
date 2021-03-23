import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Home from '../views/Home.vue'
import AdminUsersPage from '../views/admin/Users.vue'
import PasswordReset from '../views/security/PasswordReset.vue'

// oui
const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsersPage
  },
  {
    path: '/password/reset/:token',
    name: 'About',
    component: PasswordReset
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
