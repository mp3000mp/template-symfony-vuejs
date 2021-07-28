<script lang="ts">
import { defineComponent, computed, reactive } from 'vue'
import { useStore } from '@/store'

export default defineComponent({
  name: 'AccountPage',
  setup () {
    const store = useStore()

    const resetPassword = reactive({
      currentPassword: '',
      newPassword: '',
      newPassword2: '',
      show: false
    })

    const me = computed(() => store.state.security.me)
    const securityRequests = computed(() => store.state.security.actionRequest)

    async function submitResetPassword () {
      await store.dispatch('security/resetPassword', {
        currentPassword: resetPassword.currentPassword,
        newPassword: resetPassword.newPassword,
        newPassword2: resetPassword.newPassword2
      })
      resetPassword.currentPassword = ''
      resetPassword.newPassword = ''
      resetPassword.newPassword2 = ''
      if (!securityRequests.value.resetPassword.isError) {
        resetPassword.show = false
      }
    }

    return { resetPassword, me, securityRequests, submitResetPassword }
  }
})
</script>

<template>
  <div class="container text-center">
    <h1>Account</h1>
    <div class="app-block">
      <table>
        <tr>
          <th>Username: </th>
          <td>{{ me.username }}</td>
        </tr>
      </table>
      <button v-if="!resetPassword.show" class="btn btn-primary" @click="resetPassword.show = !resetPassword.show">Reset password</button>
      <form v-if="resetPassword.show" @submit.prevent="submitResetPassword" class="d-flex flex-column basic-form">
        <label class="form-label" for="current_password"></label>
        <input class="form-control" v-model="resetPassword.currentPassword" id="current_password" name="current_password" type="password" placeholder="Current password" />
        <label class="form-label" for="new_password"></label>
        <input class="form-control" v-model="resetPassword.newPassword" id="new_password" name="new_password" type="password" placeholder="New password" />
        <label class="form-label" for="new_password2"></label>
        <input class="form-control mb-2" v-model="resetPassword.newPassword2" id="new_password2" name="new_password2" type="password" placeholder="Confirm new password" />
        <input class="btn btn-primary mb-2" type="submit" value="Reset password" />
        <input @click.prevent="resetPassword.show = false" class="btn btn-secondary" type="submit" value="Cancel" />
      </form>
      <span :class="{
        err: securityRequests.resetPassword.isError,
        success: !securityRequests.resetPassword.isError
      }">{{ securityRequests.resetPassword.message }}</span>
    </div>
  </div>
</template>
