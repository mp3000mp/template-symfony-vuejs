import { AppState } from './types'

export const getters = {
  getVersion: (state: AppState) => {
    return state.version
  }
}
