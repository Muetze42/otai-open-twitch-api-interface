// noinspection JSIgnoredPromiseFromCall

import './bootstrap';

import {createApp, h} from 'vue'
import {createInertiaApp, Link} from '@inertiajs/vue3'

import Layout from './Layout/Layout.vue'
import Modal from "./Components/Dialogs/Modal.vue"
import Loading from "./Components/Loading.vue"

import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';

import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {library} from '@fortawesome/fontawesome-svg-core'
import {
    faPalette,
    faHandWave,
    faSquareXmark,
    faHandHoldingHeart,
} from '@fortawesome/pro-solid-svg-icons'
import {
    faTwitch,
    faGithub,
    faLinkedin,
} from '@fortawesome/free-brands-svg-icons'
library.add(
    faHandWave,
    faPalette,
    faTwitch,
    faGithub,
    faSquareXmark,
    faLinkedin,
    faHandHoldingHeart,
)

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        );
        page.then((module) => {
            module.default.layout = module.default.layout || Layout;
        });
        return page;
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .component("Link", Link)
            .component("Modal", Modal)
            .component("FontAwesomeIcon", FontAwesomeIcon)
            .component("Loading", Loading)
            .mixin({
                // computed: {
                //     user() {
                //         return this.$page.props.user
                //     },
                //     config() {
                //         return this.$page.props.config
                //     },
                // },
                methods: {
                    errorHandler(error) {
                        let status = error.response.status
                        if (status == 419 || status == 503) {
                            let message = status == 419 ? 'Your session has expired. Click OK to reload the page.' :
                                'There is an update in progress. This lasts only a few seconds.'

                            alert(message)
                            location.reload()
                        } else {
                            error.response && error.response.data.message ?
                                alert('Error ' + status + ': ' + error.response.data.message) :
                                alert('Error ' + status)
                        }
                    }
                }
            })
            .mount(el)
    },
})
