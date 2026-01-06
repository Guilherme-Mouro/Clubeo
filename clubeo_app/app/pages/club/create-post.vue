<template>
    <InputCard v-model="content" placeholder="Content"></InputCard>
    <button class="bg-custom-highlight text-custom-first_text font-bold rounded-lg p-2"
        @click="createPost">Post</button>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const clubId = route.query.clubId
const content = ref('')

const authCookie = useCookie('auth_data')


const createPost = async () => {
    if (!authCookie.value?.token) {
        alert("Tens de estar ligado para criar um clube!");
        return navigateTo('/login');
    }

    try {
        const res = await fetch("/clubeo_php_api/createPost.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authCookie.value.token}`
            },
            body: JSON.stringify({
                clubId: clubId,
                content: content.value,
            })
        });

        const data = await res.json()

        if (!res.ok) {
            alert(data.error || "Error creating club")
            return;
        }

    } catch (error) {
        console.error(error);
        alert("Connection error")
    }

    navigateTo(`/club/${clubId}`)
}
</script>