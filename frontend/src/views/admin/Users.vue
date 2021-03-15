<template>
  <div>
    <h1>Users</h1>
    <p v-for="user in users" :key="user.id">{{ user.username }}</p>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { AxiosResponse } from 'axios'

@Options({
  data () {
    return {
      users: []
    }
  },
  mounted () {
    this.$http.get('http://localhost:5000/api/users')
      .then((response: AxiosResponse) => {
        this.users = response.data
      })
  }
})
export default class Users extends Vue {}
</script>
