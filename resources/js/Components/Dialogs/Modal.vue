<template>
    <TransitionRoot as="template">
        <Dialog as="div" class="fixed z-40 inset-0 overflow-y-auto px-4">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click.self="this.$emit('close')">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <DialogOverlay class="fixed inset-0 bg-gray-900 bg-opacity-90 transition-opacity cursor-pointer" />
                </TransitionChild>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="cc p-0 rounded bg-primary-950 inline-block align-bottom overflow-hidden shadow-xl transform transition-all sm:align-middle w-full text-left" :class="sizeClass">
                        <DialogTitle as="div" class="p-2 flex justify-between space-x-2 border-b border-gray-500">
                            <button type="button" class="sr-only" aria-label="catch focus button" />
                            <div class="grow font-semibold truncate">
                                {{ title }}
                            </div>
                            <div class="text-right">
                                <button @click="this.$emit('close')" class="btn p-1 justify-center">
                                    <FontAwesomeIcon icon="fa-solid fa-square-xmark" fixed-width />
                                </button>
                            </div>
                        </DialogTitle>
                        <slot/>
                    </div>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    DialogDescription,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    name: "Modal",
    props: ['title', 'size'],
    data() {
        return {
            sizeClass: this.size ? this.size : 'max-w-3xl',
        }
    },
    components: {
        FontAwesomeIcon,
        Dialog,
        DialogPanel,
        DialogTitle,
        DialogDescription,
        DialogOverlay,
        TransitionChild,
        TransitionRoot,
    },
}
</script>
