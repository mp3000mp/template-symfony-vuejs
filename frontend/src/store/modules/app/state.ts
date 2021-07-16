import { AppState } from './types'
import { StoreRequest } from '@/store/types'

const state = new AppState()
state.actionRequest = {
  getInfo: new StoreRequest('GET', '/api/info')
}
export { state }
