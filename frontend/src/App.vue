<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div class="app-shell">
    <header class="app-shell__header">
      <RouterLink class="app-shell__brand" to="/">
        <span class="app-shell__brand-mark" aria-hidden="true"></span>
        <span>Sistema de Produtos</span>
      </RouterLink>
    </header>

    <main class="app-shell__content">
      <RouterView />
    </main>
  </div>
</template>

<style scoped src="./App.css"></style>
