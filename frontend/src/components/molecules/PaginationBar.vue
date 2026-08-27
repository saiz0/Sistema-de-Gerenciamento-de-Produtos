<script setup lang="ts">
import BaseButton from '../atoms/BaseButton.vue'

const props = defineProps<{ currentPage: number; lastPage: number; total: number; loading?: boolean }>()
defineEmits<{ change: [page: number] }>()
</script>

<template>
  <nav v-if="total > 0" class="pagination-bar" aria-label="Paginação">
    <p>{{ total }} {{ total === 1 ? 'registro' : 'registros' }}</p>
    <div class="pagination-bar__actions">
      <BaseButton variant="secondary" :disabled="currentPage <= 1 || loading" @click="$emit('change', currentPage - 1)">Anterior</BaseButton>
      <span>Página {{ currentPage }} de {{ Math.max(lastPage, 1) }}</span>
      <BaseButton variant="secondary" :disabled="currentPage >= lastPage || loading" @click="$emit('change', currentPage + 1)">Próxima</BaseButton>
    </div>
  </nav>
</template>

<style scoped src="./PaginationBar.css"></style>
