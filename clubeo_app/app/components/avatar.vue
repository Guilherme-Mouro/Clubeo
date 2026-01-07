<template>
  <div v-bind="$attrs">
    <img 
      v-if="image" 
      :src="image" 
      class="avatar-img" 
    />
    <img 
      v-else 
      src="/default-account-banner.png"
      class="avatar-img" 
    />
  </div>
</template>

<script setup>
defineProps(['image'])
defineOptions({ inheritAttrs: false })
</script>

<style scoped>
.avatar-img {
  @apply w-full h-full rounded-full object-cover border-2 border-custom-first_text shadow-sm;
}
</style>