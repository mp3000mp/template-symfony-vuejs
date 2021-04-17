import apiRegistry from '@/helpers/apiRegistry'
import { ActionContext } from 'vuex'
import { SecurityState } from './types'
import { RootState } from '@/store/types'
import { ApiClient } from '@/helpers/apiClient'

interface LoginPayload {
  username: string;
  password: string;
}
interface ForgottenPasswordResetPayload {
  token: string;
  password: string;
  passwordConfirm: string;
}
interface ResetPasswordPayload {
  currentPassword: string;
  newPassword: string;
  newPassword2: string;
}

export const actions = {
  async forgottenPasswordCheckToken ({ state }: ActionContext<SecurityState, RootState>, token: string) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.forgottenPasswordCheckToken, { urlParams: { token: token } })
    } catch (err) {
      // return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async forgottenPasswordSend ({ state }: ActionContext<SecurityState, RootState>, email: string) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.forgottenPasswordSend, { data: { email: email } })
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async forgottenPasswordReset ({ state }: ActionContext<SecurityState, RootState>, data: ForgottenPasswordResetPayload) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.forgottenPasswordReset, { urlParams: { token: data.token }, data: { password: data.password, passwordConfirm: data.passwordConfirm } })
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async initPasswordCheckToken ({ state }: ActionContext<SecurityState, RootState>, token: string) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.initPasswordCheckToken, { urlParams: { token: token } })
    } catch (err) {
      // return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async initPasswordReset ({ state }: ActionContext<SecurityState, RootState>, data: ForgottenPasswordResetPayload) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.initPasswordReset, { urlParams: { token: data.token }, data: { password: data.password, passwordConfirm: data.passwordConfirm } })
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async login ({ commit, state, dispatch }: ActionContext<SecurityState, RootState>, data: LoginPayload) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.login, { data })
      commit('setApiToken', response.data.token)
      commit('setRefreshToken', response.data.refreshToken)
      dispatch('getMe')
    } catch (err) {
      commit('resetMe')
      commit('setApiToken', null)
      commit('setRefreshToken', null)
    }
  },
  logout ({ commit }: ActionContext<SecurityState, RootState>) {
    commit('resetMe')
    commit('setApiToken', null)
    commit('setRefreshToken', null)
  },
  async getMe ({ commit, state }: ActionContext<SecurityState, RootState>) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.getMe)
      commit('setMe', response.data)
    } catch (err) {
      commit('resetMe')
    }
  },
  async refreshLogin ({ commit, state, getters }: ActionContext<SecurityState, RootState>) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.refreshToken, { data: { refreshToken: getters.getRefreshToken } })
      commit('setApiToken', response.data.token)
      commit('setRefreshToken', response.data.refreshToken)
    } catch (err) {
      commit('resetMe')
      commit('setApiToken', null)
      commit('setRefreshToken', null)
    }
  },
  async resetPassword ({ state }: ActionContext<SecurityState, RootState>, data: ResetPasswordPayload) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.resetPassword, { data })
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  }
}
