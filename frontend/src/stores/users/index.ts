import { defineStore } from 'pinia'
import { state } from './state'
import apiRegistry from '@/helpers/apiRegistry'
import { ApiClient } from '@/helpers/apiClient'
import { User } from '@/stores/users/types'

interface AddUserPayload {
  email: string;
  isEnabled: boolean;
  roles: string[];
  username: string;
}
interface UpdateUserPayload extends AddUserPayload {
  id: number;
}

// todo: refactor array functions in utils
function deleteUser (users: User[], userId: number) {
  const i = users.findIndex(item => item.id === userId)
  if (i > -1) {
    users.splice(i, 1)
  }
}
function updateUser (users: User[], user: User) {
  const toBeUpdated = users.find(item => item.id === user.id)
  Object.assign(toBeUpdated, user)
}

// todo: define actions in separate file when thie issue is closed: https://github.com/vuejs/pinia/issues/802
export const useUsersStore = defineStore('users', {
  state: () => state,
  actions: {
    async addUser (data: AddUserPayload) {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.addUser, { data })
        this.users.push(response.data)
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async enableUser (userId: number) {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.enableUser, { urlParams: { userId: `${userId}` } })
        updateUser(this.users, response.data)
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async deleteUser (userId: number) {
      try {
        await apiRegistry.get().httpReq(this.actionRequest.deleteUser, { urlParams: { userId: `${userId}` } })
        deleteUser(this.users, userId)
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async disableUser (userId: number) {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.disableUser, { urlParams: { userId: `${userId}` } })
        updateUser(this.users, response.data)
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async getAll () {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.getAll)
        this.users = response.data
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    },
    async updateUser (data: UpdateUserPayload) {
      try {
        const response = await apiRegistry.get().httpReq(this.actionRequest.updateUser, { data, urlParams: { userId: `${data.id}` } })
        updateUser(this.users, response.data)
      } catch (err) {
        return Promise.reject(ApiClient.generateErrorMessage(err))
      }
    }
  }
})
