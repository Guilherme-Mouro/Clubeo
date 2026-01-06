<template>
    <header v-if="club">
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between bg-custom-cards_menu rounded-xl p-8 gap-6">

            <div class="flex flex-row items-center gap-5">
                <div class="shrink-0">
                    <Avatar class="w-48 h-48" :image="club.image_banner" />
                </div>

                <div class="flex flex-col gap-1">
                    <h1 class="text-custom-highlight font-bold text-4xl md:text-6xl tracking-tight">
                        {{ club.name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 text-custom-first_text opacity-90">
                        <p><strong>{{ club.members_num }}</strong> members</p>
                        <span class="hidden md:inline">•</span>
                        <p class="italic">{{ club.description }}</p>
                    </div>
                </div>
            </div>

            <button v-if="isMember" disabled
                class="w-full md:w-auto bg-gray-600 text-white/70 font-bold rounded-lg px-8 py-3 cursor-not-allowed border border-white/10">
                Joined ✓
            </button>

            <button v-else @click="joinClub"
                class="w-full md:w-auto bg-custom-highlight hover:opacity-90 text-white font-bold rounded-lg px-8 py-3 transition-all duration-200 shadow-lg">
                Join Club
            </button>
        </div>
    </header>
    <div v-else class="text-custom-first_text font-bold">Loading...</div>

    <div>
        <button class="bg-custom-highlight text-white font-bold rounded-lg p-2" @click="toPost">Post
            +</button>
    </div>

    <div v-for="post in posts" :key="post.id"
        class="flex flex-col mt-6 shadow-xl rounded-xl overflow-hidden border border-white/5">
        <div class="flex flex-row items-center bg-custom-highlight p-3">
            <div class="shrink-0 border-2 border-white/20 rounded-full overflow-hidden">
                <Avatar />
            </div>
            <div class="flex flex-col ml-3">
                <h4 class="font-bold text-white text-lg leading-tight">Mouro</h4>
                <h6 class="text-white/70 text-xs">{{ post.created_at }}</h6>
            </div>
        </div>

        <div class="bg-custom-cards_menu">
            <div class="p-6">
                <p class="text-custom-first_text text-lg leading-relaxed">
                    {{ post.content }}
                </p>
            </div>

            <div class="px-5 py-3 bg-black/20 border-t border-white/5 flex items-center gap-4">
                <button class="flex items-center gap-2 group transition-all">
                    <div class="p-2 rounded-full group-hover:bg-red-500/20 transition-colors"
                        @click="likePost(post.id)">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-custom-highlight group-hover:text-red-500 transition-colors"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                    </div>
                    <span class="text-custom-first_text font-bold text-lg group-hover:text-red-500 transition-colors">
                        {{ post.likes_num }}
                    </span>
                </button>

                <div class="h-4 w-[1px] bg-white/10"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'

const authCookie = useCookie('auth_data')
const userId = authCookie.value?.id

const toast = useToast()
const route = useRoute()

const club = ref(null)
const posts = ref([])
const userClubs = ref([])

const clubIdFromRoute = route.params.id

const isMember = computed(() => {
    if (!userClubs.value || userClubs.value.length === 0) return false;
    return userClubs.value.some(c => c.id == clubIdFromRoute);
})

const toPost = () => {
    navigateTo({
        path: `/club/create-post`,
        query: { clubId: clubIdFromRoute }
    })
}

const fetchClubDetails = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/getClubDetails.php?id=${clubIdFromRoute}`);
        const data = await res.json()
        if (res.ok) club.value = data
    } catch (error) {
        console.error(error)
    }
}

const fetchPosts = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/getPosts.php?id=${clubIdFromRoute}`);
        const data = await res.json()
        if (res.ok) {
            posts.value = data
        }
    } catch (error) {
        console.error(error)
    }
}

const fetchUserClubs = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/getUserClubs.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
            body: JSON.stringify({ userId: userId })
        });

        const data = await res.json();
        if (res.ok) {
            userClubs.value = data;
        }
    } catch (error) {
        console.error(error)
    }
}

const joinClub = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/insertClubMembers.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
            body: JSON.stringify({
                clubId: clubIdFromRoute,
                userId: userId
            })
        });

        if (res.ok) {
            toast.success({ title: 'Success!', message: 'Joined the club successfully!' })
            await fetchClubDetails();
            await fetchUserClubs();
        }
    } catch (error) {
        toast.error({ title: 'Error!', message: 'Connection error!' })
    }
}

const likePost = async (postId) => {
    try {
        const res = await fetch(`/clubeo_php_api/likePost.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                userId: userId,
                postId: postId
            })
        });

        if (res.ok) {
            fetchPosts();
        }
    } catch (error) {
        console.error(error)
    }
}

onMounted(() => {
    fetchClubDetails()
    fetchPosts()
    fetchUserClubs()
})
</script>