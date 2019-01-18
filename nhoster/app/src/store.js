import Vue from 'vue'
import Vuex from 'vuex'

import getters from './vuex/getters'
import actions from './vuex/actions'
import modules from './vuex/modules'

Vue.use(Vuex)

export default new Vuex.Store({
    actions,
    getters,
    modules,
    strict: true
})
