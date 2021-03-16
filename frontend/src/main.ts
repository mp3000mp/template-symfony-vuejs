import { createApp } from 'vue'
import App from './App.vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faDragon, faHandSparkles, faHatWizard, faMagic } from '@fortawesome/free-solid-svg-icons'
// import {  } from '@fortawesome/free-brands-svg-icons'
// import { faHandLizard } from '@fortawesome/free-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import './registerServiceWorker'
import router from './router'
import store from './store'

import './assets/css/app.scss'

library.add(faDragon, faHandSparkles, faHatWizard, faMagic)

createApp(App)
  .component('font-aw', FontAwesomeIcon)
  .use(store)
  .use(router)
  .use(VueAxios, axios)
  .mount('#app')
