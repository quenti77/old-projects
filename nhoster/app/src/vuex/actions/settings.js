import fs from 'fs'
import path from 'path'
import * as types from '../mutation-types'

const update = (state, setting, content) => {
    fs.writeFile(setting, JSON.stringify(content), (err) => {
        if (!err) {
            state.commit(types.SET_SETTINGS, content)
        }
    })
}

export const updateSettings = (state, options) => {
    const setting = options.path
    const content = options.content

    if (content == null) {
        if (fs.existsSync(setting)) {
            fs.readFile(setting, 'utf8', (err, data) => {
                if (err) {
                    return false
                }
                update(state, setting, JSON.parse(data))
            })
        } else {
            update(state, setting, {
                folders: {
                    base: path.dirname(setting)
                }
            })
        }
    } else {
        update(state, setting, content)
    }
}

export const generateYml = (state, yml) => {
    const path = yml.file
    const data = yml.content

    let stringYml = "version: '2'\n"
    stringYml += 'services:\n'

    for (let container of data) {
        stringYml += '  ' + container.name + ':\n'
        stringYml += '    image: ' + container.image + '\n'

        if (container.ports !== null) {
            stringYml += '    ports:\n'
            stringYml += '    - "' + container.ports.source +
                ':' + container.ports.dest + '"\n'
        }

        if (container.links !== null) {
            stringYml += '    depends_on:\n'
            for (let link of container.links) {
                stringYml += '      - "' + link
            }
        }
    }

    fs.writeFile(path, stringYml, yml.callback)
}
