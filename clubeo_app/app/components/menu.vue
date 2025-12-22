<template>
    <div class="flex flex-col h-screen">
        <div class="p-2">
            <h1 class="text-custom-highlight font-bold text-5xl">Clubeo</h1>
        </div>

        <nav class="mb-5">
            <ul>
                <li class="menu-items">
                    <NuxtLink to="/" class="flex items-center gap-1 p-2 w-full">
                        <HomeIcon class="w-5 h-5" />
                        <strong>Home</strong>
                    </NuxtLink>
                </li>
                <li class="menu-items">
                    <NuxtLink to="/discover" class="flex items-center gap-1 p-2 w-full">
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
            <h4 class="text-custom-second_text mt-5">My Clubs</h4>
            <div class="flex flex-row">
            </div>
        </div>

        <div class="mt-auto mb-10">
            <SwitchColorMode class="mb-3" />
            <NuxtLink v-if="user" :to="`/${user.id}/profile`">
                <div class="profile">
                    <div class="mr-3">
                        <Avatar />
                    </div>
                    <div class="flex flex-col">
                        <p class="text-custom-first_text"><strong>{{ user.username }}</strong></p>

                        <p v-if="user.online" class="flex items-center gap-2 text-green-600">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                            Online
                        </p>
                    </div>
                </div>
            </NuxtLink>
        </div>
    </div>
</template>

<script setup>
import { BellAlertIcon, HomeIcon, RocketLaunchIcon } from '@heroicons/vue/16/solid'

const config = useRuntimeConfig()
const user = ref(null)

const fetchUserData = async (userId) => {
    try {
        const res = await fetch(`/clubeo_php_api/getUser.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: userId })
        });

        const data = await res.json()

        if (res.ok) {
            user.value = data.user
        } else {
            console.error(data.error)
        }

    } catch (error) {
        console.error("Connection error")
    }
}

onMounted(() => {
    const storedId = localStorage.getItem('userId')

    if (storedId) {
        fetchUserData(storedId)
    } else {
        //navigateTo('/login')

        user.value = {
            id: 1,
            username: 'Admin',
            online: 1,
        }
    }
})

</script>

<style scoped>
.menu-items {
    @apply text-custom-second_text rounded-lg mb-1 transition-colors
}

.menu-items:hover {
    @apply bg-custom-highlight text-custom-first_text
}

.profile {
    @apply flex flex-row bg-custom-corners rounded-lg p-5
}
</style>