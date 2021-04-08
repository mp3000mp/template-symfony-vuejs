import { createStore } from 'vuex'
import { state } from './state'

import security from './modules/security/index'
import users from './modules/users/index'

export default createStore({
  state,
  modules: {
    security,
    users
  }
})
