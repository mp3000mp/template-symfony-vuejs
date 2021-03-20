<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <authentication />
  </div>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import Authentication from './Security/Authentication.vue'

@Options({
  components: { Authentication },
  props: {
    msg: String
  }
})
export default class HelloWorld extends Vue {
  msg!: string
}
</script>
