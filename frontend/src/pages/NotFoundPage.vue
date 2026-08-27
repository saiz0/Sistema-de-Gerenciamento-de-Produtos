<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section class="not-found-page">
    <p>Erro 404</p>
    <h1>Página não encontrada</h1>
    <span>O endereço informado não existe ou foi removido.</span>
    <RouterLink to="/">Voltar ao início</RouterLink>
  </section>
</template>

<style scoped src="./NotFoundPage.css"></style>
