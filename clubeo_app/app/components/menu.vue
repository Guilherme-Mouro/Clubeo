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
            <NuxtLink  :to="`/${user.id}/profile`">
                <div class="profile">
                    <div class="mr-3">
                        <Avatar class="w-12 h-12" :image="user.avatar_url"/>
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

// Get session data from cookie
const authCookie = useCookie('auth_data')

const toast = useToast()

const user = ref({
    id: null,
    username: '',
    avatar_url: '',
    online: 0,
    clubs: []
})

/**
 * Updates the user's online status in the database
 * @param {number} status - 1 for Online, 0 for Offline
 */
const updateOnlineStatus = (status) => {
    if (!authCookie.value?.userId || !authCookie.value?.token) return;

    user.value.online = status;

    const url = '/clubeo_php_api/updateUserStatus.php';
    const payload = JSON.stringify({
        id: authCookie.value.userId,
        token: authCookie.value.token,
        status: status
    });

    if (status === 0 && navigator.sendBeacon) {
        // Use sendBeacon for reliable delivery during page unload
        const blob = new Blob([payload], { type: 'application/json' });
        navigator.sendBeacon(url, blob);
    } else {
        // Use standard fetch for logging in/active changes
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: payload
        }).catch(err => console.error("Status update failed:", err));
    }
}

/**
 * Fetches core user profile data
 */
const fetchUserData = async () => {
    if (!authCookie.value?.userId || !authCookie.value?.token) {
        navigateTo('/login');
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

        const data = await res.json();
        if (res.ok) {
            user.value = { ...user.value, ...data.user };
            // Mark as online once data is successfully loaded
            updateOnlineStatus(1);
        } else {
            authCookie.value = null;
            navigateTo('/login');
        }
    } catch (error) {
        toast.error({ title: 'Error!', message: 'Connection error while fetching user data!' });
    }
}


//  Fetches the list of clubs the user belongs to
const fetchUserClubs = async (userId) => {
    try {
        const res = await fetch(`/clubeo_php_api/getUserClubs.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ userId: userId })
        });

        const data = await res.json();
        if (res.ok) {
            user.value.clubs = data;
        }
    } catch (error) {
        toast.error({ title: 'Error!', message: 'Connection error while fetching clubs!' });
    }
}

const goToClub = (id) => {
    navigateTo(`/club/${id}`)
}

// Handler for browser tab close or refresh
const handleTabClose = () => updateOnlineStatus(0);

onMounted(async () => {
    if (authCookie.value?.userId) {
        await fetchUserData();
        await fetchUserClubs(authCookie.value.userId);

        // Listen for page unload/close events
        window.addEventListener('beforeunload', handleTabClose);
    } else {
        navigateTo('/login');
    }
})

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleTabClose);
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