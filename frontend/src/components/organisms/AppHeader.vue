<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <header class="app-header">
    <RouterLink class="app-header__brand" to="/" aria-label="Horizon Tecnologia - início">
      <img src="/horizon-logo.png" alt="Horizon Tecnologia" />
    </RouterLink>
    <nav aria-label="Navegação principal">
      <RouterLink to="/companies">Empresas</RouterLink>
      <RouterLink to="/products">Produtos</RouterLink>
    </nav>
  </header>
</template>

<style scoped src="./AppHeader.css"></style>
