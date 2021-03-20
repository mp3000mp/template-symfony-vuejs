<template>
    <div v-if="isAuthenticating">
      <h1>Authenticating...</h1>
    </div>
</template>

<script lang="ts">
// import swal from 'sweetalert2'
import { defineComponent } from '@vue/composition-api'
import { mapState } from 'vuex'

export default defineComponent({
  /* data () {

  }, */
  computed: {
    ...mapState('security', ['isLoading'])
  },
  methods: {
    async connect () {
      try {
        const response = await this.$http.post('http://localhost:5000/api/logincheck', {
          username: 'mp3000',
          password: 'Test2000!'
        })
        const token = response.data.token
        console.log(token)
      } catch (err) {
        console.log(err.response)
      }
    }
  },
  mounted: function () {
    this.connect()
  }
})
</script>

<style scoped lang="scss">
  h1 {
    color: #2ca02c;
  }
</style>
