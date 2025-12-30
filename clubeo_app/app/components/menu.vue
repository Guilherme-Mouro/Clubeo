<template>
    <div class="flex flex-col h-screen">
        <div class="p-2">
            <h1 class="text-custom-highlight font-bold text-6xl">Clubeo</h1>
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
        <hr><br>

        <div>
            <h4 class="text-custom-second_text mb-2">My Clubs</h4>
            <div class="flex flex-row">
                <div class="mr-2" v-for="club in user?.clubs" :key="club.id" @click="goToClub(club.id)">
                    <Avatar />
                </div>
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

                        <p v-if="user.online == 1" class="flex items-center gap-2 text-green-600">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                            Online
                        </p>
                        <p v-else class="flex items-center gap-2 text-red-600">
                            <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                            Offline
                        </p>
                    </div>
                </div>
            </NuxtLink>
        </div>
    </div>
</template>

<script setup>
import { BellAlertIcon, HomeIcon, RocketLaunchIcon } from '@heroicons/vue/16/solid'
import { ref, onMounted, onUnmounted } from 'vue'

const authCookie = useCookie('auth_data')

const user = ref({
    id: null,
    username: '',
    online: 0,
    clubs: []
})

const fetchUserData = async () => {
    if (!authCookie.value?.userId || !authCookie.value?.token) {
        console.error("Sessão não encontrada")
        navigateTo('/login')
        return;
    }

    try {
        const res = await fetch(`/clubeo_php_api/getUser.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id: authCookie.value.userId,
                token: authCookie.value.token
            })
        });

        const data = await res.json()
        if (res.ok) {
            user.value = { ...user.value, ...data.user };
        } else {
            authCookie.value = null
            navigateTo('/login')
        }

    } catch (error) {
        console.error("Connection error")
    }
}

const fetchUserClubs = async (userId) => {
    try {
        const res = await fetch(`/clubeo_php_api/getUserClubs.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ userId: userId })
        });

        const data = await res.json()

        if (res.ok) {
            user.value.clubs = data;
        } else {
            console.error(data.error)
        }

    } catch (error) {
        console.error("Connection error")
    }
}

const goToClub = (id) => {
    navigateTo(`/club/${id}`)
}

const handleTabClose = () => updateOnlineStatus(0);

onMounted(async () => {
    if (authCookie.value) {
        await fetchUserData()
        await fetchUserClubs(authCookie.value.userId)
    } else {
        navigateTo('/login')

        // user.value = {
        //     id: 1,
        //     username: 'Admin',
        //     online: 1,
        //     clubs: []
        // }
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