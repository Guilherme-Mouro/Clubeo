<template>
    <div class="flex flex-row items-center bg-custom-cards_menu rounded-lg p-2 mb-5 hover:bg-custom-corners">
        <Avatar />
        <div class="flex flex-col ml-4">
            <h3 class="text-custom-highlight font-bold text-3xl">{{ name }}</h3>
            <p class="text-custom-first_text">{{ description }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps(['name', 'description'])
</script>