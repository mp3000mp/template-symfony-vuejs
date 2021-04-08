import { SecurityState } from '@/store/modules/security/types'

export const getters = {
  getIsAuth: (state: SecurityState) => {
    return !state.me.roles.includes('ROLE_ANONYMOUS')
  },
  getRefreshToken: (state: SecurityState) => {
    return state.refreshToken
  },
  getToken: (state: SecurityState) => {
    return state.apiToken
  }
}
