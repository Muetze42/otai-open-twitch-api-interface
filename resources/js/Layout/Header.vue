<template>
    <header class="desktop:fixed bg-primary-950 w-full z-40 border-b border-primary-800/50">
        <div class="max-w-content mx-auto flex flex-wrap justify-between py-2 px-content gap-2 items-center">
            <Link class="flex flex-wrap gap-x-0.5 btn" href="/">
                <span class="font-semibold">{{ $page.props.config.app_name_short }}</span>
                <span class="hidden desktop:inline-block">-</span>
                <span class="hidden desktop:inline-block">{{ $page.props.config.app_name }}</span>
            </Link>
            <div class="hidden" :class="$page.props.config.theme.themes.map(el => $page.props.config.theme.prefix + el)" />
            <div class="flex flex-wrap gap-x-2 gap-y-1">
                <button type="button" class="btn" v-if="$page.props.user" @click="logout">
                    <FontAwesomeIcon icon="fa-solid fa-hand-wave" fixed-width />
                    Logout
                </button>
                <button class="btn" type="button" @click="open = true">
                    <FontAwesomeIcon icon="fa-brands fa-twitch" fixed-width />
                    Authentication
                </button>
                <Menu as="div" class="relative inline-block z-50">
                    <MenuButton class="form-select w-36 text-left" title="Select color theme">
                        <FontAwesomeIcon icon="fa-solid fa-palette" fixed-width />
                        {{ getThemeValue() }}
                    </MenuButton>
                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <MenuItems class="absolute rounded-sm shadow flex flex-col bg-primary-950 ring-1 ring-primary-700 w-full divide-y divide-primary-800">
                            <MenuItem as="button" class="menu-item" v-slot="{ active }" :class="{ 'hidden': !getCurrentTheme() }" @click="setTheme()">
                                Teal
                            </MenuItem>
                            <MenuItem v-for="theme in $page.props.config.theme.themes" class="menu-item" as="button" v-slot="{ active }" :class="{ 'hidden': getCurrentTheme() == theme }" @click="setTheme(theme)">
                                {{ capitalized(theme) }}
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </div>
    </header>
    <AuthDialogs v-if="open" @close="open = false" title="Twitch Authentication" />
</template>

<script>
import {Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { router } from '@inertiajs/vue3'
import AuthDialogs from './../Components/Dialogs/Auth.vue'

export default {
    name: "Header",
    components: {
        FontAwesomeIcon,
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        AuthDialogs,
    },
    data() {
        return {
            open: false,
        }
    },
    methods: {
        logout() {
            if (confirm('Do you really want to log out?')) {
                router.post('/auth/logout')
            }
        },
        capitalized(value) {
            return value[0].toUpperCase() + value.slice(1)
        },
        getThemeValue() {
            return localStorage.theme ? this.capitalized(localStorage.theme) : 'Teal'
        },
        getCurrentTheme() {
            return localStorage.theme
        },
        setTheme(theme = null) {
            theme ? localStorage.theme = theme : localStorage.removeItem('theme')
            this.applyTheme()
        },
        applyTheme() {
            let htmlTag = document.querySelector('html')

            htmlTag.classList.forEach(name => {
                if (name.trim().startsWith(this.$page.props.config.theme.prefix)) {
                    htmlTag.classList.remove(name)
                }
            })

            if (localStorage.theme) {
                htmlTag.classList.add(this.$page.props.config.theme.prefix + localStorage.theme)
            }
        }
    },
    mounted() {
        this.applyTheme()
    },
}
</script>
