<template>
    <div class="flex flex-row items-center">
        <SearchBar v-model="searchQuery"/>
        <button @click="creatClub" class="bg-custom-highlight ml-10 text-custom-first_text font-bold rounded-lg p-2">Create a
            new club +</button>
    </div>

    <div v-for="club in filteredClubs" :key="club.id" class="flex flex-row items-center bg-custom-cards_menu rounded-lg p-2">
        <Avatar />
        <div class="flex flex-col ml-4">
            <h3 class="text-custom-highlight font-bold text-3xl">{{ club.name }}</h3>
            <p class="text-custom-first_text">{{ club.description }}</p>
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const creatClub = () => navigateTo('/create-club')

const searchQuery = ref('')
const clubs = ref([])

const filteredClubs = computed(() => {
    if (!searchQuery.value.trim()) return clubs.value

    const query = searchQuery.value.toLocaleLowerCase()

    return clubs.value.filter(club => {
        return club.name.toLocaleLowerCase().includes(query) || club.description.toLocaleLowerCase().includes(query)
    })
})

const fetchClubs = async (userId) => {
    try {
        const res = await fetch(`/clubeo_php_api/getClubs.php`);

        const data = await res.json()

        if (res.ok) {
            clubs.value = data
        } else {
            console.error(data.error)
        }

    } catch (error) {
        console.error("Connection error")
    }
}

onMounted(() => {
    fetchClubs()
})
</script>