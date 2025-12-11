<template>
    <div class="w-full max-w-md flex flex-col gap-4">

        <div class="flex flex-col items-center bg-custom-cards_menu p-8 rounded-xl shadow-lg w-full">
            <h1 class="text-2xl text-custom-first_text font-bold mb-6">{{ title }}</h1>

            <div class="w-full mb-4">
                <slot />
            </div>

            <button @click="$emit('login')"
                class="bg-custom-highlight w-full py-2 rounded-xl font-semibold hover:opacity-90 transition">
                {{ button }}
            </button>

        </div>
    </div>
</template>

<script setup>
defineProps(['title', 'button'])
defineEmits(['login'])
</script>