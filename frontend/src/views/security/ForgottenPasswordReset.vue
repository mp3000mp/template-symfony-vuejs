<script lang="ts" setup>
import { onMounted, ref, computed } from 'vue'
import { useSecurityStore } from '@/stores/security'
import { useRoute, useRouter } from 'vue-router'

const props = defineProps<{
  init: boolean;
}>()
const securityStore = useSecurityStore()
const router = useRouter()
const route = useRoute()

const displayForm = ref(true)
const password = ref('')
const passwordConfirm = ref('')

const securityRequests = computed(() => securityStore.actionRequest)
const checkTokenIsError = computed(() => {
  return props.init ? securityRequests.value.initPasswordCheckToken.status !== 200 : securityRequests.value.forgottenPasswordCheckToken.status !== 200
})
const checkTokenMessage = computed(() => {
  return props.init ? securityRequests.value.initPasswordCheckToken.message : securityRequests.value.forgottenPasswordCheckToken.message
})
const resetIsError = computed(() => {
  return props.init ? securityRequests.value.initPasswordReset.status !== 200 : securityRequests.value.forgottenPasswordReset.status !== 200
})
const resetMessage = computed(() => {
  return props.init ? securityRequests.value.initPasswordReset.message : securityRequests.value.forgottenPasswordReset.message
})

function checkToken () {
  if (props.init) {
    securityStore.initPasswordCheckToken(route.params.token)
  } else {
    securityStore.forgottenPasswordCheckToken(route.params.token)
  }
}
async function reset () {
  if (props.init) {
    await securityStore.initPasswordReset({
      token: route.params.token,
      password: password.value,
      passwordConfirm: passwordConfirm.value
    })
  } else {
    await securityStore.forgottenPasswordReset({
      token: route.params.token,
      password: password.value,
      passwordConfirm: passwordConfirm.value
    })
  }
  password.value = ''
  passwordConfirm.value = ''
  displayForm.value = false
  setTimeout(() => {
    router.push({ path: '/login' })
  }, 2000)
}

onMounted(() => {
  checkToken()
})
</script>

<template>
  <form @submit.prevent="reset" id="init-password-form" class="basic-form">
    <div
      v-if="displayForm && !checkTokenIsError"
      class="d-flex flex-column"
    >
      <label for="pwd" class="form-label"></label>
      <input
        class="form-control"
        type="password"
        name="pwd"
        id="pwd"
        v-model="password"
        placeholder="New password"
      />
      <label for="pwd_confirm" class="form-label"></label>
      <input
        class="form-control mb-2"
        type="password"
        name="pwd_confirm"
        id="pwd_confirm"
        v-model="passwordConfirm"
        placeholder="Password confirmation"
      />
      <input class="btn btn-primary" type="submit" value="Send" />
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
