<template>
    <div>
        <div class="p-2">
            <h1 class="text-custom-highlight font-bold text-3xl">Clubeo</h1>
        </div>

        <nav>
            <ul>
                <li class="menu-items">
                    <NuxtLink to="/" class="flex items-center gap-1 p-2 w-full">
                        <HomeIcon class="w-5 h-5" />
                        <strong>Home</strong>
                    </NuxtLink>
                </li>
                <li class="menu-items">
                    <NuxtLink to="/" class="flex items-center gap-1 p-2 w-full">
                        <RocketLaunchIcon class="w-5 h-5" />
                        <strong>Discover</strong>
                    </NuxtLink>
                </li>
                <li class="menu-items">
                    <NuxtLink to="/" class="flex items-center gap-1 p-2 w-full">
                        <BellAlertIcon class="w-5 h-5" />
                        <strong>Notifications</strong>
                    </NuxtLink>
                </li>
            </ul>
        </nav>

        <br>
        <hr>
        <br>

        <div>
            <h4 class="text-custom-second_text">My Clubs</h4>
        </div>

        <div class="mt-auto w-full">
            <button @click="toggleTheme">Change</button>

            <NuxtLink to="/login">
                <div class="perfil">
                    <p>John Doe</p>
                    <p>Online</p>
                </div>
            </NuxtLink>
        </div>
    </div>
</template>

<script setup>
import { BellAlertIcon, HomeIcon, RocketLaunchIcon } from '@heroicons/vue/16/solid'

const mode = useColorMode()

const toggleTheme = () => {
    mode.preference = mode.preference === 'dark' ? 'light' : 'dark'
}

</script>

<style scoped>
.layout-wrapper {
    @apply flex flex-col h-screen
}

.menu-items {
    @apply text-custom-second_text rounded-lg mb-1 transition-colors
}

.menu-items:hover {
    @apply bg-custom-highlight text-custom-first_text
}

.perfil {
    @apply bg-custom-corners rounded-lg p-5
}
</style>