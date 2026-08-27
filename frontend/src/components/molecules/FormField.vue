<script setup lang="ts">
defineProps<{ label: string; forId: string; error?: string; hint?: string; required?: boolean }>()
</script>

<template>
  <div class="form-field" :class="{ 'form-field--invalid': error }">
    <label class="form-field__label" :for="forId">
      {{ label }} <span v-if="required" aria-hidden="true">*</span>
    </label>
    <slot :described-by="error ? `${forId}-error` : hint ? `${forId}-hint` : undefined" />
    <p v-if="hint && !error" :id="`${forId}-hint`" class="form-field__hint">{{ hint }}</p>
    <p v-if="error" :id="`${forId}-error`" class="form-field__error" role="alert">{{ error }}</p>
  </div>
</template>

<style scoped src="./FormField.css"></style>
