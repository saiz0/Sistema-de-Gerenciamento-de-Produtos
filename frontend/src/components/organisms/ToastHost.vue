<script setup lang="ts">
import { dismissToast, toastMessages } from '../../shared/model/toast'
</script>

<template>
  <aside class="toast-host" aria-live="polite" aria-atomic="false">
    <article v-for="toast in toastMessages" :key="toast.id" class="toast-host__item" :class="`toast-host__item--${toast.kind}`">
      <p>{{ toast.message }}</p>
      <button type="button" aria-label="Fechar mensagem" @click="dismissToast(toast.id)">×</button>
    </article>
  </aside>
</template>

<style scoped src="./ToastHost.css"></style>
