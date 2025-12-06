<template>
    <div class="bg-custom-cards_menu">
        <h1>{{ title }}</h1>
        <div>
            <slot />
        </div>
        <button @click="$emit('login')" class="bg-custom-highlight">{{ button }}</button>
        <NuxtLink to="/register"></NuxtLink>
    </div>
</template>

<script setup>
defineProps(['title', 'button'])
defineEmits(['login'])
</script>