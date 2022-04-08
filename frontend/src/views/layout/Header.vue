<script lang="ts" setup>
import { menuItems, MenuItem } from './data/menuItems'
import { computed } from 'vue'
import { useSecurityStore } from '@/stores/security'
import { useRouter } from 'vue-router'

const securityStore = useSecurityStore()
const router = useRouter()

const isAuth = computed(() => securityStore.getIsAuth)
const roles = computed(() => securityStore.getRoles)
const allowedMenuItems = computed(() => {
  return menuItems.filter((item: MenuItem) => {
    return roles.value.includes(item.role)
  })
})

function logout () {
  securityStore.logout()
  router.push({ path: '/login' })
}
</script>

<template>
  <ul class="nav justify-content-center">
    <li
      class="nav-item"
      v-for="(item, idx) in allowedMenuItems"
      :key="idx"
    >
      <router-link
        :to="item.to"
        class="nav-link"
        :class="{active: false}"
      >
        {{ item.label }}
      </router-link>
    </li>
    <li
      class="nav-item"
      v-if="isAuth"
    >
      <a
        class="nav-link"
        href="/logout"
        @click.prevent="logout"
      >Logout</a>
    </li>
  </ul>
</template>
