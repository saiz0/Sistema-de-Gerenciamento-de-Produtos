<script setup lang="ts">
withDefaults(defineProps<{ label?: string }>(), { label: 'Carregando' })
</script>

<template>
  <div class="loading-spinner" role="status">
    <span class="loading-spinner__icon" aria-hidden="true"></span>
    <span>{{ label }}</span>
  </div>
</template>

<style scoped src="./LoadingSpinner.css"></style>
