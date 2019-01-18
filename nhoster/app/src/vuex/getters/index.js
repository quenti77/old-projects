const files = require.context('.', false, /\.js$/)
const getters = {}

files.keys().forEach((key) => {
    if (key === './index.js') {
        return
    }

    let file = files(key)
    for (let val in file) {
        if (file.hasOwnProperty(val)) {
            getters[val] = file[val]
        }
    }
})

export default getters
