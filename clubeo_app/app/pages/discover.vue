<template>
    Discover new Clubs or create your own
    <button @click="creatClub" class="bg-custom-highlight">Create a new club</button>

    <div v-for="club in clubs" :key="club.id" class="club-card">
        <h3>{{ club.name }}</h3>
        <p>{{ club.description }}</p>
        <div class="meta">ID: {{ club.id }} | Admin ID: {{ club.admin_id }}</div>
      </div>
</template>

<script setup>
const creatClub = () => navigateTo('/create-club')

const clubs = ref([])

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
</script>