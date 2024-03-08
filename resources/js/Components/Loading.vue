<template>
    <div class="text-center cursor-wait">
        <div class="animate-pulse mb-2" :class="loadingClass">
            Loading...
        </div>
        <FontAwesomeIcon icon="fa-solid fa-spinner" class="animate-spin" :class="iconClass" />
    </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSpinner } from '@fortawesome/pro-solid-svg-icons'
library.add(faSpinner);

export default {
    name: "Loading",
    props: [
        'loadingClass',
        'iconClass',
    ],
}
</script>
