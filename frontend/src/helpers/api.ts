import axios, { AxiosResponse, Method } from 'axios'
import store from '../store/index'

interface Headers {
  Authorization?: string;
}

const BASE_URL = 'http://localhost:5000'
const noNeedsAuthUrls = ['/api/logincheck', '/api/token/refresh']
let nbRetry = 0

/**
 * http request
 */
function httpReq (method: Method, url: string, data: object = {}, headers: Headers = {}): Promise<AxiosResponse> {
  const endpoint = `${BASE_URL}${url}`
  return axios.request({
    data,
    headers,
    method,
    url: endpoint
  })
}

/**
 * add bearer if needed
 */
axios.interceptors.request.use(function (config) {
  // console.log(`req: ${config.url}`)
  if (typeof config.url !== 'undefined') {
    if (noNeedsAuthUrls.indexOf(config.url.replace(BASE_URL, '')) === -1) {
      config.headers.Authorization = `Bearer ${store.getters['security/getToken']}`
    }
  }
  return config
}, function (err) {
  console.log(err)
  return Promise.reject(err)
})

/**
 * retry if not connected or expired
 */
axios.interceptors.response.use(function (response) {
  // console.log(`res ok: ${response.config.url}`)
  nbRetry = 0
  return response
}, function (err) {
  // console.log(`res err: ${err.config.url}`)
  if (err.response.status !== 401 || nbRetry > 0 || err.response.data.message === 'Invalid credentials.') {
    console.log(err)
    return Promise.reject(err)
  }
  nbRetry++
  return store.dispatch('security/refreshLogin', { username: 'mp3000', password: 'Test2000!' })
    .then(function () {
      return axios.request(err.config)
    })
    .catch(function (err2) {
      return Promise.reject(err2)
    })
})

export { httpReq }
