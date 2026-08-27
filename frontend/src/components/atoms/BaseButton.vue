<script setup lang="ts">
import { ref } from 'vue'

withDefaults(defineProps<{
  type?: 'button' | 'submit'
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost'
  loading?: boolean
  disabled?: boolean
}>(), {
  type: 'button',
  variant: 'primary',
  loading: false,
  disabled: false,
})

const element = ref<HTMLButtonElement | null>(null)

defineExpose({ focus: () => element.value?.focus() })
</script>

<template>
  <button
    ref="element"
    class="base-button"
    :class="`base-button--${variant}`"
    :type="type"
    :disabled="disabled || loading"
    :aria-busy="loading"
  >
    <span v-if="loading" class="base-button__spinner" aria-hidden="true"></span>
    <slot />
  </button>
</template>

<style scoped src="./BaseButton.css"></style>
