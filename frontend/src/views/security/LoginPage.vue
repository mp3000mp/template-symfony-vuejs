<template>
  <div class="container text-center">
    <h1>Login</h1>
    <form @submit.prevent="login">
      <label for="username"></label>
      <input required="required" id="username" type="text" placeholder="Username" v-model="username" />
      <label for="password"></label>
      <input required="required" id="password" type="password" placeholder="Password" v-model="password" />
      <input type="submit" value="login" />
      <span class="err">{{ securityRequests.login.message }}</span>
    </form>
    <button @click="forgottenPasswordSend.show = true">Forgotten password</button>
    <form v-if="forgottenPasswordSend.show" @submit.prevent="sendForgottenPasswordEmail">
      <label for="email"></label>
      <input required="required" id="email" type="email" placeholder="email@example.com" v-model="forgottenPasswordSend.email" />
      <input type="submit" value="Send forgotten password email" />
      <span :class="{
        err: !forgottenPasswordSend.status,
        success: forgottenPasswordSend.status
      }">{{ forgottenPasswordSend.message }}</span>
    </form>
  </div>
  <p>todo design</p>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'

@Options({
  name: 'LoginPage',
  data () {
    return {
      forgottenPasswordSend: {
        email: '',
        message: '',
        show: false,
        status: true
      },
      password: '',
      username: ''
    }
  },
  computed: {
    ...mapState('security', {
      securityRequests: 'actionRequest'
    })
  },
  methods: {
    login () {
      this.$store.dispatch('security/login', {
        username: this.username,
        password: this.password
      })
        .then(() => {
          if (this.securityRequests.login.status === 200) {
            this.$router.push({ path: '/' })
          }
        })
    },
    sendForgottenPasswordEmail () {
      this.$store.dispatch('security/forgottenPasswordSend', this.forgottenPasswordSend.email)
        .then((res: any) => {
          this.forgottenPasswordSend.status = true
          this.forgottenPasswordSend.message = res.data.message
          this.forgottenPasswordSend.email = ''
        })
        .catch((err: any) => {
          this.forgottenPasswordSend.status = false
          this.forgottenPasswordSend.message = err.message || err.response.data.message
        })
    }
  }
})
export default class AccountPage extends Vue {}
</script>

<style lang="scss">
/* todo trouver pourquoi non trouver dans bootstrap ? */

.text-center {
  text-align: center;
}

.err {
  color: #8d0502;
}

.success {
  color: #2ca02c;
}
</style>
