<template>
    <div v-show="!authenticated">
        <h1>Salut auth 27</h1>
    </div>
</template>

<script lang="ts">
import { defineComponent } from '@vue/composition-api'

export default defineComponent({
  data () {
    return {
      authenticated: false
    }
  }
})
</script>

<style scoped lang="scss">
  h1 {
    color: green;
  }
</style>
