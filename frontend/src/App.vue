<template>
  <header id="nav">
    <layout-header/>
  </header>
  <router-view/>
  <footer>
    <font-aw icon="magic" /> Invoked from the magic kingdom by
    <a href="https://github.com/mp3000mp">
      mp3000
      <font-aw icon="hand-sparkles" />
    </a>
  </footer>
</template>

<script lang="ts">
import { Options, Vue } from 'vue-class-component'
import LayoutHeader from '@/views/layout/Header.vue'

@Options({
  components: {
    LayoutHeader
  }
})
export default class App extends Vue {}
</script>
