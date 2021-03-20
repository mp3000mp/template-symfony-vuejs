import { User, UserState } from './types'

export const mutations = {
  setErrorMsg (state: UserState, errorMsg: string|null) {
    state.errorMsg = errorMsg
  },
  setIsLoading (state: UserState, isLoading: boolean) {
    state.isLoading = isLoading
  },
  setUsers (state: UserState, users: User[]) {
    state.users = users
  }
}
