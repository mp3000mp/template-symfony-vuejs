import { UserState } from './types'
import { StoreRequest } from '@/store/types'

const state = new UserState()
state.actionRequest = {
  addUser: new StoreRequest('PUT', '/api/users'),
  deleteUser: new StoreRequest('DELETE', '/api/users/{userId}'),
  enableUser: new StoreRequest('POST', '/api/users/{userId}/enable'),
  disableUser: new StoreRequest('POST', '/api/users/{userId}/disable'),
  getAll: new StoreRequest('GET', '/api/users'),
  updateUser: new StoreRequest('POST', '/api/users/{userId}')
}
export { state }
