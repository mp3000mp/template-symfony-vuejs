<template>
  <div>
    <span class="err">{{ tokenError || '' }}</span>
    <div v-if="actionRequest.forgottenPasswordCheckToken.status === 200">
      <label for="pwd">
        New password:
        <input type="password" name="pwd" id="pwd" v-model="password" v-on:keyup.enter="reset" />
      </label>
      <button @click.prevent="reset">Send</button>
    </div>
    <span
      class="err"
      v-if="actionRequest.forgottenPasswordCheckToken.status !== 200"
    >
      {{ actionRequest.forgottenPasswordCheckToken.message }}
    </span>
    <span :class="{
      err: actionRequest.forgottenPasswordReset.status !== 200,
      success: actionRequest.forgottenPasswordReset.status === 200
    }">
      {{ actionRequest.forgottenPasswordReset.message }}
    </span>
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import { mapState } from 'vuex'
import { AxiosResponse } from 'axios'

@Options({
  name: 'SecurityPasswordReset',
  data () {
    return {
      password: ''
    }
  },
  computed: {
    ...mapState('security', ['actionRequest'])
  },
  methods: {
    checkToken () {
      this.$store.dispatch('security/forgottenPasswordCheckToken', this.$route.params.token)
    },
    reset () {
      this.$store.dispatch('security/forgottenPasswordReset', {
        token: this.$route.params.token,
        password: this.password
      })
        .then((res: AxiosResponse) => {
          if (res.status === 200) {
            setTimeout(() => {
              this.$router.push({ path: '/' })
            }, 2000)
          }
        })
    }
  },
  mounted () {
    this.checkToken()
  }
})
export default class SecurityPasswordReset extends Vue {}
</script>

<style lang="scss" scoped>
.err {
  color: #ff0000;
}

.success {
  color: #00ff00;
}
</style>
