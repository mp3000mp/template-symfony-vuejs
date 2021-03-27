import { User, UserState } from './types'

export const mutations = {
  setUsers (state: UserState, users: User[] = []) {
    state.users = users
  }
}
