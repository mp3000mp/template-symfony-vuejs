<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'

@Options({
  name: 'AdminUserRowPage',
  props: {
    user: { type: Object, required: true }
  },
  data () {
    return {
      isUpdating: false,
      profile: 'user',
      tmpUser: {
        id: null,
        email: '',
        username: ''
      }
    }
  },
  mounted () {
    this.initTmpUser()
  },
  computed: {
    ...mapState('users', {
      userRequests: 'actionRequest'
    }),
    roles () {
      if (this.profile === 'user') {
        return ['ROLE_USER']
      } else {
        return ['ROLE_USER', 'ROLE_ADMIN']
      }
    }
  },
  methods: {
    closeForm () {
      this.isUpdating = false
    },
    deleteUser () {
      if (confirm('Do you confirm you want to delete this user ?')) {
        this.$store.dispatch('users/deleteUser', this.user.id)
      }
    },
    async disableUser () {
      try {
        await this.$store.dispatch('users/disableUser', this.user.id)
      } catch (err) {
        alert(err)
      }
    },
    enableUser () {
      this.$store.dispatch('users/enableUser', this.user.id)
    },
    initForm () {
      this.initTmpUser()
      this.isUpdating = true
    },
    initProfile () {
      if (this.user.roles.length === 1) {
        this.profile = 'user'
      } else {
        this.profile = 'admin'
      }
    },
    initTmpUser () {
      this.tmpUser.id = this.user.id
      this.tmpUser.email = this.user.email
      this.tmpUser.roles = this.user.roles
      this.tmpUser.username = this.user.username
      this.initProfile()
    },
    async updateUser () {
      this.tmpUser.roles = this.roles
      try {
        await this.$store.dispatch('users/updateUser', this.tmpUser)
        this.closeForm()
      } catch (err) {
        alert(err)
      }
    }
  }
})
export default class AdminUserRowPage extends Vue {}
</script>

<template>
  <tr v-if="isUpdating">
    <td colspan="4">
      <form @submit.prevent="updateUser">
        <label for="updateUsername"></label><input required="required" type="text" placeholder="Username" v-model="tmpUser.username" id="updateUsername" />
        <label for="updateEmail"></label><input required="required" type="email" placeholder="Email" v-model="tmpUser.email" id="updateEmail" />
        <label for="updateRoles"></label><select id="updateRoles" v-model="profile"><option value="user">User</option><option value="admin">Admin</option></select>
        <input type="submit" value="Update user" />
        <input type="submit" value="Cancel" @click.prevent="closeForm" />
      </form>
    </td>
  </tr>
  <tr v-else>
    <td>{{ user.username }}</td>
    <td>{{ user.email }}</td>
    <td>{{ user.roles.join(', ') }}</td>
    <td>
      <a
        v-if="!user.isEnabled"
        href="JavaScript:void(0)"
        @click.prevent="enableUser(user)"
        title="Enable"
      >
        <font-aw icon="user-times" />
      </a>
      <a
        v-if="user.isEnabled"
        href="JavaScript:void(0)"
        @click.prevent="disableUser(user)"
        title="Disable"
      >
        <font-aw icon="user-check" />
      </a>
      <a
        href="JavaScript:void(0)"
        @click.prevent="initForm"
      >
        <font-aw :icon="['far', 'edit']" />
      </a>
      <a href="JavaScript:void(0)"
         @click.prevent="deleteUser(user)"
         v-if="!user.isEnabled"
         title="Delete"
      >
        <font-aw :icon="['far', 'trash-alt']" />
      </a>
    </td>
  </tr>
</template>
