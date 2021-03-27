<template>
  <div class="container text-center">
    <h1>Account</h1>
    <table>
      <tr>
        <th>Username: </th>
        <td>{{ me.username }}</td>
      </tr>
    </table>
    <p>Todo change password</p>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'

@Options({
  name: 'AccountPage',
  data () {
    return {

    }
  },
  computed: {
    ...mapState('security', ['me'])
  },
  methods: {

  }
})
export default class AccountPage extends Vue {}
</script>

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
</style>
