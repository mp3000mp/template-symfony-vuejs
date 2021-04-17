import { createStore, Store } from 'vuex'
import { RootState } from './types'

import security from './modules/security/index'
import users from './modules/users/index'

const store = createStore({
  modules: {
    security,
    users
  }
})

export function useStore (): Store<RootState> {
  return store
}

export default store
