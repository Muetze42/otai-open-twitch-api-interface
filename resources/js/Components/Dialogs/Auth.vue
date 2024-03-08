<template>
    <Modal :show="true" title="Twitch Authentication" @close="this.$emit('close')">
        <form class="flex flex-col" @submit.prevent="submit">
            <div class="overflow-y-auto max-h-[70vh] pt-2 px-4" scroll-region>
                <p class="text-lg font-semibold mb-4">
                    Select needed scopes for your (new) <a href="https://dev.twitch.tv/docs/authentication/#user-access-tokens" target="_blank">Twitch User Access Token</a>.
                </p>
                <div>
                    <div class="flex flex-warp gap-2 justify-between items-center h2border">
                        <h2 class="border-b-0 inline-block">Twitch API scopes</h2>
                        <label>
                            <input type="checkbox" class="form-checkbox" v-model="checkAll"> Check/Uncheck all
                        </label>
                    </div>
                    <div class="flex flex-col divide-y divide-primary-800">
                        <div v-for="scope in $page.props.config.scopes.api" class="py-2">
                            <label>
                                <input type="checkbox" v-model="form.apiScopes" class="form-checkbox" :value="scope.id">
                                <code>{{ scope.id }}</code>
                            </label>
                            <div class="pl-6" v-html="scope.description" />
                            <div class="pl-6" v-if="scope.endpoints.length">
                                Endpoints:
                                <template v-for="(endpoint, index) in scope.endpoints">
                                    <template v-if="index != 0">, </template>
                                    <a :href="'https://dev.twitch.tv/docs/api/reference/#'+endpoint.slug" target="_blank">
                                        {{ endpoint.name }}
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex flex-warp gap-2 justify-between items-center h2border">
                        <h2 class="border-b-0 inline-block">Chat and PubSub scopes</h2>
                        <label>
                            <input type="checkbox" class="form-checkbox" v-model="checkAllChat"> Check/Uncheck all
                        </label>
                    </div>
                    <div class="flex flex-col divide-y divide-primary-800">
                        <div v-for="scope in $page.props.config.scopes.chat" class="py-2">
                            <label>
                                <input type="checkbox" v-model="form.chatScopes" class="form-checkbox" :value="scope.id">
                                <code>{{ scope.id }}</code>
                            </label>
                            <div class="pl-6" v-html="scope.description" />
                            <div class="pl-6" v-if="scope.endpoints.length">
                                Endpoints:
                                <template v-for="(endpoint, index) in scope.endpoints">
                                    <template v-if="index != 0">, </template>
                                    <a :href="'https://dev.twitch.tv/docs/api/reference/#'+endpoint.slug" target="_blank">
                                        {{ endpoint.name }}
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block text-center flex flex-col gap-4 py-4 border-t border-gray-500">
                <label>
                    <input type="checkbox" v-model="form.remember" class="form-checkbox" :value="true">
                    Remember me
                </label>
                <label>
                    <input type="checkbox" v-model="form.privacy" class="form-checkbox" :value="true" required>
                    Accept <a href="https://huth.it/privacy" target="_blank">Privacy Policy</a>
                </label>
                <div>
                    <button type="submit" class="btn text-lg pb-0.5 px-2" :disabled="form.processing || !form.privacy">
                        <FontAwesomeIcon icon="fa-brands fa-twitch" fixed-width />
                        Authenticate
                    </button>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script>
import {router} from "@inertiajs/vue3";

export default {
    name: "AuthModal",
    computed: {
        checkAll: {
            get: function () {
                return this.$page.props.config.scopes.api ? this.form.apiScopes.length == this.$page.props.config.scopes.api.length : false;
            },
            set: function (value) {
                let checked = [];
                if (value) {
                    this.$page.props.config.scopes.api.forEach(function (scope) {
                        checked.push(scope.id);
                    });
                }
                this.form.apiScopes = checked;
            }
        },
        checkAllChat: {
            get: function () {
                return this.$page.props.config.scopes.chat ? this.form.chatScopes.length == this.$page.props.config.scopes.chat.length : false;
            },
            set: function (value) {
                let checked = [];
                if (value) {
                    this.$page.props.config.scopes.chat.forEach(function (scope) {
                        checked.push(scope.id);
                    });
                }
                this.form.chatScopes = checked;
            }
        },
    },
    data() {
        return {
            form: {
                apiScopes: [],
                chatScopes: [],
                privacy: false,
                current: this.$page.url,
                remember: false,
            },
        }
    },
    methods: {
        submit() {
            router.post('/auth', this.form)
        },
    }
}
</script>
