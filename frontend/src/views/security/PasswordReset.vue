<template>
  <div>
    <span class="err">{{ tokenError || '' }}</span>
    <div v-if="isTokenValid">
      <label for="pwd">
        New password:
        <input type="password" name="pwd" id="pwd" v-model="password" v-on:keyup.enter="reset" />
      </label>
      <button @click.prevent="reset">Send</button>
    </div>
    <span :class="{
      err: !isResetSuccess,
      success: isResetSuccess
    }">
      {{ passwordMsg || '' }}
    </span>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { httpReq } from '@/helpers/api'

@Options({
  data () {
    return {
      isResetSuccess: false,
      isTokenValid: false,
      password: '',
      passwordMsg: null,
      tokenError: null
    }
  },
  methods: {
    checkToken () {
      httpReq('GET', `/api/password/reset/${this.$route.params.token}`)
        .then(() => {
          this.isTokenValid = true
        })
        .catch(err => {
          this.isTokenValid = false
          this.tokenError = err.response.data.message
        })
    },
    reset () {
      httpReq('POST', '/api/password/reset', {
        token: this.$route.params.token,
        password: this.password
      })
        .then(() => {
          this.passwordMsg = 'Password has been reset.'
          this.isResetSuccess = true
          setTimeout(() => {
            this.$router.push({ path: '/' })
          }, 2000)
        })
        .catch(err => {
          this.isResetSuccess = false
          this.passwordMsg = err.response.data.message
        })
    }
  },
  mounted () {
    this.checkToken()
  }
})
export default class Home extends Vue {}
</script>

<style lang="scss" scoped>
.err {
  color: #ff0000;
}

.success {
  color: #00ff00;
}
</style>
