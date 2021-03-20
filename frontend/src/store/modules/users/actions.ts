import { httpReq } from '@/helpers/api'
import { ActionContext } from 'vuex'

export const actions = {
  all ({ commit }: ActionContext<any, any>) {
    commit('setIsLoading', true)
    httpReq('GET', '/api/users')
      .then(response => {
        commit('setErrorMsg', null)
        commit('setUsers', response.data)
      })
      .catch(err => {
        commit('setErrorMsg', err.response.data.message)
      })
      .finally(() => {
        commit('setIsLoading', false)
      })
  }
}
