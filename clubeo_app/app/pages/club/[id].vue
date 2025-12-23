<template>
    <div v-if="club">
        <h1>{{ club.name }}</h1>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const club = ref(null)

const fetchClubDetails = async () => {
    try {
        const id = route.params.id
        const res = await fetch(`/clubeo_php_api/getClubDetails.php?id=${id}`);

        const data = await res.json()

        if (res.ok) {
            club.value = data
        } else {
            console.error(data.error)
        }

    } catch (error) {
        console.error("Connection error")
    }
}

onMounted(() => {
    fetchClubDetails()
})
</script>