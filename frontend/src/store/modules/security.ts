interface SecurityState {
  apiToken: string;
  isAuthenticating: boolean;
}

const module = {
  state: {
    apiToken: null,
    isAuthenticating: true
  },
  getters: {
    isAuthenticated: (state: SecurityState) => {
      return state.apiToken != null
    }
  },
  mutations: {
  },
  actions: {
  },
  namespaced: true
}

export default module
