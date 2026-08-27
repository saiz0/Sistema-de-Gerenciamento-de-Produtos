<script setup lang="ts">
import type { Status } from '../../shared/api/types'

const props = defineProps<{ status: Status; deleted?: boolean }>()
</script>

<template>
  <span class="status-badges">
    <span class="status-badge" :class="`status-badge--${status}`">{{ status === 'active' ? 'Ativo' : 'Inativo' }}</span>
    <span v-if="props.deleted" class="status-badge status-badge--deleted">Excluído</span>
  </span>
</template>

<style scoped src="./StatusBadge.css"></style>
