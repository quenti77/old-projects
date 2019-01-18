<template>
    <div class="modal">
        <form class="modal-form">
            <transition name="fade">
                <div class="msg msg-danger" v-if="errorMessage"
                     @click="hideError()">
                    {{ errorMessage }}
                </div>
            </transition>

            <label for="projectName" class="label">
                Nom du projet :
            </label>
            <input type="text" id="projectName"
                   v-model="project.name" class="field">
            <label for="projectSlug" class="label">
                Nom du dossier (minuscule et - seulement) :
            </label>
            <input type="text" id="projectSlug"
                   v-model="project.slug" class="field">
            <label for="projectDescription" class="label">
                Description :
            </label>
            <input type="text" id="projectDescription"
                   v-model="project.description" class="field">

            <div class="btn-group">
                <button class="btn btn-success header-btn"
                        @click.prevent="save()">
                    <i class="fa fa-plus"></i>
                    Ajouter
                </button>
                <button class="btn btn-danger header-btn"
                        @click.prevent="close()">
                    <i class="fa fa-times"></i>
                    Annuler
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import fs from 'fs'
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    export default {
        store,
        data () {
            return {
                project: {
                    name: 'My Project',
                    slug: 'my-project',
                    description: 'Un projet de test de l\'application',
                    type: 'project-danger',
                    log: [],
                    containers: []
                },
                errorMessage: null
            }
        },
        computed: {
            ...vuex.mapGetters([
                'settings',
                'projects'
            ])
        },
        methods: {
            ...vuex.mapActions([
                'updateSettings',
                'updateProjects',
                'addProject'
            ]),
            close () {
                this.$emit('close')
            },
            hideError () {
                this.errorMessage = null
            },
            save () {
                const userData = this.$electron.remote.app.getPath('userData')
                const setting = path.resolve(userData, 'projects.json')
                const that = this

                const regex = /^[a-z]([a-z0-9\-]*)$/g
                if (this.project.slug.match(regex) === null) {
                    this.errorMessage = 'Le nom du dossier n\'est pas correct'
                    return false
                }

                fs.mkdir(
                    path.resolve(
                        this.settings.folders.base,
                        this.project.slug),
                    (e) => {
                        if (e !== null) {
                            that.errorMessage = `Le projet "${that.project.slug}" existe déjà !`
                            return false
                        }
                        that.addProject(that.project)
                        that.updateProjects({
                            path: setting,
                            content: that.projects
                        })

                        that.close()
                    })
            }
        }
    }
</script>

<style lang="scss">
    /* Nothing */
</style>
