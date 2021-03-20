import { actions } from './actions'
import { getters } from './getters'
import { mutations } from './mutations'

export default {
  state: {
    apiToken: localStorage.getItem('apiToken'),
    errorMsg: null,
    isAuthenticated: false,
    isLoading: false,
    refreshToken: localStorage.getItem('refreshToken')
  },
  actions,
  getters,
  mutations,
  namespaced: true
}
