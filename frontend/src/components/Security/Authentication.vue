<template>
    <div v-show="isAuthenticating">
        <h1>Auth en cours</h1>
    </div>
    <h1 v-if="isAuthenticated">OUI</h1>
    <h1 v-else>NON</h1>

</template>

<script lang="ts">
import swal from 'sweetalert2'
import { defineComponent } from '@vue/composition-api'
import { mapGetters, mapState } from 'vuex'

export default defineComponent({
  data () {
    return {

    }
  },
  computed: {
    ...mapGetters('security', ['isAuthenticated']),
    ...mapState('security', ['isAuthenticating'])
  },
  methods: {
    refreshPopup: function () {
      console.log(this.isAuthenticated)
      if (!this.isAuthenticated) {
        swal.fire('test')
      }
    }
  },
  mounted: function () {
    this.refreshPopup()
  },
  watch: {
    isAuthenticated: function () {
      this.refreshPopup()
    }
  }
})
</script>

<style scoped lang="scss">
  h1 {
    color: #2ca02c;
  }
</style>
