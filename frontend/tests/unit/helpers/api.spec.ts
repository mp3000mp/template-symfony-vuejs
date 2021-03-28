import axios from 'axios'
import api from '@/helpers/apiRegistry'

jest.mock('axios', () => {
  return {
    interceptors: {
      request: { use: jest.fn(), eject: jest.fn() },
      response: { use: jest.fn(), eject: jest.fn() }
    }
  }
})

describe('apiRegistry.ts', () => {
  // todo: not really usefull
  it('should get 200 status', async () => {
    axios.request = jest.fn().mockResolvedValue({ oui: 'oui' })
    const result = await api.httpReq('GET', 'http://mp3000.fr/api/logincheck')

    expect(axios.request).toHaveBeenCalledTimes(1)
    expect(result).toStrictEqual({ oui: 'oui' })
  })
})
