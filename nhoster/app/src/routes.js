import routeDefault from './routes/default'

export default [
    ...routeDefault,
    {
        path: '*',
        redirect: '/'
    }
]
