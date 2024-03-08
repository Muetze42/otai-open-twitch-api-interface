<template>
    <div class="border-x pb-4 px-4 w-full epc-row">
        <div v-for="field in fields" class="border-y">
            <div class="flex flex-col lg:flex-row text-sm">
                <div class="border-l max-lg:border-r w-full p-2 font-mono">
                    {{ field.id }}
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
</template>
jij
<script>

export default {
    name: "EndpointChild",
    components: {
        // async import:
        EndpointChild: () => import('./EndpointChild.vue')
    },
    props: [
        'fields'
    ],
}
</script>
