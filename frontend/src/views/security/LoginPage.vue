<script lang="ts">
import { defineComponent, ref, reactive, computed, watch } from 'vue'
import { useStore } from '@/store'
import { useRouter } from 'vue-router'
import { AxiosResponse } from 'axios'

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
        .then((res: AxiosResponse) => {
          forgottenPasswordSend.status = true
          forgottenPasswordSend.message = res.data.message
          forgottenPasswordSend.email = ''
        })
        .catch((err: string) => {
          forgottenPasswordSend.status = false
          forgottenPasswordSend.message = err
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
  <div class="container-fluid text-center form">
    <h1>Login</h1>
    <form @submit.prevent="login" class="d-flex flex-column" id="login-form">
      <label for="username" class="form-label"></label>
      <input required="required" class="txt-color form-control my-2s" id="username" type="text" placeholder="Username" v-model="username" />
      <label for="password" class="form-label"></label>
      <input required="required" class="form-control mb-2 txt-color" id="password" type="password" placeholder="Password" v-model="password" />
      <input class="btn-lg login-btn" type="submit" value="login" />
      <span class="err">{{ securityRequests.login.message }}</span>
    </form>
    <button @click="forgottenPasswordSend.show = true" class="btn btn-link" data-cy="forgottenPasswordButton">Forgotten password</button>
    <form v-if="forgottenPasswordSend.show" @submit.prevent="sendForgottenPasswordEmail" id="forgotten-password-form">
      <label for="email"></label>
      <input required="required" id="email" type="email" placeholder="email@example.com" v-model="forgottenPasswordSend.email" />
      <input type="submit" value="Send forgotten password email" />
      <span :class="{
        err: securityRequests.forgottenPasswordSend.isError,
        success: !securityRequests.forgottenPasswordSend.isError
      }">{{ securityRequests.forgottenPasswordSend.message }}</span>
    </form>
  </div>
  <p class="ms-3">todo design</p>
</template>

<style lang="scss">

@import 'src/assets/css/app';

.err {
  color: #8d0502;
}

.success {
  color: #2ca02c;
}

.login-btn{
  background-color: #cc8800;
}

input.txt-color,
input.txt-color:focus{
  color: #001b3d;
}
</style>
