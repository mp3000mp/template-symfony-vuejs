import { User, UserState } from './types'

export const mutations = {
  addUser (state: UserState, user: User) {
    state.users.push(user)
  },
  deleteUser (state: UserState, userId: number) {
    const i = state.users.findIndex(item => item.id === userId)
    if (i > -1) {
      state.users.splice(i, 1)
    }
  },
  setUsers (state: UserState, users: User[] = []) {
    state.users = users
  },
  updateUser (state: UserState, user: User) {
    const toBeUpdated = state.users.find(item => item.id === user.id)
    Object.assign(toBeUpdated, user)
  }
}
