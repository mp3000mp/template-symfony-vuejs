import axios from 'axios'
import { ApiClient } from '@/helpers/apiClient'
import { StoreRequest } from '@/stores/types'
import { createApp } from 'vue'
import { createTestingPinia } from '@pinia/testing'
import { useSecurityStore } from '@/stores/security'
import { setActivePinia } from 'pinia'

// todo test état StoreRequest après les requêtes

const onErrorMock = jest.fn()
let apiClient = new ApiClient('', onErrorMock)

// mock security store
const pinia = createTestingPinia()
createApp({}).use(pinia)
setActivePinia(pinia)
const mockedStore = useSecurityStore()
mockedStore.apiToken = 'apiToken'

// mock axios
const mockedAxios = axios as jest.Mocked<typeof axios>
jest.mock('axios')
mockedAxios.create = jest.fn(() => mockedAxios)

describe('apiRegistry.ts', () => {
  beforeEach(() => {
    jest.clearAllMocks()
  })

  it('should get 200 status', async () => {
    mockedAxios.request.mockImplementationOnce(() =>
      Promise.resolve({ data: { message: 'ok' }, status: 200 })
    )

    apiClient = new ApiClient('http://mp3000.fr', onErrorMock)
    const storeRequest = new StoreRequest('GET', '/api/test-200', false)
    const result = await apiClient.httpReq(storeRequest)

    expect(axios.request).toHaveBeenCalledTimes(1)
    expect(result).toStrictEqual({ data: { message: 'ok' }, status: 200 })
  })

  it('should add bearer', async () => {
    const url = '/api/test-bearer'
    mockedAxios.request.mockImplementationOnce(() =>
      Promise.resolve({ data: { message: 'ok' }, status: 200 })
    )

    apiClient = new ApiClient('http://mp3000.fr', onErrorMock)
    const storeRequest = new StoreRequest('GET', url, true)
    const result = await apiClient.httpReq(storeRequest)

    expect(axios.request).toHaveBeenCalledTimes(1)
    expect(axios.request).toHaveBeenCalledWith({
      data: {},
      headers: {
        Authorization: 'Bearer apiToken'
      },
      method: 'GET',
      url: url
    })
    expect(result).toStrictEqual({ data: { message: 'ok' }, status: 200 })
  })

  it('should refresh token and ok', async () => {
    let i = 0
    const responseData = [
      { data: { message: 'Expired JWT Token' }, status: 401 },
      { data: { message: 'ok' }, status: 200 }
    ]
    mockedAxios.request.mockImplementation(() => {
      i++
      if (i - 1 === 0) {
        // eslint-disable-next-line prefer-promise-reject-errors
        return Promise.reject({ response: responseData[i - 1] })
      } else {
        return Promise.resolve(responseData[i - 1])
      }
    })

    apiClient = new ApiClient('http://mp3000.fr', onErrorMock)
    const storeRequest = new StoreRequest('GET', '/api/test-refresh-200', true)
    const result = await apiClient.httpReq(storeRequest)

    expect(axios.request).toHaveBeenCalledTimes(2)
    expect(mockedStore.refreshLogin).toHaveBeenCalledTimes(1)
    expect(result).toStrictEqual({ data: { message: 'ok' }, status: 200 })
  })

  it('should try refresh token once', async () => {
    const responseData = { data: { message: 'Expired JWT Token' }, status: 401 }

    mockedAxios.request.mockImplementation(() => {
      // eslint-disable-next-line prefer-promise-reject-errors
      return Promise.reject({ response: responseData })
    })

    apiClient = new ApiClient('http://mp3000.fr', onErrorMock)
    const storeRequest = new StoreRequest('GET', '/api/test-refresh-once', true)
    await expect(apiClient.httpReq(storeRequest)).rejects.toHaveProperty('response', { data: { message: 'Expired JWT Token' }, status: 401 })

    expect(axios.request).toHaveBeenCalledTimes(2)
    expect(mockedStore.refreshLogin).toHaveBeenCalledTimes(1)
  })
})
