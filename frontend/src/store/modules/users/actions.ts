import apiRegistry from '@/helpers/apiRegistry'
import { ActionContext } from 'vuex'
import { UserState } from '@/store/modules/users/types'
import { RootState } from '@/store/types'

export const actions = {
  async getAll ({ commit, state }: ActionContext<UserState, RootState>) {
    try {
      const response = await apiRegistry.get().httpReq(state.actionRequest.getAll)
      commit('setUsers', response.data)
    } catch (e) {

    }
  }
}
