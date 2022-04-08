import axios, { AxiosInstance, AxiosResponse } from 'axios'
import { StoreRequest } from '@/stores/types'
import { useSecurityStore } from '@/stores/security'

const debugMode = false
function debug (msg: string) {
  if (debugMode) {
    console.log(msg)
  }
}

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
  onError: Function;

  constructor (baseURL: string, onError: Function) {
    this.baseUrl = baseURL
    this.onError = onError
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
    const securityStore = useSecurityStore()
    return `Bearer ${securityStore.apiToken}`
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

    debug(`req start: ${config.method} ${config.url}`)
    request.start()
    try {
      const response = await this.axios.request(config)

      this.nbRefreshTokenRetry = 0
      debug(`req ok: ${config.method} ${config.url}`)
      request.end(response.status, response.data.message || '')
      return response
    } catch (err: any) {
      if (err.response) {
        debug(`req ${err.response.status}: ${config.method} ${config.url}`)

        // tru refresh token
        if (err.response.status === 401 && err.response.data.message === 'Expired JWT Token' && this.nbRefreshTokenRetry === 0) {
          this.nbRefreshTokenRetry++
          try {
            const securityStore = useSecurityStore()
            await securityStore.refreshLogin()
            config.headers.Authorization = ApiClient.getJwtToken()
            const response = await this.axios.request(config)

            this.nbRefreshTokenRetry = 0
            debug(`req ok: ${config.method} ${config.url}`)
            request.end(response.status, response.data.message || '')
            return response
          } catch (refreshErr: any) {
            if (refreshErr.response) {
              debug(`req ${refreshErr.response.status}: ${config.method} ${config.url}`)
            } else {
              debug(`refresh token failed: ${config.method} ${config.url}`)
              debug(refreshErr)
            }
            request.end(refreshErr.response.status, ApiClient.generateErrorMessage(refreshErr))
            this.onError(refreshErr.response)
            throw refreshErr
          }
        }
        this.onError(err.response)
        request.end(err.response.status, ApiClient.generateErrorMessage(err))
      } else {
        debug(`unexpected err: ${config.method} ${config.url}`)
        debug(err)
        request.end(500, 'Unexpected request error')
      }
      throw err
    }
  }
}
