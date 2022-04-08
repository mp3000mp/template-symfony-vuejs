import { state } from './state'
import { defineStore } from 'pinia'
import apiRegistry from '@/helpers/apiRegistry'
import { ApiClient } from '@/helpers/apiClient'
import { Me } from '@/stores/security/types'

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

// todo: refactor localstorage functions in utils
function setApiToken (token: string|null) {
  if (token === null) {
    localStorage.removeItem('apiToken')
  }
  localStorage.setItem('apiToken', token || '')
}
function setMe (me: Me|null) {
  if (me === null) {
    localStorage.removeItem('me')
  }
  localStorage.setItem('me', JSON.stringify(me || (new Me())))
}
function setRefreshToken (refreshToken: string|null) {
  if (refreshToken === null) {
    localStorage.removeItem('refreshToken')
  }
  localStorage.setItem('refreshToken', refreshToken || '')
}

// todo: define actions in separate file when thie issue is closed: https://github.com/vuejs/pinia/issues/802
export const useSecurityStore = defineStore('security', {
  state: () => state,
  getters: {
    getIsAuth: (state) => !state.me.roles.includes('ROLE_ANONYMOUS'),
    getRoles: (state) => state.me.roles
  },
  actions: {
    async forgottenPasswordCheckToken (token: string) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.forgottenPasswordCheckToken, { urlParams: { token: token } })
      } catch (err) {
        // return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async forgottenPasswordSend (email: string) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.forgottenPasswordSend, { data: { email: email } })
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async forgottenPasswordReset (data: ForgottenPasswordResetPayload) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.forgottenPasswordReset, { urlParams: { token: data.token }, data: { password: data.password, passwordConfirm: data.passwordConfirm } })
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async initPasswordCheckToken (token: string) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.initPasswordCheckToken, { urlParams: { token: token } })
      } catch (err) {
        // return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async initPasswordReset (data: ForgottenPasswordResetPayload) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.initPasswordReset, { urlParams: { token: data.token }, data: { password: data.password, passwordConfirm: data.passwordConfirm } })
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async login (data: LoginPayload) {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.login, { data })
        this.apiToken = response.data.token
        this.refreshToken = response.data.refreshToken
        this.getMe()
      } catch (err) {
        this.me = new Me()
        setMe(this.me)
        this.apiToken = null
        this.refreshToken = null
      } finally {
        setApiToken(this.apiToken)
        setRefreshToken(this.refreshToken)
      }
    },
    logout () {
      this.me = new Me()
      setMe(this.me)
      this.apiToken = null
      setApiToken(this.apiToken)
      this.refreshToken = null
      setRefreshToken(this.refreshToken)
    },
    async getMe () {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.getMe)
        this.me = response.data
      } catch (err) {
        this.me = new Me()
      } finally {
        setMe(this.me)
      }
    },
    async refreshLogin () {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.refreshToken, { data: { refreshToken: this.refreshToken } })
        this.apiToken = response.data.token
        this.refreshToken = response.data.refreshToken
      } catch (err) {
        this.me = new Me()
        setMe(this.me)
        this.apiToken = null
        this.refreshToken = null
        return Promise.reject(ApiClient.generateErrorMessage(err))
      } finally {
        setApiToken(this.apiToken)
        setRefreshToken(this.refreshToken)
      }
    },
    async resetPassword (data: ResetPasswordPayload) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.resetPassword, { data })
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    }
  }
})
