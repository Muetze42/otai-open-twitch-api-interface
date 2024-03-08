<template>
    <div class="flex flex-col divide-y divide-primary-800/50 gap-y-4 mb-4">
        <template v-for="resource in resources">
            <div v-if="resource.items.length">
                <h3 class="mt-2"><span class="font-normal">Resource:</span> {{ resource.name }}</h3>
                <div class="flex flex-wrap gap-2">
                    <Component v-for="item in resource.items"
                               :is="item.active ? 'Link' : 'div'"
                               class="cc flex flex-col justify-between gap-2 w-full xl:max-w-[31rem] animate-fade min-h-[7.5rem]"
                               :class="opacity"
                               :href="item.active ? '/endpoints/'+item.slug : null"
                    >
                        <span>
                            <span class="text-xl block font-medium mb-0.5">
                                {{ item.name }}
                            </span>
                            <span v-html="item.description" />
                        </span>
                        <span class="flex flex-wrap gap-x-1 gap-y-0.5">
                            <code v-for="scope in item.scopes">{{ scope }}</code>
                        </span>
                    </Component>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "ResourceBlock",
    props: [
        'resources',
        'opacity'
    ],
}
</script>
