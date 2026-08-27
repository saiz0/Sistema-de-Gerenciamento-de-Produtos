<script setup lang="ts">
import { nextTick, ref, watch } from 'vue'

import BaseButton from '../atoms/BaseButton.vue'

const props = withDefaults(defineProps<{
  open: boolean
  title: string
  message: string
  confirmLabel?: string
  danger?: boolean
  loading?: boolean
}>(), { confirmLabel: 'Confirmar', danger: false, loading: false })

const emit = defineEmits<{ confirm: []; cancel: [] }>()
const confirmButton = ref<{ focus: () => void } | null>(null)

watch(() => props.open, async (open) => {
  if (open) {
    await nextTick()
    confirmButton.value?.focus()
  }
})
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="confirm-dialog" role="presentation" @click.self="emit('cancel')">
      <section class="confirm-dialog__panel" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-message" @keydown.esc="emit('cancel')">
        <h2 id="confirm-title">{{ title }}</h2>
        <p id="confirm-message">{{ message }}</p>
        <div class="confirm-dialog__actions">
          <BaseButton variant="secondary" :disabled="loading" @click="emit('cancel')">Cancelar</BaseButton>
          <BaseButton ref="confirmButton" :variant="danger ? 'danger' : 'primary'" :loading="loading" @click="emit('confirm')">{{ confirmLabel }}</BaseButton>
        </div>
      </section>
    </div>
  </Teleport>
</template>

<style scoped src="./ConfirmDialog.css"></style>
