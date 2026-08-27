<script setup lang="ts">
import BaseButton from '../atoms/BaseButton.vue'

withDefaults(defineProps<{ title: string; message?: string; kind?: 'empty' | 'error' }>(), { kind: 'empty' })
defineEmits<{ retry: [] }>()
</script>

<template>
  <section class="feedback-state" :class="`feedback-state--${kind}`" role="status">
    <h2>{{ title }}</h2>
    <p v-if="message">{{ message }}</p>
    <BaseButton v-if="kind === 'error'" variant="secondary" @click="$emit('retry')">Tentar novamente</BaseButton>
  </section>
</template>

<style scoped src="./FeedbackState.css"></style>
