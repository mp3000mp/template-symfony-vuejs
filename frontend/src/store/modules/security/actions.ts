import { httpReq } from '@/helpers/api'
import { ActionContext } from 'vuex'

export const actions = {
  login ({ commit }: ActionContext<any, any>) {
    commit('setIsLoading', true)
    return httpReq('POST', '/api/logincheck', { username: 'mp3000', password: 'Test2000!' })
      .then(response => {
        commit('setErrorMsg', null)
        commit('setApiToken', response.data.token)
        commit('setRefreshToken', response.data.refreshToken)
        commit('setIsAuthenticated', true)
      })
      .catch(err => {
        commit('setErrorMsg', err.response.data.message)
        commit('setApiToken', null)
        commit('setRefreshToken', null)
        commit('setIsAuthenticated', false)
      })
      .finally(() => {
        commit('setIsLoading', false)
      })
  },
  refreshLogin ({ commit, getters }: ActionContext<any, any>) {
    commit('setIsLoading', true)
    return httpReq('POST', '/api/token/refresh', { refreshToken: getters.getRefreshToken })
      .then(response => {
        commit('setErrorMsg', null)
        commit('setApiToken', response.data.token)
        commit('setRefreshToken', response.data.refreshToken)
        commit('setIsAuthenticated', true)
      })
      .catch(err => {
        commit('setErrorMsg', err.response.data.message)
        commit('setApiToken', null)
        commit('setRefreshToken', null)
        commit('setIsAuthenticated', false)
      })
      .finally(() => {
        commit('setIsLoading', false)
      })
  },
  logout ({ commit }: ActionContext<any, any>) {
    // todo send to server ?
    commit('setApiToken', null)
    commit('setRefreshToken', null)
    commit('setIsAuthenticated', false)
  }
}
