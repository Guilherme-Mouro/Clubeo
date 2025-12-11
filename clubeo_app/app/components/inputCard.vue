<template>
    <input class="bg-custom-background p-2 rounded-xl mb-3 text-custom-first_text" :placeholder="value"></input>
    <slot />
</template>

<script setup>
defineProps(['value'])
</script>