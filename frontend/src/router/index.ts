import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Home from '../views/Home.vue'
import AdminUsersPage from '../views/admin/Users.vue'
import PasswordReset from '../views/security/ForgottenPasswordReset.vue'
import AccountPage from '@/views/account/AccountPage.vue'
import LoginPage from '@/views/security/LoginPage.vue'

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
    path: '/login',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/account',
    name: 'Account',
    component: AccountPage
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
