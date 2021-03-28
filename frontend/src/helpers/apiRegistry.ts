import { ApiClient } from '@/helpers/apiClient'

class ApiClientRegistry {
  private registry: {
    [key: string]: ApiClient;
  } = {};

  set (api: string, baseURL: string) {
    this.registry[api] = new ApiClient(baseURL)
  }

  get (api = 'default'): ApiClient {
    return this.registry[api]
  }
}

const apiRegistry = new ApiClientRegistry()
export default apiRegistry
