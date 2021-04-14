import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Home from '@/views/Home.vue'
import AdminUsersPage from '@/views/admin/UsersPage.vue'
import PasswordReset from '@/views/security/ForgottenPasswordReset.vue'
import AccountPage from '@/views/account/AccountPage.vue'
import LoginPage from '@/views/security/LoginPage.vue'
import { state as securityState } from '@/store/modules/security/state'

function checkPermission (role: string) {
  return securityState.me.roles.includes(role)
}

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_USER')) {
        next()
      } else {
        next({ name: 'Login' })
      }
    }
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsersPage,
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_ADMIN')) {
        next()
      } else {
        next({ name: 'Home' })
      }
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_ANONYMOUS')) {
        next()
      } else {
        next({ name: 'Home' })
      }
    }
  },
  {
    path: '/account',
    name: 'Account',
    component: AccountPage,
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_USER')) {
        next()
      } else {
        next({ name: 'Login' })
      }
    }
  },
  {
    path: '/password/forgotten/:token',
    name: 'ForgottenPassword',
    component: PasswordReset,
    props: {
      init: false
    },
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_ANONYMOUS')) {
        next()
      } else {
        next({ name: 'Home' })
      }
    }
  },
  {
    path: '/password/init/:token',
    name: 'InitPassword',
    component: PasswordReset,
    props: {
      init: true
    },
    beforeEnter: (to, from, next) => {
      if (checkPermission('ROLE_ANONYMOUS')) {
        next()
      } else {
        next({ name: 'Home' })
      }
    }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
