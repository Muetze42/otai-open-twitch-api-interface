<template>
    <h1>Make Twitch API Request via Webinterface</h1>
    <h2>Available requests</h2>
    <ResourceBlock :resources="resources.active" />
    <hr class="mb-4">
    <h2 class="text-rose-800 bg-rose-200 rounded border border-rose-600 border-solid px-2 pb-0.5">Deactivated requests</h2>
    <ResourceBlock :resources="resources.inactive" opacity="opacity-80" />
</template>

<script>
import ResourceBlock from './../../Components/ResourceBlock.vue'

export default {
    name: "RequestsIndex",
    components: {
        ResourceBlock
    },
    props: [
        'resources',
    ]
}
</script>
