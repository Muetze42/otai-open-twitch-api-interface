<template>
    <div class="bg-rose-200 text-rose-800 font-semibold text-center p-2 mb-4 rounded border-2 border-rose-800 flex flex-col gap-2 py-4">
        <p v-if="!scopes.length">
            You need a <strong>Twitch Authentication</strong> to use this endpoint.
        </p>
        <p v-else-if="scopes.length === 1">
            You need a <strong>Twitch Authentication</strong> includes the <strong>{{ scopes[0] }}</strong> scope to use this endpoint.
        </p>
        <p v-else>
            You need a <strong>Twitch Authentication</strong> includes the
            <template v-for="(scope, index) in scopes">
                <template v-if="index != 0"> or </template>
                <strong>{{ scope }}</strong>
            </template>
            scope to use this endpoint.
        </p>
        <p v-if="$page.props.user && $page.props.user.scopes.length && scopes.length">
            Your current {{ $page.props.user.scopes.length === 1 ? 'Scope' : 'Scopes' }}:
            <span class="block">
                <template v-for="(scope, index) in $page.props.user.scopes">
                    <template v-if="index != 0"> / </template>
                    <span class="font-mono">[{{ scope }}]</span>
                </template>
            </span>
        </p>
    </div>
</template>

<script>
export default {
    name: "ScopeAuthentication",
    props: ['scopes']
}
</script>
