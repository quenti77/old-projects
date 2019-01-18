import * as types from '../mutation-types'

const state = {
    current: null
}

const mutations = {
    [types.SET_SETTINGS] (state, settings) {
        state.current = settings
    }
}

export default {
    state,
    mutations
}
