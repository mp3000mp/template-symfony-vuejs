import { SecurityState } from '@/store/modules/security/types'

export const getters = {
  getToken: (state: SecurityState) => {
    return state.apiToken
  },
  getRefreshToken: (state: SecurityState) => {
    return state.refreshToken
  }
}
