<template>
    <div class="flex flex-row items-center bg-custom-cards_menu rounded-lg p-2 mb-5 hover:bg-custom-corners">
        <Avatar />
        <div class="flex flex-col ml-4">
            <h3 class="text-custom-highlight font-bold text-3xl">{{ name }}</h3>
            <div class="flex flex-wrap items-center gap-2 text-custom-first_text opacity-90">
                <p><strong>32.156</strong> members</p>
                <span class="hidden md:inline">•</span>
                <p class="italic">{{ description }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps(['name', 'description'])
</script>