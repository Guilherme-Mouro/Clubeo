<template>
    <img v-if="image" :src="image" class="avatar" />
    <img v-else src="https://github.com/benjamincanac.png" class="avatar" />
</template>

<script setup>
defineProps('image')
</script>

<style scoped>
.avatar {
    @apply w-12 h-12 rounded-full object-cover border border-custom-corners
}
</style>