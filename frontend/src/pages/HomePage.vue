<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section class="home-page" aria-labelledby="home-title">
    <div class="home-page__intro">
      <p class="home-page__eyebrow">Painel de gestão</p>
      <h1 id="home-title">Empresas e produtos</h1>
      <p class="home-page__description">Gerencie empresas e seus produtos em um único lugar.</p>
    </div>
    <nav class="home-page__cards" aria-label="Áreas do sistema">
      <RouterLink to="/companies"><strong>Empresas</strong><span>Cadastros, status e exclusões</span></RouterLink>
      <RouterLink to="/products"><strong>Produtos</strong><span>Catálogo e vínculos com empresas</span></RouterLink>
    </nav>
  </section>
</template>

<style scoped src="./HomePage.css"></style>
