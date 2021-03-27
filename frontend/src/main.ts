import { createApp } from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faHandSparkles, faMagic } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import './registerServiceWorker'
import router from './router'
import store from './store'

import './assets/css/app.scss'

// import { Tooltip, Toast, Popover } from 'bootstrap'

library.add(faHandSparkles, faMagic)

createApp(App)
  .component('font-aw', FontAwesomeIcon)
  .use(store)
  .use(router)
  .mount('#app')
