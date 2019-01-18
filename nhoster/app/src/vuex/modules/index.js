const files = require.context('.', false, /\.js$/)
const modules = {}

files.keys().forEach((key) => {
    if (key === './index.js') {
        return
    }

    let name = key.replace(/(\.\/|\.js)/g, '')
    modules[name] = files(key).default
})

export default modules
