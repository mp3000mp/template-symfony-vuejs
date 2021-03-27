import axios, { AxiosResponse } from 'axios'
import store from '../store/index'
import { StoreRequest } from '@/store/types'

interface ApiRequestConfig {
  headers?: any;
  data?: object;
  urlParams?: {
    [key: string]: string;
  };
}

// todo variable d'environnement + instance api
const BASE_URL = 'http://localhost:5000'
let nbRetry = 0

function generateUrl (url: string, urlParams: any) {
  const regex = /{(.+?)}/g
  const matches = [...url.matchAll(regex)]
  for (const match of matches) {
    url = url.replace(`{${match[1]}}`, urlParams[match[1]])
  }
  return url
}

/**
 * http request
 */
function httpReq (request: StoreRequest, options: ApiRequestConfig = {}): Promise<AxiosResponse> {
  const endpoint = `${BASE_URL}${request.url}`
  const config = {
    data: options.data,
    headers: options.headers || {},
    method: request.method,
    url: generateUrl(endpoint, options.urlParams)
  }
  if (request.auth) {
    config.headers.Authorization = `Bearer ${store.getters['security/getToken']}`
  }

  // console.log(`req start: ${config.url}`)
  request.start()
  return axios.request(config)
    .then(response => {
      // console.log(`req ok: ${config.url}`)
      request.end(response.status, response.data.message || '')
      return response
    })
    .catch(err => {
      console.log(`req err: ${config.url}`)
      console.log(err)
      request.end(err.response.status, err.response.data.message)
      return err
    })
}

/**
 * retry if not connected or expired
 */
axios.interceptors.response.use(response => {
  // console.log(`intercept ok: ${response.config.url}`)
  // todo trouver mieux
  if (!(response.config.url || '').includes('/api/token/refresh')) {
    nbRetry = 0
  }
  return response
}, err => {
  // console.log(`intercept err: ${err.config.url}`)
  // if axios error, we set data similar to response for action to be able to handle this
  if (!err.response) {
    console.log(err)
    err.response = {
      data: {
        message: err.message
      },
      status: 0
    }
  }
  if (err.response.status === 401 && err.response.data.message === 'Expired JWT Token' && nbRetry === 0) {
    nbRetry++
    return store.dispatch('security/refreshLogin')
      .then(() => {
        // console.log(`refresh ok: ${err.response.config.url}`)
        err.config.headers.Authorization = `Bearer ${store.getters['security/getToken']}`
        return axios.request(err.config)
          .then(res => {
            // console.log('then chance 2')
            return Promise.resolve(res)
          })
          .catch(err => {
            // console.log('err chance 2')
            return Promise.reject(err)
          })
      })
      .catch(err2 => {
        // console.log(`refresh err: ${err.response.config.url}`)
        return Promise.reject(err2)
      })
  } else {
    // console.log(`err: ${err.response.config.url}`)
    return Promise.reject(err)
  }
})

export { httpReq }

export default {
  httpReq
}
