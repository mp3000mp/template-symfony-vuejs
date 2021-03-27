import { Me, SecurityState } from './types'

export const mutations = {
  setApiToken (state: SecurityState, token: string|null) {
    state.apiToken = token
    if (token === null) {
      localStorage.removeItem('apiToken')
    }
    localStorage.setItem('apiToken', token || '')
  },
  resetMe (state: SecurityState) {
    state.me = new Me()
    localStorage.removeItem('me')
  },
  setMe (state: SecurityState, me: Me) {
    state.me = me
    localStorage.setItem('me', JSON.stringify(me))
  },
  setRefreshToken (state: SecurityState, refreshToken: string|null) {
    state.refreshToken = refreshToken
    if (refreshToken === null) {
      localStorage.removeItem('refreshToken')
    }
    localStorage.setItem('refreshToken', refreshToken || '')
  }
}
