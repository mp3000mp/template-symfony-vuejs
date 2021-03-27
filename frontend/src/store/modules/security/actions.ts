import { httpReq } from '@/helpers/api'
import { ActionContext } from 'vuex'
import { SecurityState } from './types'
import { RootState } from '@/store/types'

interface LoginPayload {
  username: string;
  password: string;
}
interface ForgottenPasswordResetPayload {
  token: string;
  password: string;
}

export const actions = {
  forgottenPasswordCheckToken ({ state }: ActionContext<SecurityState, RootState>, token: string) {
    return httpReq(state.actionRequest.forgottenPasswordCheckToken, { urlParams: { token: token } })
  },
  forgottenPasswordSend ({ state }: ActionContext<SecurityState, RootState>, email: string) {
    return httpReq(state.actionRequest.forgottenPasswordSend, { data: { email: email } })
  },
  forgottenPasswordReset ({ state }: ActionContext<SecurityState, RootState>, data: ForgottenPasswordResetPayload) {
    return httpReq(state.actionRequest.forgottenPasswordReset, { urlParams: { token: data.token }, data: { password: data.password } })
  },
  login ({ commit, state, dispatch }: ActionContext<SecurityState, RootState>, data: LoginPayload) {
    return httpReq(state.actionRequest.login, { data })
      .then(response => {
        commit('setApiToken', response.data.token)
        commit('setRefreshToken', response.data.refreshToken)
        dispatch('getMe')
      })
      .catch(() => {
        commit('resetMe')
        commit('setApiToken', null)
        commit('setRefreshToken', null)
      })
  },
  logout ({ commit }: ActionContext<SecurityState, RootState>) {
    commit('resetMe')
    commit('setApiToken', null)
    commit('setRefreshToken', null)
  },
  getMe ({ commit, state }: ActionContext<SecurityState, RootState>) {
    httpReq(state.actionRequest.getMe)
      .then(response => {
        commit('setMe', response.data)
      })
      .catch(() => {
        commit('resetMe')
      })
  },
  refreshLogin ({ commit, state, getters }: ActionContext<SecurityState, RootState>) {
    return httpReq(state.actionRequest.refreshToken, { data: { refreshToken: getters.getRefreshToken } })
      .then(response => {
        commit('setApiToken', response.data.token)
        commit('setRefreshToken', response.data.refreshToken)
      })
      .catch(() => {
        commit('resetMe')
        commit('setApiToken', null)
        commit('setRefreshToken', null)
      })
  }
}
