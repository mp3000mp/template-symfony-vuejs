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
  baseUrl: string;
  private axios: AxiosInstance;
  private nbRefreshTokenRetry = 0;

  constructor (baseURL: string) {
    this.baseUrl = baseURL
    this.axios = axios.create({
      baseURL,
      headers: {
        'content-type': 'application/json'
      }
    })
  }

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

  public static generateErrorMessage (err: any): string {
    if (err.response) {
      return err.response.data.message ||
        err.response.data.detail ||
        `Unexpected error ${err.response.status}: ${err.response.statusText}`
      // return msg.replaceAll('\n', '<br />')
    }
    return 'unexpected err'
  }

  private static getJwtToken (): string {
    return `Bearer ${store.getters['security/getToken']}`
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

    console.log(`req start: ${config.method} ${config.url}`)
    request.start()
    try {
      const response = await this.axios.request(config)

      this.nbRefreshTokenRetry = 0
      console.log(`req ok: ${config.method} ${config.url}`)
      request.end(response.status, response.data.message || '')
      return response
    } catch (err) {
      if (err.response) {
        console.log(`req ${err.response.status}: ${config.method} ${config.url}`)

        // tru refresh token
        if (err.response.status === 401 && err.response.data.message === 'Expired JWT Token' && this.nbRefreshTokenRetry === 0) {
          this.nbRefreshTokenRetry++
          try {
            await store.dispatch('security/refreshLogin')
            config.headers.Authorization = ApiClient.getJwtToken()
            const response = await this.axios.request(config)

            this.nbRefreshTokenRetry = 0
            console.log(`req ok: ${config.method} ${config.url}`)
            request.end(response.status, response.data.message || '')
            return response
          } catch (refreshErr) {
            if (refreshErr.response) {
              console.log(`req ${refreshErr.response.status}: ${config.method} ${config.url}`)
            } else {
              console.log(`refresh token failed: ${config.method} ${config.url}`)
              console.log(refreshErr)
            }
            request.end(refreshErr.response.status, ApiClient.generateErrorMessage(refreshErr))
            throw refreshErr
          }
        }
        request.end(err.response.status, ApiClient.generateErrorMessage(err))
      } else {
        console.log(`unexpected err: ${config.method} ${config.url}`)
        console.log(err)
        request.end(500, 'Unexpected request error')
      }
      throw err
    }
  }
}
