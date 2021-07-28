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
    <div>Front v{{ frontVersion }} - back v{{ backVersion }}</div>
  </footer>
</template>

<script lang="ts">
import { computed, defineComponent, onMounted } from 'vue'
import LayoutHeader from '@/views/layout/Header.vue'
import { useStore } from '@/store'
import variables from '../config/variables.json'

export default defineComponent({
  name: 'App',
  components: {
    LayoutHeader
  },
  setup () {
    const store = useStore()
    const backVersion = computed(() => store.state.app.version)
    const frontVersion = variables.APP_VERSION
    onMounted(() => {
      store.dispatch('app/getInfo')
    })

    return { backVersion, frontVersion }
  }
})
</script>
