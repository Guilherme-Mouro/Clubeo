<template>
    <form>
        <input class="bg-custom-cards_menu text-custom-first_text rounded-lg p-2" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)" type="text" placeholder="Search for Clubs...">
    </form>
</template>

<script setup>
defineProps(['modelValue'])
defineEmits(['update:modelValue'])
</script>