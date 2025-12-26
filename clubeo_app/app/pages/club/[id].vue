<template>
    <header v-if="club">
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between bg-custom-cards_menu rounded-xl p-8 gap-6">

            <div class="flex flex-row items-center gap-5">
                <div class="shrink-0">
                    <Avatar />
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

            <button @click="joinClub"
                class="w-full md:w-auto bg-custom-highlight hover:opacity-90 text-white font-bold rounded-lg px-8 py-3 transition-all duration-200 shadow-lg">
                Join Club
            </button>
        </div>
    </header>
    <div v-else class="text-custom-first_text font-bold">Loading...</div>

    <div>
        <button class="bg-custom-highlight text-custom-first_text font-bold rounded-lg p-2" @click="toPost">Post
            +</button>
    </div>


    <div v-for="post in posts" :key="post.id">
        <p>{{ post.content }}</p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const club = ref(null)
const posts = ref([])
const userId = localStorage.getItem('userId')

const clubIdFromRoute = route.params.id 

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
        console.error("Erro ao carregar detalhes do clube")
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
        console.error("Erro ao carregar posts")
    }
}

const joinClub = async () => {
    try {
        const res = await fetch(`/clubeo_php_api/insertClubMembers.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ 
                clubId: clubIdFromRoute, 
                userId: userId 
            })
        });
        
        if (res.ok) {
            alert("Você entrou no clube!");
            fetchClubDetails();
        }
    } catch (error) {
        console.error("Erro ao entrar no clube")
    }
}

onMounted(() => {
    fetchClubDetails()
    fetchPosts()
})
</script>