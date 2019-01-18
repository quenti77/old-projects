<template>
    <div class="modal">
        <form class="modal-form">
            <label for="folderPath" class="label">
                Dossier de sauvegarde :
            </label>
            <div class="field-btn">
                <input type="text" id="folderPath"
                       v-model="folderPath" class="field">
                <button class="btn btn-primary"
                        @click.prevent="folder()">
                    <i class="fa fa-folder"></i>
                </button>
            </div>
            <div class="btn-group">
                <button class="btn btn-success header-btn"
                        @click.prevent="save()">
                    <i class="fa fa-floppy-o"></i>
                    Sauvegarder
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
                folderPath: '/'
            }
        },
        computed: {
            ...vuex.mapGetters([
                'settings'
            ])
        },
        methods: {
            ...vuex.mapActions([
                'updateSettings'
            ]),
            close () {
                this.$emit('close')
            },
            save () {
                const userData = this.$electron.remote.app.getPath('userData')
                const setting = path.resolve(userData, 'settings.json')

                this.updateSettings({
                    path: setting,
                    content: {
                        folders: {
                            base: this.folderPath
                        }
                    }
                })

                this.close()
            },
            folder () {
                const dialog = this.$electron.remote.dialog
                let that = this

                dialog.showOpenDialog({
                    title: 'Choisir votre répertoire de projet',
                    properties: [
                        'openDirectory',
                        'createDirectory'
                    ]
                }, (folder) => {
                    if (folder.length > 0) {
                        that.folderPath = folder[0]
                    }
                })
            }
        },
        mounted () {
            this.folderPath = this.settings.folders.base
        }
    }
</script>

<style lang="scss">
    /* Nothing */
</style>
