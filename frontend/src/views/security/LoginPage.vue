<script lang="ts" setup>
import { ref, reactive, computed, watch } from 'vue'
import { useSecurityStore } from '@/stores/security'
import { useRouter } from 'vue-router'
import { AxiosResponse } from 'axios'

const securityStore = useSecurityStore()
const router = useRouter()

const forgottenPasswordSend = reactive({
  email: '',
  message: '',
  show: false,
  status: true
})
const password = ref('')
const username = ref('')

const me = computed(() => securityStore.me)
const securityRequests = computed(() => securityStore.actionRequest)

async function login () {
  await securityStore.login({
    username: username.value,
    password: password.value
  })
}
function sendForgottenPasswordEmail () {
  securityStore.forgottenPasswordSend(forgottenPasswordSend.email)
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
</script>

<template>
  <div class="container-fluid text-center">
    <h1>Welcome</h1>
    <form @submit.prevent="login" class="d-flex flex-column basic-form" id="login-form">
      <label for="username" class="form-label"></label>
      <input required="required" class="form-control" id="username" type="text" placeholder="Username" v-model="username" />
      <label for="password" class="form-label"></label>
      <input required="required" class="form-control mb-2" id="password" type="password" placeholder="Password" v-model="password" />
      <input class="btn btn-primary" type="submit" value="Log in" />
      <span class="err">{{ securityRequests.login.message }}</span>
    </form>
    <button @click="forgottenPasswordSend.show = !forgottenPasswordSend.show" class="btn btn-link" data-cy="forgottenPasswordButton">Forgotten password</button>
    <form v-if="forgottenPasswordSend.show" @submit.prevent="sendForgottenPasswordEmail" id="forgotten-password-form" class="d-flex flex-column basic-form">
      <label for="email"></label>
      <input class="form-control mb-2" required="required" id="email" type="email" placeholder="email@example.com" v-model="forgottenPasswordSend.email" />
      <input class="btn btn-primary" type="submit" value="Send forgotten password email" />
      <span :class="{
        err: securityRequests.forgottenPasswordSend.isError,
        success: !securityRequests.forgottenPasswordSend.isError
      }">{{ securityRequests.forgottenPasswordSend.message }}</span>
    </form>
  </div>
</template>
