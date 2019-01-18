<template>
    <div class="projects">
        <div class="project-info" :class="project.type">
            <h3 class="project-title">
                {{ project.name }}
            </h3>
            <p>
                {{ project.description }}
            </p>
            <div class="project-log">
                <div v-for="log in project.log"
                    :class="{primary: log.type == 'status',warning: log.type == 'end', danger: log.type == 'err', success: log.type == 'out'}">
                    <strong>{{ log.type }}</strong> :
                    {{ log.content }}
                </div>
            </div>
            <div class="project-actions">
                <button class="btn btn-success header-btn"
                        @click.prevent="start()"
                        v-if="project.type === 'project-danger'">
                    <i class="fa fa-play"></i>
                    Démarrer
                </button>
                <button class="btn btn-danger header-btn"
                        @click.prevent="stop()"
                        v-if="project.type === 'project-success'">
                    <i class="fa fa-stop"></i>
                    Arrêter
                </button>
                <button class="btn btn-warning header-btn"
                        @click.prevent="clearLogs()">
                    <i class="fa fa-trash"></i>
                    Vider les logs
                </button>
                <button class="btn btn-warning header-btn"
                        @click.prevent="goOption()"
                        v-if="project.type === 'project-danger'">
                    <i class="fa fa-cog can-rotate"></i>
                    Options
                </button>
                <button class="btn btn-primary header-btn"
                        @click.prevent="gb()">
                    <i class="fa fa-arrow-left"></i>
                    Revenir
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    export default {
        store,
        data () {
            return {
                log: ''
            }
        },
        computed: {
            ...vuex.mapGetters([
                'settings',
                'projects'
            ]),
            project () {
                return this.projects[this.$route.params.id]
            }
        },
        methods: {
            ...vuex.mapActions([
                'updateProjects',
                'updateProject',
                'removeProject',
                'startProject',
                'stopProject',
                'clearLog'
            ]),
            gb () {
                this.$router.push('/')
            },
            goOption () {
                this.$router.push(`/project/${this.$route.params.id}/setting`)
            },
            remove () {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')

                this.removeProject(this.$route.params.id)
                this.updateProjects({
                    path: project,
                    content: this.projects
                })
                this.gb()
            },
            update (action) {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')

                this.updateProject({
                    index: this.$route.params.id,
                    action
                })
                this.updateProjects({
                    path: project,
                    content: this.projects
                })
            },
            start () {
                const that = this
                that.startProject({
                    index: that.$route.params.id,
                    path: path.resolve(that.settings.folders.base, that.project.slug),
                    callback () {
                        that.update(1)
                    }
                })
            },
            stop () {
                const that = this
                that.stopProject({
                    index: that.$route.params.id,
                    path: path.resolve(that.settings.folders.base, that.project.slug),
                    callback () {
                        that.update(2)
                    }
                })
            },
            clearLogs () {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')

                this.clearLog(this.$route.params.id)
                this.updateProjects({
                    path: project,
                    content: this.projects
                })
            }
        }
    }
</script>

<style lang="sass">
    /* Nothing */
</style>
