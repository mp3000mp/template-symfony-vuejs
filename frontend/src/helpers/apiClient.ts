import axios, { AxiosInstance, AxiosResponse } from 'axios'
import { StoreRequest } from '@/store/types'
import store from '@/store'

interface ApiRequestConfig {
  headers?: {
    [key: string]: string;
  };
  data?: any;
  urlParams?: {
    [key: string]: string;
  };
}

export class ApiClient {
  private axios: AxiosInstance;
  private nbRefreshTokenRetry = 0;

  private static generateUrl (url: string, urlParams: {[key: string]: string}): string {
    const regex = /{(.+?)}/g
    const matches = [...url.matchAll(regex)]
    for (const match of matches) {
      if (typeof urlParams[match[1]] !== 'undefined') {
        url = url.replace(`{${match[1]}}`, urlParams[match[1]])
      }
    }
    return url
  }

  private static getJwtToken (): string {
    return `Bearer ${store.getters['security/getToken']}`
  }

  constructor (baseURL: string) {
    this.axios = axios.create({
      baseURL,
      headers: {
        'content-type': 'application/json'
      }
    })
  }

  /**
   * if response status is different than 2xx, promise will be rejected with an error containing the response
   */
  async httpReq (request: StoreRequest, options: ApiRequestConfig = {}): Promise<AxiosResponse> {
    const config = {
      data: options.data || {},
      headers: options.headers || {},
      method: request.method,
      url: ApiClient.generateUrl(request.url, options.urlParams || {})
    }
    if (request.auth) {
      config.headers.Authorization = ApiClient.getJwtToken()
    }

    console.log(`req start: ${config.url}`)
    request.start()
    try {
      const response = await this.axios.request(config)

      if (!(response.config.url || '').includes('/api/token/refresh')) {
        this.nbRefreshTokenRetry = 0
      }

      console.log(`req ok: ${config.url}`)
      request.end(response.status, response.data.message || '')
      return response
    } catch (err) {
      if (err.response) {
        console.log(`req ${err.response.status}: ${config.url}`)

        // tru refresh token
        if (err.response.status === 401 && err.response.data.message === 'Expired JWT Token' && this.nbRefreshTokenRetry === 0) {
          this.nbRefreshTokenRetry++
          try {
            await store.dispatch('security/refreshLogin')
            config.headers.Authorization = ApiClient.getJwtToken()
            const response = await this.axios.request(config)

            console.log(`req ok: ${config.url}`)
            request.end(response.status, response.data.message || '')
            return response
          } catch (refreshErr) {
            if (err.response) {
              console.log(`req ${err.response.status}: ${config.url}`)
            } else {
              console.log(`refresh token failed: ${config.url}`)
              console.log(err)
            }
            request.end(err.response.status, err.response.data.message || `Unexpected error ${err.response.status}: ${err.response.statusText}`)
            throw err
          }
        }
        request.end(err.response.status, err.response.data.message || `Unexpected error ${err.response.status}: ${err.response.statusText}`)
      } else {
        console.log(`unexpected err: ${config.url}`)
        console.log(err)
        request.end(500, 'Unexpected request error')
      }
      throw err
    }
  }
}
