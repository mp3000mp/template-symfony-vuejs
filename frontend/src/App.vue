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
import { defineComponent } from 'vue'
import LayoutHeader from '@/views/layout/Header.vue'

export default defineComponent({
  name: 'App',
  components: {
    LayoutHeader
  }
})
</script>
