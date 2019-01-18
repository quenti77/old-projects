export default [
    {
        path: '/',
        name: 'default-page',
        component: require('src/components/DefaultPageView')
    },
    {
        path: '/project/:id',
        name: 'project-page',
        component: require('src/components/ProjectPageView')
    },
    {
        path: '/project/:id/setting',
        name: 'option-page',
        component: require('src/components/OptionPageView')
    }
]
