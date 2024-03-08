<template>
    <ScopeAuthentication v-if="!can_execute" :scopes="scopes" />
    <form class="cc doc-formatted flex flex-col gap-4 pb-4" @submit.prevent="submit">
        <h1 class="mb-0">{{ form_data.name }}</h1>
        <div class="border-y pb-0.5 border-primary-800/50">
            Reference:
            <a class="break-all" :href="'https://dev.twitch.tv/docs/api/reference/#'+form_data.slug" target="_blank">https://dev.twitch.tv/docs/api/reference/#{{ form_data.slug }}</a>
        </div>
        <div v-if="form_data.instruction" v-html="form_data.instruction" />
        <div v-if="form_data.authorization">
            <h2>Authorization</h2>
            <div v-html="form_data.authorization" />
        </div>
        <div>
            <h2>Route</h2>
            <code class="break-all">{{ form_data.method }} {{ form_data.route }}</code>
        </div>
        <template v-for="(title, section) in sections">
            <div v-if="form_data[section].fields.length">
                <h2 class="mb-4">{{ title }}</h2>
                <div v-if="form_data[section].text" v-html="form_data[section].text" />
                <div>
                    <div v-for="field in form_data[section].fields" class="border-y">
                        <div class="flex flex-col lg:flex-row">
                            <div class="border-l max-lg:border-r w-full p-2">
                                <div class="flex flex-wrap gap-2 mb-1 items-center">
                                    <div class="font-medium font-mono">{{ field.id }}</div>
                                    <label v-if="!field.required && can_execute">
                                        <input type="checkbox" class="form-checkbox" v-model="form.useFields[field.id]">
                                        Send this element in the request
                                    </label>
                                </div>
                                <div v-if="field.attributes.type == 'JSON' || field.children">
                                    <JsonEditorVue
                                        v-if="field.required || form.useFields[field.id] || !can_execute"
                                        class="jse-theme-dark"
                                        :bind="{mode: 'text'}"
                                        mode="text"
                                        v-model="form.fields[field.id]"
                                        :readOnly="!can_execute"
                                        :required="can_execute && field.required"
                                    />
                                </div>
                                <label v-if="field.attributes.type == 'bool' && (form.useFields[field.id] || field.required || !can_execute)">
                                    <input type="checkbox" class="form-checkbox rounded" v-model="form.fields[field.id]" value="1">
                                    <code>True</code>
                                </label>
                                <input v-else-if="form.useFields[field.id] || field.required || !can_execute"
                                       v-model="form.fields[field.id]"
                                       :type="field.attributes.type"
                                       class="form-input font-mono"
                                       :readonly="!can_execute"
                                       :required="can_execute && field.required"
                                >
                                <div v-if="field.arrayable && form.useFields[field.id]">
                                    Enter <span class="italic font-medium">comma separated</span> value to create to specify more than one {{ field.id }}.<br>
                                    Example: <code>1312,1706</code> generate <code>{{ field.id }}=1312&amp;{{ field.id }}=1706</code>
                                </div>
                                <div v-if="this.errors && this.errors['fields.'+[field.id]] && (field.required || form.useFields[field.id])">
                                    <ul class="list-none bg-rose-200 text-rose-800 font-semibold">
                                        <li v-for="error in this.errors['fields.'+[field.id]]">
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="border-r max-lg:border-l w-full p-2">
                                <div>Type: <strong v-html="field.type" /></div>
                                <div>Required: <strong v-html="field.required" /></div>
                                <div class="text-sm" v-html="field.instruction" />
                            </div>
                        </div>
                        <EndpointChild v-if="field.children" :fields="field.children" />
                    </div>
                </div>
            </div>
        </template>
        <div class="text-center">
            <button class="btn text-lg pt-1 pb-1.5 justify-center w-[150px]" type="submit" :disabled="processing || !can_execute">
                <Loading v-if="processing" loadingClass="sr-only" />
                <span v-else>Send Request</span>
            </button>
        </div>
    </form>

    <div class="cc doc-formatted flex flex-col pb-4 mt-4">
        <h2>Your latest requests</h2>
        <Loading v-if="$page.props.user && processingIndex" loadingClass="sr-only" iconClass="h-24" />
        <div v-else-if="$page.props.user && indexData && indexData.data && indexData.data.length">
            <table>
                <tr v-for="row in indexData.data">
                    <td class="w-1 sm:whitespace-nowrap p-2">{{ row.created_at }}</td>
                    <td class="w-1 whitespace-nowrap">
                        <button type="button" class="btn" @click="openRequest(row.id)">
                            Show
                        </button>
                    </td>
                    <td class="p-2">
                        <span class="py-0.5 px-2 rounded block text-sm  max-h-[4rem] overflow-y-auto" :class="getStatusCodeColors(row.response_code)">
                            {{ row.response_code }}
                            {{ row.status_text }}
                            <span v-if="response_codes[row.response_code]" class="pl-0.5 block">
                                <span class="mb-0-list" v-html="response_codes[row.response_code]" />
                            </span>
                        </span>
                    </td>
                </tr>
            </table>
            <div v-if="indexData.links.length > 3" class="mt-1">
                <template v-for="link in indexData.links">
                    <button type="button"
                            class="btn rounded-none justify-center min-w-[1.75rem]"
                            v-html="link.label"
                            :disabled="link.active"
                            v-if="link.url"
                            @click="getRequestsHistory(link.url)"
                    />
                </template>
            </div>
        </div>
        <div v-else class="text-center italic p-2 opacity-90 text-lg">
            Nothing here
        </div>
    </div>

    <Modal :show="showRequest" @close="showRequest = false" size="max-w-5xl" title="API Request">
        <Loading v-if="!requestModel" loadingClass="text-xl" iconClass="h-14" class="p-4" />
        <template v-else>
            <div class="flex flex-col divide-y divide-primary-800/50 overflow-y-auto max-h-[70vh]" scroll-region>
                <div class="p-4">
                    <div class="font-semibold mb-1">
                        Status
                    </div>
                    <span class="py-0.5 px-2 rounded inline-block text-sm" :class="getStatusCodeColors(requestModel.response_code)">
                        {{ requestModel.response_code }}
                        {{ requestModel.status_text }}
                        <span v-if="response_codes[requestModel.response_code]" class="pl-0.5">
                            <FontAwesomeIcon icon="fa-brands fa-twitch" fixed-width />
                            <span v-html="response_codes[requestModel.response_code]" />
                        </span>
                    </span>
                </div>
                <div class="p-4">
                    <div class="font-semibold mb-1">
                        URL with query parameters
                    </div>
                    <textarea class="form-textarea break-all font-mono" rows="1" readonly>{{ requestModel.url }}</textarea>
                </div>
                <div class="p-4" v-if="requestModel.request_body && (requestModel.request_body.length || Object.keys(requestModel.request_body).length)">
                    <div class="font-semibold mb-1">
                        Request Body
                    </div>
                    <div v-if="showRaw.request_body">
                        <button type="button" class="btn mb-1" @click="showRaw.request_body = false">Show Pretty</button>
                        <textarea
                            :value="JSON.stringify(requestModel.request_body, null, 2)"
                            class="form-textarea font-mono text-xs overflow-auto text-black whitespace-nowrap p-0"
                            rows="5"
                        />
                    </div>
                    <div v-else>
                        <button type="button" class="btn mb-1" @click="showRaw.request_body = true">Show RAW</button>
                        <VueJsonPretty
                            :data="requestModel.request_body"
                            :showIcon="true"
                            :deep="4"
                        />
                    </div>
                </div>
                <div class="p-4" v-if="requestModel.response_body && (requestModel.response_body.length || Object.keys(requestModel.response_body).length)">
                    <div class="font-semibold mb-1">
                        Response Body
                    </div>
                    <div v-if="showRaw.response_body">
                        <button type="button" class="btn mb-1" @click="showRaw.response_body = false">Show Pretty</button>
                        <textarea
                            :value="JSON.stringify(requestModel.response_body, null, 2)"
                            class="form-textarea font-mono text-xs overflow-auto text-black whitespace-nowrap p-0 min-h-[7.5rem]"
                            rows="5"
                        />
                    </div>
                    <div v-else>
                        <button type="button" class="btn mb-1" @click="showRaw.response_body = true">Show RAW</button>
                        <VueJsonPretty
                            :data="requestModel.response_body"
                            :showIcon="true"
                            :deep="3"
                        />
                    </div>
                </div>
            </div>
            <div class="p-4 text-center">
                <button type="button" class="btn" @click="deleteRequest(requestModel.id)">
                    Delete this request from history
                </button>
            </div>
        </template>
    </Modal>
