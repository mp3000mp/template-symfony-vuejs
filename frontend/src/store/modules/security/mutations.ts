import { SecurityState } from './types'

export const mutations = {
  setApiToken (state: SecurityState, token: string|null) {
    state.apiToken = token
    localStorage.setItem('apiToken', token || '')
  },
  setErrorMsg (state: SecurityState, errorMsg: string|null) {
    state.errorMsg = errorMsg
  },
  setIsAuthenticated (state: SecurityState, isAuthenticated: boolean) {
    state.isAuthenticated = isAuthenticated
  },
  setIsLoading (state: SecurityState, isLoading: boolean) {
    state.isLoading = isLoading
  },
  setRefreshToken (state: SecurityState, refreshToken: string|null) {
    state.refreshToken = refreshToken
    localStorage.setItem('refreshToken', refreshToken || '')
  }
}
