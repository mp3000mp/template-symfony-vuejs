import apiRegistry from '@/helpers/apiRegistry'
import { ActionContext } from 'vuex'
import { UserState } from '@/store/modules/users/types'
import { RootState } from '@/store/types'
import { ApiClient } from '@/helpers/apiClient'

interface AddUserPayload {
  email: string;
  isEnabled: boolean;
  roles: string[];
  username: string;
}
interface UpdateUserPayload extends AddUserPayload {
  id: number;
  email: string;
  roles: string[];
  username: string;
}

export const actions = {
  async addUser ({ commit, state }: ActionContext<UserState, RootState>, data: AddUserPayload) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.addUser, { data })
      commit('addUser', response.data)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async enableUser ({ commit, state }: ActionContext<UserState, RootState>, userId: number) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.enableUser, { urlParams: { userId: `${userId}` } })
      commit('updateUser', response.data)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async deleteUser ({ commit, state }: ActionContext<UserState, RootState>, userId: number) {
    try {
      await apiRegistry.get().httpReq(state.actionRequest.deleteUser, { urlParams: { userId: `${userId}` } })
      commit('deleteUser', userId)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async disableUser ({ commit, state }: ActionContext<UserState, RootState>, userId: number) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.disableUser, { urlParams: { userId: `${userId}` } })
      commit('updateUser', response.data)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async getAll ({ commit, state }: ActionContext<UserState, RootState>) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.getAll)
      commit('setUsers', response.data)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  },
  async updateUser ({ commit, state }: ActionContext<UserState, RootState>, data: UpdateUserPayload) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.updateUser, { data, urlParams: { userId: `${data.id}` } })
      commit('updateUser', response.data)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  }
}