</template>
<script>
import EndpointChild from './../../Components/EndpointChild.vue'
import ScopeAuthentication from './../../Components/Alerts/ScopeAuthentication.vue'
import 'vanilla-jsoneditor/themes/jse-theme-dark.css'
import JsonEditorVue from 'json-editor-vue'
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

export default {
    name: "RequestsShow",
    components: {
        EndpointChild,
        JsonEditorVue,
        ScopeAuthentication,
        VueJsonPretty,
    },
    props: [
        'form_data',
        'scopes',
        'can_execute',
        'endpoint',
        'response_codes',
        'latest',
    ],
    mounted() {
        if (this.$page.props.user) {
            this.getRequestsHistory()
        }
    },
    methods: {
        deleteRequest(id) {
            if (confirm('Do you really want delete this request from history?')) {
                axios.delete('/requests/' + id)
                    .then(response => {
                        this.getRequestsHistory()
                        this.showRequest = false
                        this.requestModel = null
                    }).catch(error => {
                    error.response.status == 422 ? this.errors = error.response.data.errors :
                        this.errorHandler(error)
                })
            }
        },
        getRequestsHistory(url = null) {
            this.processingIndex = true
            if (!url) {
                url = '/requests/list/'+this.endpoint
            }
            axios.post(url)
                .then(response => {
                    this.indexData = response.data
                    this.processingIndex = false
                }).catch(error => {
                    error.response.status == 422 ? this.errors = error.response.data.errors :
                        this.errorHandler(error)
            })
        },
        getStatusCodeColors(status) {
            if (status >= 400) {
                return 'bg-red-700 text-red-100'
            }
            if (status >= 300) {
                return 'bg-blue-700 text-blue-100'
            }
            return 'bg-green-700 text-green-100'
        },
        openRequest(id = null) {
            if (id && (!this.requestModel || (this.requestModel && this.requestModel.id && this.requestModel.id !== id))) {
                this.requestModel = null
                axios.post('/requests/'+id)
                    .then(response => {
                        this.requestModel = response.data
                        this.showRequest = true
                    }).catch(error => {
                        this.errorHandler(error)
                    })
                return
            }
            this.showRequest = true
        },
        submit() {
            this.errors = null
            this.processing = true

            axios.put('/requests/'+this.endpoint, this.form)
                .then(response => {
                    this.showRequest = true
                    this.requestModel = response.data
                    this.processing = false
                    this.getRequestsHistory()
                }).catch(error => {
                    if (error.response.status === 422) {
                        let data = error.response.data
                        this.errors = data.errors
                        console.log(this.errors)
                        alert(data.message)
                        return
                    }
                    this.errorHandler(error)
                })

            this.processing = false
        }
    },
    data() {
        return {
            showRaw: {
                request_body: false,
                response_body: false,
            },
            requestModel: null,
            showRequest: false,
            processing: false,
            indexData: null,
            processingIndex: true,
            form: {
                fields: {
                    broadcaster_id: this.$page.props.user ? this.$page.props.user.id : null,
                    moderator_id: this.$page.props.user ? this.$page.props.user.id : null,
                    user_id: this.$page.props.user ? this.$page.props.user.id : null,
                    from_broadcaster_id: this.$page.props.user ? this.$page.props.user.id : null,
                    to_broadcaster_id: this.$page.props.user ? this.$page.props.user.id : null,
                    from_id: this.$page.props.user ? this.$page.props.user.id : null,
                    to_id: this.$page.props.user ? this.$page.props.user.id : null,
                },
                useFields: {},
            },
            errors: null,
            sections: {
                request_query_parameters: "Request Query Parameters",
                request_body: "Request Body",
                //response_body: "Response Body",
            },
            pseudoModel: [],
        }
    },
}
</script>
