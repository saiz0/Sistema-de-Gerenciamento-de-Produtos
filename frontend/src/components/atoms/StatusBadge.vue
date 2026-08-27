<script setup lang="ts">
import type { Status } from '../../shared/api/types'

const props = defineProps<{ status: Status; deleted?: boolean }>()
</script>

<template>
  <span class="status-badge" :class="`status-badge--${props.deleted ? 'deleted' : status}`">
    {{ props.deleted ? 'Excluído' : status === 'active' ? 'Ativo' : 'Inativo' }}
  </span>
</template>

<style scoped src="./StatusBadge.css"></style>
