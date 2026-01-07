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

    <div v-if="isMember">
        <button class="bg-custom-highlight text-white font-bold rounded-lg p-2" @click="toPost">Post
            +</button>
    </div>

    <div v-for="post in posts" :key="post.id">
        <PostCard :username="post.username" :user_avatar="post.avatar_url" :created_at="post.created_at"
            :content="post.content" :likes_num="post.likes_num" @likePost="likePost(post.id)" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import PostCard from '~/components/post-card.vue'

const authCookie = useCookie('auth_data')
const toast = useToast()
const route = useRoute()

const posts = ref([])
const club = ref(null)
const userClubs = ref([])

const clubIdFromRoute = computed(() => route.params.id)

const userId = computed(() => authCookie.value?.user?.id || null)

const isMember = computed(() => {
    if (!userClubs.value || userClubs.value.length === 0) return false

    const targetId = Number(clubIdFromRoute.value)

    const found = userClubs.value.some(c => Number(c.id) === targetId)

    console.log(`Verificando membro: ClubID=${targetId}, Encontrado=${found}`)

    return found
})

const toPost = () => {
    navigateTo({
        path: `/club/create-post`,
        query: { clubId: clubIdFromRoute.value }
    })
}

const fetchClubDetails = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/getClubDetails.php?id=${clubIdFromRoute.value}`);
        const data = await res.json()
        if (res.ok) club.value = data
    } catch (error) {
        console.error("Erro detalhes clube:", error)
    }
}

const fetchPosts = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/getPosts.php?id=${clubIdFromRoute.value}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
        });
        const data = await res.json()
        if (res.ok) posts.value = data
    } catch (error) {
        console.error("Erro posts:", error)
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
            body: JSON.stringify({ userId: userId.value })
        });

        const data = await res.json();
        if (res.ok) {
            userClubs.value = data;
            console.log("Os meus clubes carregados:", data)
        }
    } catch (error) {
        console.error("Erro user clubs:", error)
    }
}

const joinClub = async () => {
    if (isMember.value) return;

    try {
        const res = await fetch(`/clubeo_php_api/insertClubMembers.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
            body: JSON.stringify({
                clubId: clubIdFromRoute.value,
            })
        });

        const data = await res.json();

        if (res.ok) {
            toast.success({ title: 'Success!', message: 'Joined the club successfully!' })

            await fetchUserClubs();
            await fetchClubDetails();
        } else {
            if (data.message === "Already a member") {
                await fetchUserClubs();
            } else {
                toast.error({ title: 'Error', message: data.message || 'Error joining' })
            }
        }
    } catch (error) {
        toast.error({ title: 'Error!', message: 'Connection error!' })
    }
}

const likePost = async (postId) => {
    try {
        const res = await fetch(`/clubeo_php_api/likePost.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
            body: JSON.stringify({ postId: postId })
        });

        if (res.ok) fetchPosts();
    } catch (error) {
        toast.error({ title: 'Error!', message: 'Connection error!' })
    }
}

onMounted(() => {
    fetchClubDetails()
    fetchPosts()
    fetchUserClubs()
})
</script>