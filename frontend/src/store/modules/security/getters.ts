import { SecurityState } from './types'

export const getters = {
  getIsAuth: (state: SecurityState) => {
    return !state.me.roles.includes('ROLE_ANONYMOUS')
  },
  getRefreshToken: (state: SecurityState) => {
    return state.refreshToken
  },
  getRoles: (state: SecurityState) => {
    return state.me.roles
  },
  getToken: (state: SecurityState) => {
    return state.apiToken
  }
}
