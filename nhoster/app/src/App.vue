<template>
    <div class="container">
        <transition name="fade">
            <login v-if="modal.login" @close="setModal(false)"></login>
        </transition>
        <transition name="fade">
            <setting v-if="modal.setting" @close="setSetting(false)"></setting>
        </transition>
        <transition name="fade">
            <add v-if="modal.addProject" @close="setAddProject(false)"></add>
        </transition>
        <header class="header">
            <button class="btn btn-success header-btn"
                    @click.prevent="setAddProject(true)">
                <i class="fa fa-plus-circle"></i>
                Ajouter
            </button>

            <h1 class="header-title">
                <button class="btn btn-success header-btn"
                        v-if="authUser === null"
                        @click="setModal(true)">
                    Se connecter
                    <i class="fa fa-sign-in"></i>
                </button>
                <button class="btn btn-success header-btn"
                        v-if="authUser"
                        @click="authenticateUser(null)">
                    {{ authUser.name }}
                    <i class="fa fa-sign-out"></i>
                </button>
            </h1>

            <button class="btn btn-warning header-btn"
                         @click.prevent="setSetting(true)">
                <i class="fa fa-cog can-rotate"></i>
                Paramètres
            </button>
        </header>
        <router-view class="content">
            <!-- Nothing -->
        </router-view>
        <footer class="footer">
            Vous avez <strong>{{ projects.length }}</strong> projets
        </footer>
    </div>
</template>

<script>
    import path from 'path'
    import vuex from 'vuex'
    import store from 'src/store'

    import Login from 'components/Modals/Login'
    import Setting from 'components/Modals/Setting'
    import Add from 'components/Modals/Add'

    export default {
        store,
        components: {
            Login,
            Setting,
            Add
        },
        data () {
            /**
             * boolean: est-ce que la modal est affiché
             **/
            return {
                modal: {
                    login: false,
                    setting: false,
                    addProject: false
                }
            }
        },
        computed: {
            ...vuex.mapGetters([
                'authUser',
                'projects'
            ])
        },
        methods: {
            ...vuex.mapActions([
                'authenticateUser',
                'updateSettings',
                'updateProjects'
            ]),
            setModal (value) {
                this.modal.login = value
            },
            setSetting (value) {
                this.modal.setting = value
            },
            setAddProject (value) {
                this.modal.addProject = value
            }
        },
        mounted () {
            // Récupération au démarrage de l'app (création du composant)
            // des différentes données que l'on passe au store
            const userData = this.$electron.remote.app.getPath('userData')
            const setting = path.resolve(userData, 'settings.json')
            const project = path.resolve(userData, 'projects.json')

            this.updateSettings({
                path: setting,
                content: null
            })

            this.updateProjects({
                path: project,
                content: null
            })
        }
    }
</script>

<style lang="scss" src="./styles/app.scss">
    /* Nothing */
</style>
