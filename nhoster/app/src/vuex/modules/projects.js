import * as types from '../mutation-types'

const state = {
    current: []
}

const mutations = {
    [types.SET_PROJECTS] (state, projects) {
        state.current = projects
    },
    [types.ADD_PROJECT] (state, project) {
        state.current.push(project)
    },
    [types.UPDATE_PROJECT] (state, project) {
        let type = 'project-primary'
        if (project.action === 1) {
            type = 'project-success'
        } else if (project.action === 2) {
            type = 'project-danger'
        }

        state.current[project.index].type = type
    },
    [types.UPDATE_PROJECT_INFO] (state, project) {
        state.current[project.index].name = project.name
        state.current[project.index].description = project.description
    },
    [types.REMOVE_PROJECT] (state, index) {
        state.current.splice(index, 1)
    },
    [types.ADD_LOG] (state, log) {
        state.current[log.index].log.push({
            type: log.type,
            content: log.content
        })
    },
    [types.CLEAR_LOG] (state, index) {
        state.current[index].log.splice(0)
    },
    [types.ADD_CONTAINER] (state, container) {
        state.current[container.index].containers.push(container.content)
    },
    [types.REMOVE_CONTAINER] (state, index) {
        state.current[index].containers.splice(index, 1)
    }
}

export default {
    state,
    mutations
}
