import axios, { AxiosInstance } from 'axios'
import { ApiClient } from '@/helpers/apiClient'
import { StoreRequest } from '@/store/types'

describe('apiRegistry.ts', () => {
  it('should get 200 status', async () => {
    // todo pourquoi this.axios.request is not a function après try dans ApiClient ?
    const axiosInstance = jest.fn(() => ({
      request: jest.fn()
    }))
    axios.create = jest.fn().mockResolvedValue(axiosInstance)

    const apiclient = new ApiClient('http://mp3000.fr')
    const storeRequest = new StoreRequest('GET', '/api/test', false)
    const result = await apiclient.httpReq(storeRequest)

    expect(axios.create).toHaveBeenCalledTimes(1)
    expect(result).toStrictEqual({ oui: 'oui' })
  })
})
