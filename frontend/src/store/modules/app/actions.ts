import apiRegistry from '@/helpers/apiRegistry'
import { ActionContext } from 'vuex'
import { AppState } from './types'
import { RootState } from '@/store/types'
import { ApiClient } from '@/helpers/apiClient'

export const actions = {
  async getInfo ({ commit, state }: ActionContext<AppState, RootState>) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.getInfo)
      commit('setVersion', response.data.version)
    } catch (err) {
      return Promise.reject(ApiClient.generateErrorMessage(err))
    }
  }
}
