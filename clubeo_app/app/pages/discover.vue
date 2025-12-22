<template>
    Discover new Clubs or create your own
    <button @click="creatClub" class="bg-custom-highlight">Create a new club</button>
</template>

<script setup>
const creatClub = () => navigateTo('/create-club')

const fetchUserData = async (userId) => {
    try {
        const res = await fetch(`/clubeo_php_api/getClubs.php`, {
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
</script>