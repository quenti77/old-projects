import Vue from 'vue'
import * as types from '../mutation-types'

export const authenticateUser = (state, options) => {
    if (options === null) {
        state.commit(types.AUTH_USER, null)
        return false
    }

    Vue.http.get('auth', {
        headers: options.user
    }).then((response) => {
        state.commit(types.AUTH_USER, response.body.user)
        options.callback(response.body)
    }, (response) => {
        options.callback(response.body)
    })
}
