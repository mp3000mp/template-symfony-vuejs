<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'
import { HTMLElementEvent } from '@/utils/types'
import AdminUserRow from '@/views/admin/UserRow.vue'

@Options({
  name: 'AdminUserPage',
  components: {
    AdminUserRow
  },
  data () {
    return {
      newUser: {
        email: '',
        isEnabled: true,
        roles: ['ROLE_USER'],
        username: ''
      }
    }
  },
  computed: {
    ...mapState('users', {
      userRequests: 'actionRequest'
    }),
    ...mapState('users', ['users'])
  },
  mounted () {
    this.getAllUsers()
  },
  methods: {
    addUser () {
      this.$store.dispatch('users/addUser', this.newUser)
    },
    getAllUsers () {
      this.$store.dispatch('users/getAll')
      for (let i = 0; i < this.users.length; i++) {
        // Vue.set(this.users[i], 'isUpdating', false)
      }
    },
    setRoles (event: HTMLElementEvent<HTMLSelectElement>) {
      if (event.target.value === 'admin') {
        this.newUser.roles.push('ROLE_ADMIN')
      } else {
        const i = this.newUser.roles.indexOf('ROLE_ADMIN')
        if (i > -1) {
          this.newUser.roles.splice(i, 1)
        }
      }
    }
  }
})
export default class AdminUserPage extends Vue {}
</script>

<template>
  <div class="container">
    <h1>Users</h1>
    <div class="table-responsive">
      <!-- pourquoi table-striped et table-hover marchent po ? -->
      <table class="table">
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>roles</th>
          <th>Action</th>
        </tr>
        <admin-user-row
          v-for="user in users"
          :key="user.id"
          :user="user"
        />
        <tr>
          <td colspan="3">
            <form @submit.prevent="addUser">
              <label for="newUsername"></label><input required="required" type="text" placeholder="Username" v-model="newUser.username" id="newUsername" />
              <label for="newEmail"></label><input required="required" type="email" placeholder="Email" v-model="newUser.email" id="newEmail" />
              <label for="newRoles"></label><select id="newRoles" @change="setRoles"><option value="user">User</option><option value="admin">Admin</option></select>
              <input type="submit" value="New user" />
            </form>
            <span class="err">{{ userRequests.addUser.message }}</span>
        </td>
        </tr>
      </table>
    </div>
  </div>
</template>
