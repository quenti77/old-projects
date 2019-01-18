<template>
    <div class="projects">
        <div class="project" :class="project.type"
             v-for="(project, index) in projects"
             @contextmenu.prevent="contextProject(index, project)">
            <h3 class="project-title">
                {{ project.name }}
            </h3>
            <p class="project-content">
                {{ project.description.substr(0, 50) }}
            </p>
            <div class="project-actions">
                <button class="btn btn-primary header-btn"
                        @click.prevent="goProject(index)">
                    Voir
                    <i class="fa fa-eye"></i>
                </button>
                <button class="btn btn-danger header-btn"
                        @click="remove(index)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import fs from 'fs-extra'
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    export default {
        store,
        computed: {
            ...vuex.mapGetters([
                'settings',
                'projects'
            ])
        },
        methods: {
            ...vuex.mapActions([
                'updateProjects',
                'updateProject',
                'removeProject',
                'startProject',
                'stopProject'
            ]),
            goProject (index) {
                this.$router.push('/project/' + index)
            },
            remove (index) {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')
                const that = this

                let cb = (index) => {
                    fs.remove(path.resolve(that.settings.folders.base, that.projects[index].slug), (e) => {
                        that.removeProject(index)
                        that.updateProjects({
                            path: project,
                            content: that.projects
                        })
                    })
                }

                if (that.projects[index].type === 'project-danger') {
                    cb(index)
                } else {
                    that.stop(index, cb)
                }
            },
            update (index, action) {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')

                this.updateProject({
                    index,
                    action
                })
                this.updateProjects({
                    path: project,
                    content: this.projects
                })
            },
            contextProject (index, project) {
                const remote = this.$electron.remote
                const menu = new remote.Menu()
                const that = this

                // Ouverture du projet
                menu.append(new remote.MenuItem({
                    label: 'Visualiser',
                    click () {
                        that.goProject(index)
                    }
                }))

                menu.append(new remote.MenuItem({
                    type: 'separator'
                }))

                // Action des containers
                if (project.type !== 'project-success') {
                    menu.append(new remote.MenuItem({
                        label: 'Démarrer',
                        click () {
                            that.start(index)
                        }
                    }))
                }

                if (project.type !== 'project-danger') {
                    menu.append(new remote.MenuItem({
                        label: 'Arrêter',
                        click () {
                            that.stop(index)
                        }
                    }))
                }

                menu.append(new remote.MenuItem({
                    type: 'separator'
                }))

                // Autre actions
                menu.append(new remote.MenuItem({
                    label: 'Supprimer ',
                    click () {
                        that.remove(index)
                    }
                }))

                menu.popup(remote.getCurrentWindow())
            },
            start (index) {
                const that = this
                this.startProject({
                    index,
                    path: path.resolve(this.settings.folders.base, this.projects[index].slug),
                    callback () {
                        that.update(index, 1)
                    }
                })
            },
            stop (index, cb) {
                const that = this
                this.stopProject({
                    index,
                    path: path.resolve(this.settings.folders.base, this.projects[index].slug),
                    callback () {
                        that.update(index, 2)
                        if (cb) {
                            cb(index)
                        }
                    }
                })
            }
        }
    }
</script>

<style lang="sass">
    /* Nothing */
</style>
