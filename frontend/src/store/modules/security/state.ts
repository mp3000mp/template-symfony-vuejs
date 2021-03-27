import { Me, SecurityState } from './types'
import { StoreRequest } from '@/store/types'

function initMe (): Me {
  const json = localStorage.getItem('me')
  if (json === null) {
    return new Me()
  } else {
    return JSON.parse(json)
  }
}

const state = new SecurityState()
state.apiToken = localStorage.getItem('apiToken')
state.me = initMe()
state.refreshToken = localStorage.getItem('refreshToken')
state.actionRequest = {
  forgottenPasswordSend: new StoreRequest('POST', '/api/password/forgotten', false),
  getMe: new StoreRequest('GET', '/api/me'),
  forgottenPasswordCheckToken: new StoreRequest('GET', '/api/password/forgotten/{token}', false),
  forgottenPasswordReset: new StoreRequest('POST', '/api/password/forgotten/{token}', false),
  login: new StoreRequest('POST', '/api/logincheck', false),
  refreshToken: new StoreRequest('POST', '/api/token/refresh', false)
}
export { state }
