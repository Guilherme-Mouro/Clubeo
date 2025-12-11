<template>
    <input 
        :type="type"
        :placeholder="placeholder"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="bg-custom-background p-2 rounded-xl mb-3 text-custom-first_text">
    </input>
    <slot />
</template>

<script setup>
defineProps({
    modelValue: String,
    placeholder: String,
    type: {
        type: String,
        default: 'text'
    }
})

defineEmits(['update:modelValue'])
</script>