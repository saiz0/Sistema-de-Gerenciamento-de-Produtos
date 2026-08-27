<script setup lang="ts">
import { reactive, watch } from 'vue'

import type { Company, CompanyPayload } from '../../entities/company/model/types'
import { validateCompany } from '../../entities/company/model/validation'
import { formatCnpj, formatPhone } from '../../shared/utils/formatters'
import BaseButton from '../atoms/BaseButton.vue'
import FormField from '../molecules/FormField.vue'

const props = withDefaults(defineProps<{
  company?: Company
  saving?: boolean
  serverErrors?: Record<string, string>
}>(), { saving: false, serverErrors: () => ({}) })

const emit = defineEmits<{ submit: [payload: CompanyPayload] }>()

const form = reactive<CompanyPayload>({ name: '', cnpj: '', email: '', phone: '', status: 'active' })
const errors = reactive<Record<string, string>>({})

watch(() => props.company, (company) => {
  if (!company) return
  Object.assign(form, { name: company.name, cnpj: formatCnpj(company.cnpj), email: company.email, phone: formatPhone(company.phone), status: company.status })
}, { immediate: true })

watch(() => props.serverErrors, (serverErrors) => Object.assign(errors, serverErrors), { deep: true })

function setCnpj(event: Event): void {
  form.cnpj = formatCnpj((event.target as HTMLInputElement).value)
}

function setPhone(event: Event): void {
  form.phone = formatPhone((event.target as HTMLInputElement).value)
}

function submit(): void {
  Object.keys(errors).forEach((key) => delete errors[key])
  Object.assign(errors, validateCompany(form))
  if (Object.keys(errors).length > 0) return
  emit('submit', { ...form, name: form.name.trim(), email: form.email.trim().toLowerCase() })
}
</script>

<template>
  <form class="company-form" novalidate @submit.prevent="submit">
    <div class="form-grid">
      <FormField class="form-grid__full" label="Nome" for-id="company-name" :error="errors.name" required>
        <template #default="{ describedBy }"><input id="company-name" v-model="form.name" class="form-control" type="text" maxlength="150" autocomplete="organization" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.name)" /></template>
      </FormField>
      <FormField label="CNPJ" for-id="company-cnpj" :error="errors.cnpj" hint="Pode ser informado com ou sem máscara." required>
        <template #default="{ describedBy }"><input id="company-cnpj" :value="form.cnpj" class="form-control" type="text" inputmode="numeric" maxlength="18" autocomplete="off" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.cnpj)" @input="setCnpj" /></template>
      </FormField>
      <FormField label="Telefone" for-id="company-phone" :error="errors.phone" hint="Informe o DDD e o número." required>
        <template #default="{ describedBy }"><input id="company-phone" :value="form.phone" class="form-control" type="tel" maxlength="15" autocomplete="tel" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.phone)" @input="setPhone" /></template>
      </FormField>
      <FormField class="form-grid__full" label="Email" for-id="company-email" :error="errors.email" required>
        <template #default="{ describedBy }"><input id="company-email" v-model="form.email" class="form-control" type="email" maxlength="254" autocomplete="email" :aria-describedby="describedBy" :aria-invalid="Boolean(errors.email)" /></template>
      </FormField>
      <FormField v-if="!company" label="Status inicial" for-id="company-status" :error="errors.status" required>
        <template #default="{ describedBy }"><select id="company-status" v-model="form.status" class="form-control" :aria-describedby="describedBy"><option value="active">Ativo</option><option value="inactive">Inativo</option></select></template>
      </FormField>
    </div>
    <div class="company-form__actions">
      <RouterLink class="company-form__cancel" to="/companies">Cancelar</RouterLink>
      <BaseButton type="submit" :loading="saving">{{ company ? 'Salvar alterações' : 'Cadastrar empresa' }}</BaseButton>
    </div>
  </form>
</template>

<style scoped src="./CompanyForm.css"></style>
