<script setup lang="ts">
import { RouterView } from 'vue-router'

import AppHeader from './components/organisms/AppHeader.vue'
import ToastHost from './components/organisms/ToastHost.vue'
</script>

<template>
  <div class="app-shell">
    <a class="app-shell__skip-link" href="#main-content">Pular para o conteúdo</a>
    <AppHeader />

    <main id="main-content" class="app-shell__content" tabindex="-1">
      <RouterView />
    </main>
    <ToastHost />
  </div>
</template>

<style scoped src="./App.css"></style>
