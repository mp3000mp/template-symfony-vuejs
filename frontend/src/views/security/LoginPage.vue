<script lang="ts">
import { defineComponent, ref, reactive, computed, watch } from 'vue'
import { useStore } from '@/store'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'LoginPage',
  setup () {
    const store = useStore()
    const router = useRouter()

    const forgottenPasswordSend = reactive({
      email: '',
      message: '',
      show: false,
      status: true
    })
    const password = ref('')
    const username = ref('')

    const me = computed(() => store.state.security.me)
    const securityRequests = computed(() => store.state.security.actionRequest)

    async function login () {
      await store.dispatch('security/login', {
        username: username.value,
        password: password.value
      })
    }
    function sendForgottenPasswordEmail () {
      store.dispatch('security/forgottenPasswordSend', forgottenPasswordSend.email)
        .then((res: any) => {
          forgottenPasswordSend.status = true
          forgottenPasswordSend.message = res.data.message
          forgottenPasswordSend.email = ''
        })
        .catch((err: any) => {
          forgottenPasswordSend.status = false
          forgottenPasswordSend.message = err.message || err.response.data.message
        })
    }

    watch(me, () => {
      router.push({ path: '/' })
    })

    return { login, password, username, forgottenPasswordSend, securityRequests, sendForgottenPasswordEmail }
  }
})
</script>

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
        err: securityRequests.forgottenPasswordSend.isError,
        success: !securityRequests.forgottenPasswordSend.isError
      }">{{ securityRequests.forgottenPasswordSend.message }}</span>
    </form>
  </div>
  <p>todo design</p>
</template>

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
