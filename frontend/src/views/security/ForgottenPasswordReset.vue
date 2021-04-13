<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'

@Options({
  name: 'SecurityForgottenPasswordReset',
  data () {
    return {
      displayForm: true,
      password: '',
      passwordConfirm: ''
    }
  },
  props: {
    init: { type: Boolean, required: true }
  },
  computed: {
    ...mapState('security', ['actionRequest']),
    checkTokenIsError () {
      return this.init ? this.actionRequest.initPasswordCheckToken.status !== 200 : this.actionRequest.forgottenPasswordCheckToken.status !== 200
    },
    checkTokenMessage () {
      return this.init ? this.actionRequest.initPasswordCheckToken.message : this.actionRequest.forgottenPasswordCheckToken.message
    },
    resetIsError () {
      return this.init ? this.actionRequest.initPasswordReset.status !== 200 : this.actionRequest.forgottenPasswordReset.status !== 200
    },
    resetMessage () {
      return this.init ? this.actionRequest.initPasswordReset.message : this.actionRequest.forgottenPasswordReset.message
    }
  },
  methods: {
    checkToken () {
      const action = this.init ? 'security/initPasswordCheckToken' : 'security/forgottenPasswordCheckToken'
      this.$store.dispatch(action, this.$route.params.token)
    },
    async reset () {
      const action = this.init ? 'security/initPasswordReset' : 'security/forgottenPasswordReset'
      await this.$store.dispatch(action, {
        token: this.$route.params.token,
        password: this.password,
        passwordConfirm: this.passwordConfirm
      })
      this.password = ''
      this.passwordConfirm = ''
      this.displayForm = false
      setTimeout(() => {
        this.$router.push({ path: '/login' })
      }, 2000)
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
    <div v-if="displayForm && !checkTokenIsError">
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
      v-if="checkTokenIsError"
    >
      {{ checkTokenMessage }}
    </span>
    <span :class="{
      err: resetIsError,
      success: !resetIsError
    }">
      {{ resetMessage }}
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
