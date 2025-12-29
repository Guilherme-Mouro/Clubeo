<template>
    <div class="flex flex-row items-center bg-custom-error rounded-lg p-2 mb-4">
        <ExclamationTriangleIcon class="w-5 h-5 text-white" />
        <span class="ml-2 font-bold text-custom-first_text">{{ message }}</span>
    </div>
</template>

<script setup>
import { ExclamationTriangleIcon } from '@heroicons/vue/16/solid'

defineProps({
    message: ''
})
</script>