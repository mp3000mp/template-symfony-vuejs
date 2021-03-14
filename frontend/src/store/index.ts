import { createStore } from 'vuex'

import security from './modules/security'

export default createStore({
  state: {
    test: true
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    security
  }
})
