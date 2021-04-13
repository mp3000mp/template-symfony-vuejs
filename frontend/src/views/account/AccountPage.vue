<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'

@Options({
  name: 'AccountPage',
  data () {
    return {
      resetPassword: {
        currentPassword: '',
        newPassword: '',
        newPassword2: '',
        show: false
      }
    }
  },
  computed: {
    ...mapState('security', ['me']),
    ...mapState('security', {
      securityRequests: 'actionRequest'
    })
  },
  methods: {
    async submitResetPassword () {
      await this.$store.dispatch('security/resetPassword', {
        currentPassword: this.resetPassword.currentPassword,
        newPassword: this.resetPassword.newPassword,
        newPassword2: this.resetPassword.newPassword2
      })
      this.resetPassword.currentPassword = ''
      this.resetPassword.newPassword = ''
      this.resetPassword.newPassword2 = ''
      if (!this.securityRequests.resetPassword.isError) {
        this.resetPassword.show = false
      }
    }
  }
})
export default class AccountPage extends Vue {}
</script>

<template>
  <div class="container text-center">
    <h1>Account</h1>
    <table>
      <tr>
        <th>Username: </th>
        <td>{{ me.username }}</td>
      </tr>
    </table>
    <button @click="resetPassword.show = true">Reset password</button>
    <form v-if="resetPassword.show" @submit.prevent="submitResetPassword">
      <label for="current_password"></label>
      <input v-model="resetPassword.currentPassword" id="current_password" name="current_password" type="password" placeholder="Current password" />
      <label for="new_password"></label>
      <input v-model="resetPassword.newPassword" id="new_password" name="new_password" type="password" placeholder="New password" />
      <label for="new_password2"></label>
      <input v-model="resetPassword.newPassword2" id="new_password2" name="new_password2" type="password" placeholder="Confirm new password" />
      <input type="submit" value="Reset password" />
    </form>
    <span :class="{
        err: securityRequests.resetPassword.isError,
        success: !securityRequests.resetPassword.isError
      }">{{ securityRequests.resetPassword.message }}</span>
  </div>
</template>

<style lang="scss">
/* todo trouver pourquoi non trouver dans bootstrap ? */

.text-center {
  text-align: center;
}

table {
  margin: auto;

  th {
    text-align: right;
  }
}

.err {
  color: #8d0502;
}

.success {
  color: #2ca02c;
}
</style>
