<template>
    <Header />
    <main class="max-w-content mx-auto px-content pt-8 desktop:pt-20 pb-20 mb-auto">
        <slot/>
    </main>
    <Footer />
</template>

<script>
import Header from "./Header.vue";
import Footer from "./Footer.vue";

export default {
    name: "Layout",
    components: {
        Header,
        Footer,
    },
    updated() {
        if (this.$page.props.title) {
            // noinspection JSValidateTypes
            document.title = this.$page.props.title
        }
        if (this.$page.props.description) {
            // noinspection JSUnresolvedVariable
            document.head.children.description.content = this.$page.props.description
        }
    },
}
</script>
