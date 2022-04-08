<script lang="ts" setup>
import { computed, onMounted } from 'vue'
import LayoutHeader from '@/views/layout/Header.vue'
import { useAppStore } from '@/stores/app'
import variables from '../config/variables.json'

const appStore = useAppStore()

const backVersion = computed(() => appStore.version)
const frontVersion = variables.APP_VERSION
onMounted(() => {
  appStore.getInfo()
})
</script>

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
