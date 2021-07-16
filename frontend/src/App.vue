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
    <div>v{{ version }}</div>
  </footer>
</template>

<script lang="ts">
import { computed, defineComponent, onMounted } from 'vue'
import LayoutHeader from '@/views/layout/Header.vue'
import { useStore } from '@/store'

export default defineComponent({
  name: 'App',
  components: {
    LayoutHeader
  },
  setup () {
    const store = useStore()
    const version = computed(() => store.state.app.version)
    onMounted(() => {
      store.dispatch('app/getInfo')
    })

    return { version }
  }
})
</script>
