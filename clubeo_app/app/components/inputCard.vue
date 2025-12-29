<template>
    <div class="relative flex items-center mb-3">
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <XCircleIcon v-if="modelValue && hasError" class="w-5 h-5 text-custom-error" />
            <CheckCircleIcon v-if="modelValue && !hasError" class="w-5 h-5 text-custom-success" />
        </div>

        <input :type="type" :placeholder="placeholder" :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            class="bg-custom-background p-2 pr-10 rounded-xl w-full text-custom-first_text outline-none">
    </div>
    <slot />
</template>

<script setup>
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/16/solid'

defineProps({
    modelValue: String,
    placeholder: String,
    hasError: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'text'
    }
})

defineEmits(['update:modelValue', 'change'])

const handleInput = (event) => {
    const value = event.target.value
    emit('update:modelValue', value)
    emit('change', value)
}
</script>