<script lang="ts">
import { Options, Vue } from 'vue-class-component'

@Options({
  name: 'Home'
})
export default class Home extends Vue {}
</script>

<template>
  <div class="home">
    <h1>Bienvenue</h1>
  </div>
</template>
