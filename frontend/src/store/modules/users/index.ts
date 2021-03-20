import { actions } from './actions'
import { mutations } from './mutations'

export default {
  state: {
    errorMsg: null,
    isLoading: false,
    users: []
  },
  actions,
  mutations,
  namespaced: true
}
