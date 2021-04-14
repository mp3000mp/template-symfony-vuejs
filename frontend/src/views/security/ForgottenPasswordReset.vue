<script lang="ts">
import { onMounted, defineComponent, ref, computed } from 'vue'
import { useStore } from '@/store'
import { useRoute, useRouter } from 'vue-router'

export default defineComponent({
  name: 'SecurityForgottenPasswordReset',
  props: {
    init: { type: Boolean, required: true }
  },
  setup (props) {
    const store = useStore()
    const router = useRouter()
    const route = useRoute()

    const displayForm = ref(true)
    const password = ref('')
    const passwordConfirm = ref('')

    const securityRequests = computed(() => store.state.security.actionRequest)
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
      const action = props.init ? 'security/initPasswordCheckToken' : 'security/forgottenPasswordCheckToken'
      store.dispatch(action, route.params.token)
    }
    async function reset () {
      const action = props.init ? 'security/initPasswordReset' : 'security/forgottenPasswordReset'
      await store.dispatch(action, {
        token: route.params.token,
        password: password.value,
        passwordConfirm: passwordConfirm.value
      })
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

    return { reset, displayForm, checkTokenIsError, password, passwordConfirm, checkTokenMessage, resetIsError, resetMessage }
  }
})
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
