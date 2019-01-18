<template>
    <div class="options">
        <transition name="fade">
            <container v-if="modal.addContainer" @close="setAddContainer(false)"></container>
        </transition>
        <div class="option-project">
            <label class="option-label" for="projectName">
                Nom :
            </label>
            <input type="text" id="projectName" class="option-field"
                   v-model="projectName">
        </div>
        <div class="option-project">
            <label class="option-label" for="projectDesc">
                Description :
            </label>
            <input type="text" id="projectDesc" class="option-field"
                   v-model="projectDesc">
        </div>
        <div class="option-content">
            <div v-for="(container, index) in project.containers">
                <strong>
                    {{ container.name }}:{{ container.image }}
                </strong>
                <button class="btn btn-danger btn-small"
                        @click.prevent="remove(index)">
                    <i class="fa fa-cross"></i>
                </button>
            </div>
        </div>
        <div class="option-action">
            <button class="btn btn-success header-btn"
                    @click.prevent="setAddContainer(true)">
                <i class="fa fa-plus-circle"></i>
                Ajouter
            </button>
            <button class="btn btn-primary header-btn"
                    @click.prevent="save()">
                <i class="fa fa-floppy-o"></i>
                Sauvegarder
            </button>
            <button class="btn btn-danger header-btn"
                    @click.prevent="gb()">
                <i class="fa fa-cross"></i>
                Annuler
            </button>
        </div>
    </div>
</template>

<script>
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    import Container from 'components/Modals/Container'

    export default {
        store,
        components: {
            Container
        },
        data () {
            return {
                projectName: '',
                projectDesc: '',
                modal: {
                    addContainer: false
                }
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
                'updateProjectInfo',
                'removeProject',
                'startProject',
                'stopProject',
                'generateYml',
                'removeContainer'
            ]),
            gb () {
                this.$router.push('/project/' + this.$route.params.id)
            },
            setAddContainer (value) {
                this.modal.addContainer = value
            },
            save () {
                this.updateProjectInfo({
                    index: this.$route.params.id,
                    name: this.projectName,
                    description: this.projectDesc
                })

                this.update(true)
            },
            remove (index) {
                this.removeContainer(index)
                this.update(false)
            },
            update (redirect) {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')
                const that = this

                that.updateProjects({
                    path: project,
                    content: that.projects
                })

                that.generateYml({
                    file: path.resolve(this.settings.folders.base, this.project.slug, 'docker-compose.yml'),
                    content: this.project.containers,
                    callback () {
                        if (redirect) {
                            that.gb()
                        }
                    }
                })
            }
        },
        mounted () {
            this.projectName = this.project.name
            this.projectDesc = this.project.description
        }
    }
</script>

<style lang="sass">
    /* Nothing */
</style>
