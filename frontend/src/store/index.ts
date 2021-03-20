import { createStore } from 'vuex'

import security from './modules/security/index'
import users from './modules/users/index'

export default createStore({
  state: {
    test: true
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    security,
    users
  }
})
