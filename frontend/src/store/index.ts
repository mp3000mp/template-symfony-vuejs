import { createStore, Store } from 'vuex'
import { RootState } from './types'

import app from './modules/app/index'
import security from './modules/security/index'
import users from './modules/users/index'

const store = createStore({
  modules: {
    app,
    security,
    users
  }
})

export function useStore (): Store<RootState> {
  return store
}

export default store
