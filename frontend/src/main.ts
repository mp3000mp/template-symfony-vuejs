import { createApp } from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faHandSparkles, faMagic, faUserCheck, faUserTimes } from '@fortawesome/free-solid-svg-icons'
import { faEdit, faTrashAlt } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import './registerServiceWorker'
import router from './router'
import store from './store'
import variables from '../config/variables.json'

import apiRegistry from './helpers/apiRegistry'

import './assets/css/app.scss'
import { AxiosResponse } from 'axios'
// import { Tooltip, Toast, Popover } from 'bootstrap'

apiRegistry.set('default', variables.URL, (error: AxiosResponse) => {
  if (error.status === 401 || error.status === 403) {
    console.log('redirect login')
    router.push({ path: '/login' })
  }
})

library.add(faHandSparkles, faMagic, faUserCheck, faUserTimes, faTrashAlt, faEdit)

createApp(App)
  .component('font-aw', FontAwesomeIcon)
  .use(store)
  .use(router)
  .mount('#app')
