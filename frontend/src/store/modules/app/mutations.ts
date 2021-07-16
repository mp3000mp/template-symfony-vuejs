import { AppState } from './types'

export const mutations = {
  setVersion (state: AppState, version: string) {
    state.version = version
  }
}
