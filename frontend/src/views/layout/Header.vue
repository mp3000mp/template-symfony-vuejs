<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { menuItems, MenuItem } from './data/menuItems'
import { mapGetters, mapState } from 'vuex'

@Options({
  name: 'Header',
  data () {
    return {
      menuItems
    }
  },
  computed: {
    allowedMenuItems () {
      return this.menuItems.filter((item: MenuItem) => {
        return this.me.roles.includes(item.role)
      })
    },
    ...mapState('security', ['me']),
    ...mapGetters('security', {
      isAuth: 'getIsAuth'
    })
  },
  methods: {
    logout () {
      this.$store.dispatch('security/logout')
      this.$router.push({ path: '/login' })
    }
  }
})
export default class LayoutHeader extends Vue {}
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
