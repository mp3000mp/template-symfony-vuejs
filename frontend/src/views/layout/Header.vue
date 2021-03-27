<template>
  <ul class="nav justify-content-center">
    <li
      class="nav-item"
      v-for="(item, idx) in menuItems"
      :key="idx"
    >
      <router-link
        v-if="me.roles.includes(item.role)"
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

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { menuItems } from './data/menuItems'
import { mapGetters, mapState } from 'vuex'

@Options({
  name: 'Header',
  data () {
    return {
      menuItems
    }
  },
  computed: {
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

<style lang="scss">
  /* todo trouver pourquoi non trouver dans bootstrap ? */

  .justify-content-center {
    justify-content: center;
  }
</style>
