<template>
  <div class="home">
    <h2>Auth</h2>
    <p id="11">Auth loading: {{ authLoading ? 'oui' : 'non' }}</p>
    <p id="22">Is auth: {{ isAuth ? 'oui' : 'non' }}</p>
    <p class="red">{{ authError || '' }}</p>
    <h2>Users</h2>
    <p>User loading: {{ userLoading ? 'oui' : 'non' }}</p>
    <p>User length: {{ users.length }}</p>
    <p class="red">{{ userError || '' }}</p>
    <button id="logg" @click="logg">Login</button>
    <button @click="unlogg">Logout</button>
    <button @click="findUsers">Get users</button>
    <button @click="refresh">Refresh</button>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import HelloWorld from '@/components/HelloWorld.vue'
import { mapActions, mapState } from 'vuex'

@Options({
  components: {
    HelloWorld
  },
  computed: {
    ...mapState('security', {
      isAuth: 'isAuthenticated',
      authLoading: 'isLoading',
      authError: 'errorMsg'
    }),
    ...mapState('users', {
      userLoading: 'isLoading',
      userError: 'errorMsg'
    }),
    ...mapState('users', ['users'])
  },
  methods: {
    logg () {
      this.$store.dispatch('security/login')
    },
    unlogg () {
      this.$store.dispatch('security/logout')
    },
    findUsers () {
      this.$store.dispatch('users/all')
    },
    refresh () {
      this.$store.dispatch('security/refreshLogin')
    }
  },
  mounted () {
    // this.$store.dispatch('users/getUsers')
  }
})
export default class Home extends Vue {}
</script>

<style lang="scss" scoped>
.red {
  color: #ff0000;
}
</style>
