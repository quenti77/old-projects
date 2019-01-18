<template>
    <div class="modal">
        <form class="modal-form">
            <transition name="fade">
                <div class="msg msg-danger" v-if="errorMessage"
                     @click="hideError()">
                    {{ errorMessage }}
                </div>
            </transition>

            <label for="containerName" class="label">
                Nom du container :
            </label>
            <input type="text" id="containerName"
                   v-model="container.name" class="field">
            <label for="containerImage" class="label">
                Image du container :
            </label>
            <input type="text" id="containerImage"
                   v-model="container.image" class="field">

            <label for="containerPortStart" class="label">
                Port host source :
            </label>
            <input type="text" id="containerPortStart"
                   v-model="container.ports.source" class="field">
            <label for="containerPortEnd" class="label">
                Port docker de destination :
            </label>
            <input type="text" id="containerPortEnd"
                   v-model="container.ports.dest" class="field">

            <label for="containerLink" class="label">
                Liens à faire avec :
            </label>
            <input type="text" id="containerLink"
                   v-model="container.link" class="field">

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
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    export default {
        store,
        data () {
            return {
                container: {
                    name: 'web',
                    image: 'nginx',
                    ports: {
                        source: 8080,
                        dest: 80
                    },
                    links: null
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
                'addProject',
                'addContainer'
            ]),
            close () {
                this.$emit('close')
            },
            hideError () {
                this.errorMessage = null
            },
            save () {
                const userData = this.$electron.remote.app.getPath('userData')
                const project = path.resolve(userData, 'projects.json')

                if (this.container.ports.source === '') {
                    if (this.container.ports.dest === '') {
                        this.container.ports = null
                    }
                }

                if (this.container.links === '') {
                    this.container.links = null
                }

                this.addContainer({
                    index: this.$route.params.id,
                    content: this.container
                })

                this.updateProjects({
                    path: project,
                    content: this.projects
                })

                this.close()
            }
        }
    }
</script>

<style lang="scss">
    /* Nothing */
</style>
