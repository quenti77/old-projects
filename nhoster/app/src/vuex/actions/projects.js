import fs from 'fs'
import { spawn } from 'child_process'
import * as types from '../mutation-types'

const update = (state, setting, content) => {
    fs.writeFile(setting, JSON.stringify(content), (err) => {
        if (!err) {
            state.commit(types.SET_PROJECTS, content)
        }
    })
}

const addLog = (state, log) => {
    state.commit(types.ADD_LOG, log)
}

export const clearLog = (state, index) => {
    state.commit(types.CLEAR_LOG, index)
}

export const updateProjects = (state, options) => {
    const project = options.path
    const content = options.content

    if (content == null) {
        if (fs.existsSync(project)) {
            fs.readFile(project, 'utf8', (err, data) => {
                if (err) {
                    return false
                }
                update(state, project, JSON.parse(data))
            })
        } else {
            update(state, project, [])
        }
    } else {
        update(state, project, content)
    }
}

export const addProject = (state, project) => {
    state.commit(types.ADD_PROJECT, project)
}

export const updateProject = (state, project) => {
    state.commit(types.UPDATE_PROJECT, project)
}

export const updateProjectInfo = (state, project) => {
    state.commit(types.UPDATE_PROJECT_INFO, project)
}

export const removeProject = (state, index) => {
    state.commit(types.REMOVE_PROJECT, index)
}

export const startProject = (state, option) => {
    addLog(state, {
        index: option.index,
        type: 'status',
        content: 'Démarrage du projet'
    })

    let child = spawn('docker-compose', ['up'], {
        cwd: option.path
    })
    child.stdout.on('data', (data) => {
        addLog(state, {
            index: option.index,
            type: 'out',
            content: data.toString('utf8')
        })
    })
    child.stderr.on('data', (data) => {
        addLog(state, {
            index: option.index,
            type: 'err',
            content: data.toString('utf8')
        })
    })
    child.on('close', (code) => {
        addLog(state, {
            index: option.index,
            type: (code === 0) ? 'status' : 'end',
            content: (code === 0) ? 'Projet en cours d\'arrêt' : 'Erreur'
        })
    })

    option.callback()
}

export const stopProject = (state, option) => {
    let child = spawn('docker-compose', ['down'], {
        cwd: option.path
    })
    child.stdout.on('data', (data) => {
        addLog(state, {
            index: option.index,
            type: 'out',
            content: data.toString('utf8')
        })
    })
    child.stderr.on('data', (data) => {
        addLog(state, {
            index: option.index,
            type: 'err',
            content: data.toString('utf8')
        })
    })
    child.on('close', (code) => {
        addLog(state, {
            index: option.index,
            type: (code === 0) ? 'status' : 'end',
            content: (code === 0) ? 'Projet arrêté' : 'Erreur'
        })
    })

    option.callback()
}

export const addContainer = (state, container) => {
    state.commit(types.ADD_CONTAINER, container)
}

export const removeContainer = (state, index) => {
    state.commit(types.REMOVE_CONTAINER, index)
}
