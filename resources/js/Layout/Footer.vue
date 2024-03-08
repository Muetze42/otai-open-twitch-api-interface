<template>
    <footer class="bg-primary-950 w-full z-40 border-t border-primary-800/50">
        <div class="max-w-content mx-auto flex flex-wrap gap-2 p-2 items-center justify-center md:justify-between">
            <div class="flex flex-wrap gap-2 max-md:justify-center">
                <a href="https://github.com/Muetze42/otai-open-twitch-api-interface" target="_blank" class="btn">
                    <FontAwesomeIcon icon="fa-brands fa-github" fixed-width />
                    Issues/Questions
                </a>
                <a href="https://huth.it/coffee" target="_blank" class="btn">
                    <FontAwesomeIcon icon="fa-solid fa-hand-holding-heart" fixed-width />
                    Support/BeGreat
                </a>
                <a href="https://www.linkedin.com/in/normanhuth/" target="_blank" class="btn">
                    <FontAwesomeIcon icon="fa-brands fa-linkedin" fixed-width />
                    Follow/Connect
                </a>
            </div>
            <div class="text-sm">
                2023 <a href="https://huth.it" target="_blank">Norman Huth</a>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: "Footer",
}
</script>
