import { httpReq } from '@/helpers/api'
import { ActionContext } from 'vuex'
import { UserState } from '@/store/modules/users/types'
import { RootState } from '@/store/types'

export const actions = {
  getAll ({ commit, state }: ActionContext<UserState, RootState>) {
    httpReq(state.actionRequest.getAll)
      .then(response => {
        commit('setUsers', response.data)
      })
      .catch(() => {
        commit('setUsers')
      })
  }
}
