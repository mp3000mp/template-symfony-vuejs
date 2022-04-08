import { UserState } from './types'
import { StoreRequest } from '@/stores/types'

const state = new UserState()
state.actionRequest = {
  addUser: new StoreRequest('POST', '/api/users'),
  deleteUser: new StoreRequest('DELETE', '/api/users/{userId}'),
  enableUser: new StoreRequest('PUT', '/api/users/{userId}/enable'),
  disableUser: new StoreRequest('PUT', '/api/users/{userId}/disable'),
  getAll: new StoreRequest('GET', '/api/users'),
  updateUser: new StoreRequest('PUT', '/api/users/{userId}')
}
export { state }
