<script setup lang="ts">
import { computed, reactive, watch } from 'vue'

import type { Company } from '../../entities/company/model/types'
import type { Product, ProductPayload } from '../../entities/product/model/types'
import { validateProduct } from '../../entities/product/model/validation'
import BaseButton from '../atoms/BaseButton.vue'
import FormField from '../molecules/FormField.vue'

const props = withDefaults(defineProps<{
  product?: Product
  companies: Company[]
  saving?: boolean
  serverErrors?: Record<string, string>
}>(), { saving: false, serverErrors: () => ({}) })

const emit = defineEmits<{ submit: [payload: ProductPayload] }>()
const form = reactive<ProductPayload>({ company_id: 0, name: '', description: null, price: '', internal_code: '', status: 'active' })
const errors = reactive<Record<string, string>>({})
const descriptionLength = computed(() => form.description?.length ?? 0)

watch(() => props.product, (product) => {
  if (!product) return
  Object.assign(form, {
    company_id: product.company_id,
    name: product.name,
    description: product.description,
    price: product.price.replace('.', ','),
    internal_code: product.internal_code,
    status: product.status,
  })
}, { immediate: true })

watch(() => props.serverErrors, (serverErrors) => Object.assign(errors, serverErrors), { deep: true })

function setDescription(event: Event): void {
  const value = (event.target as HTMLTextAreaElement).value
  form.description = value || null
}

function submit(): void {
  Object.keys(errors).forEach((key) => delete errors[key])
  Object.assign(errors, validateProduct(form))
  if (Object.keys(errors).length > 0) return

  emit('submit', {
    ...form,
    name: form.name.trim(),
    description: form.description?.trim() || null,
    price: form.price.trim().replace(',', '.'),
    internal_code: form.internal_code.trim(),
  })
}
</script>

<template>
  <form class="product-form" novalidate @submit.prevent="submit">
    <div class="form-grid">
      <FormField class="form-grid__full" label="Empresa" for-id="product-company" :error="errors.company_id" hint="Somente empresas ativas e não excluídas podem receber produtos." required>
        <template #default="{ describedBy }"><select id="product-company" v-model.number="form.company_id" class="form-control" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.company_id)"><option :value="0" disabled>Selecione uma empresa</option><option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option></select></template>
      </FormField>
      <FormField class="form-grid__full" label="Nome" for-id="product-name" :error="errors.name" required>
        <template #default="{ describedBy }"><input id="product-name" v-model="form.name" class="form-control" type="text" maxlength="150" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.name)" /></template>
      </FormField>
      <FormField label="Preço" for-id="product-price" :error="errors.price" hint="Valor maior que zero, com até duas casas decimais." required>
        <template #default="{ describedBy }"><input id="product-price" v-model="form.price" class="form-control" type="text" inputmode="decimal" placeholder="0,00" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.price)" /></template>
      </FormField>
      <FormField label="Código interno" for-id="product-code" :error="errors.internal_code" hint="Deve ser único dentro da empresa." required>
        <template #default="{ describedBy }"><input id="product-code" v-model="form.internal_code" class="form-control" type="text" maxlength="100" autocomplete="off" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.internal_code)" /></template>
      </FormField>
      <FormField class="form-grid__full" label="Descrição" for-id="product-description" :error="errors.description" :hint="`${descriptionLength}/2000 caracteres`">
        <template #default="{ describedBy }"><textarea id="product-description" :value="form.description ?? ''" class="form-control" maxlength="2000" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.description)" @input="setDescription"></textarea></template>
      </FormField>
      <FormField v-if="!product" label="Status inicial" for-id="product-status" :error="errors.status" required>
        <template #default="{ describedBy }"><select id="product-status" v-model="form.status" class="form-control" :aria-describedby="describedBy"><option value="active">Ativo</option><option value="inactive">Inativo</option></select></template>
      </FormField>
    </div>
    <div class="product-form__actions">
      <RouterLink class="product-form__cancel" to="/products">Cancelar</RouterLink>
      <BaseButton type="submit" :loading="saving">{{ product ? 'Salvar alterações' : 'Cadastrar produto' }}</BaseButton>
    </div>
  </form>
</template>

<style scoped src="./ProductForm.css"></style>
