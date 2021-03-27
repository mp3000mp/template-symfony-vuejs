<template>
  <div class="home">
    <h2>Auth</h2>
    <p id="log_status">Is auth: {{ isAuth ? 'oui' : 'non' }}</p>
    <p id="log_err" class="red">{{ securityRequests.login.message }}</p>
    <h2>Users</h2>
    <p id="user_loading">User loading: {{ userRequests.getAll.loading ? 'oui' : 'non' }}</p>
    <p id="user_status">User length: {{ users.length }}</p>
    <p id="user_err" class="red">{{ userRequests.getAll.message }}</p>
    <button id="user_but" @click="findUsers">Get users</button>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import HelloWorld from '@/components/HelloWorld.vue'
import { mapGetters, mapState } from 'vuex'

@Options({
  name: 'Home',
  components: {
    HelloWorld
  },
  computed: {
    ...mapState('security', {
      securityRequests: 'actionRequest'
    }),
    ...mapGetters('security', {
      isAuth: 'getIsAuth'
    }),
    ...mapState('users', {
      userRequests: 'actionRequest'
    }),
    ...mapState('users', ['users'])
  },
  methods: {
    findUsers () {
      this.$store.dispatch('users/getAll')
    }
  }
})
export default class Home extends Vue {}
</script>

<style lang="scss" scoped>
.red {
  color: #ff0000;
}
</style>
