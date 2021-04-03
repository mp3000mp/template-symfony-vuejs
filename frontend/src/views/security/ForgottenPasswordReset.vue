<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'
import { AxiosResponse } from 'axios'

@Options({
  name: 'SecurityForgottenPasswordReset',
  data () {
    return {
      displayForm: true,
      password: '',
      passwordConfirm: ''
    }
  },
  computed: {
    ...mapState('security', ['actionRequest'])
  },
  methods: {
    checkToken () {
      this.$store.dispatch('security/forgottenPasswordCheckToken', this.$route.params.token)
    },
    async reset () {
      await this.$store.dispatch('security/forgottenPasswordReset', {
        token: this.$route.params.token,
        password: this.password,
        passwordConfirm: this.passwordConfirm
      })
      this.password = ''
      this.passwordConfirm = ''
      if (!this.actionRequest.forgottenPasswordReset.isError) {
        this.displayForm = false
        setTimeout(() => {
          this.$router.push({ path: '/login' })
        }, 2000)
      }
    }
  },
  mounted () {
    this.checkToken()
  }
})
export default class SecurityPasswordReset extends Vue {}
</script>

<template>
  <form @submit.prevent="reset">
    <div v-if="displayForm && actionRequest.forgottenPasswordCheckToken.status === 200">
      <label for="pwd">
        New password:
        <input type="password" name="pwd" id="pwd" v-model="password" />
      </label>
      <label for="pwd_confirm">
        Password confirmation:
        <input type="password" name="pwd_confirm" id="pwd_confirm" v-model="passwordConfirm" />
      </label>
      <input type="submit" value="send" />
    </div>
    <span
      class="err"
      v-if="actionRequest.forgottenPasswordCheckToken.status !== 200"
    >
      {{ actionRequest.forgottenPasswordCheckToken.message }}
    </span>
    <span :class="{
      err: actionRequest.forgottenPasswordReset.status !== 200,
      success: actionRequest.forgottenPasswordReset.status === 200
    }">
      {{ actionRequest.forgottenPasswordReset.message }}
    </span>
  </form>
</template>

<style lang="scss" scoped>
.err {
  color: #ff0000;
}

.success {
  color: #00ff00;
}
</style>
