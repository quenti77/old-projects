const files = require.context('.', false, /\.js$/)
const actions = {}

files.keys().forEach((key) => {
    if (key === './index.js') {
        return
    }

    let file = files(key)
    for (let val in file) {
        if (file.hasOwnProperty(val)) {
            actions[val] = file[val]
        }
    }
})

export default actions
