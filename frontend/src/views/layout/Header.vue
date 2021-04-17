<script lang="ts">
import { menuItems, MenuItem } from './data/menuItems'
import { computed, defineComponent } from 'vue'
import { useStore } from '@/store'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'Header',
  setup () {
    const store = useStore()
    const router = useRouter()

    const me = computed(() => store.state.security.me)
    const isAuth = computed(() => store.getters['security/getIsAuth'])
    const allowedMenuItems = computed(() => {
      return menuItems.filter((item: MenuItem) => {
        return me.value.roles.includes(item.role)
      })
    })

    function logout () {
      store.dispatch('security/logout')
      router.push({ path: '/login' })
    }

    return { allowedMenuItems, me, isAuth, logout }
  }
})
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

<style lang="scss">
  /* todo trouver pourquoi non trouver dans bootstrap ? */

  .justify-content-center {
    justify-content: center;
  }
</style>
