import Vue from 'vue'
import Electron from 'vue-electron'
import Router from 'vue-router'
import Resource from 'vue-resource'

import App from './App'
import routes from './routes'

import 'font-awesome/css/font-awesome.min.css'

Vue.use(Electron)
Vue.use(Router)
Vue.use(Resource)

Vue.config.debug = true

Vue.http.options.root = 'http://local.nhoster'
Vue.http.options.emulateJSON = true

const router = new Router({
    scrollBehavior: () => ({ y: 0 }),
    routes
})

/* eslint-disable no-new */
new Vue({
    router,
    ...App
}).$mount('#app')
