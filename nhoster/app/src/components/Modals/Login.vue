<template>
    <div class="modal">
        <form class="modal-form">
            <transition name="fade">
                <div class="msg msg-danger" v-if="errorMessage"
                     @click="hideError()">
                    {{ errorMessage }}
                </div>
            </transition>

            <label for="name" class="label">
                Votre nom :
            </label>
            <input type="text" class="field" id="name" v-model="name">
            <label for="pass" class="label">
                Votre mot de passe :
            </label>
            <input type="password" class="field" id="pass" v-model="pass">
            <div class="btn-group">
                <button class="btn btn-success header-btn"
                        @click.prevent="login()">
                    <i class="fa fa-paper-plane"></i>
                    Se connecter
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
    import vuex from 'vuex'
    import store from 'src/store'

    export default {
        store,
        data () {
            return {
                errorMessage: null
            }
        },
        methods: {
            ...vuex.mapActions([
                'authenticateUser'
            ]),
            login () {
                let that = this
                this.authenticateUser({
                    user: {
                        name: this.name,
                        pass: this.pass
                    },
                    callback (result) {
                        that.errorMessage = result.message
                        if (result.success) {
                            that.close()
                        }
                    }
                })

                this.pass = ''
            },
            close () {
                this.$emit('close')
            },
            hideError () {
                this.errorMessage = null
            }
        }
    }
</script>

<style lang="scss">
    /* Nothing */
</style>
