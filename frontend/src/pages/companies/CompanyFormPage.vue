<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import CompanyForm from '../../components/organisms/CompanyForm.vue'
import FeedbackState from '../../components/molecules/FeedbackState.vue'
import PageHeader from '../../components/molecules/PageHeader.vue'
import LoadingSpinner from '../../components/atoms/LoadingSpinner.vue'
import { companyApi } from '../../entities/company/api/companyApi'
import type { Company, CompanyPayload } from '../../entities/company/model/types'
import { ApiError, getErrorMessage } from '../../shared/api/ApiError'
import { notify } from '../../shared/model/toast'

const route = useRoute()
const router = useRouter()
const id = computed(() => Number(route.params.id || 0))
const editing = computed(() => id.value > 0)
const company = ref<Company>()
const loading = ref(editing.value)
const saving = ref(false)
const error = ref('')
const serverErrors = ref<Record<string, string>>({})

async function load(): Promise<void> {
  if (!editing.value) return
  loading.value = true
  error.value = ''
  try { company.value = (await companyApi.get(id.value)).data }
  catch (caught) { error.value = getErrorMessage(caught) }
  finally { loading.value = false }
}

async function save(payload: CompanyPayload): Promise<void> {
  saving.value = true
  serverErrors.value = {}
  try {
    const response = editing.value
      ? await companyApi.update(id.value, { name: payload.name, cnpj: payload.cnpj, email: payload.email, phone: payload.phone })
      : await companyApi.create(payload)
    notify(response.message)
    await router.push('/companies')
  } catch (caught) {
    if (caught instanceof ApiError) {
      serverErrors.value = Object.fromEntries(Object.entries(caught.fieldErrors).map(([field, messages]) => [field, messages[0] ?? caught.message]))
    }
    notify(getErrorMessage(caught), 'error')
  } finally { saving.value = false }
}

onMounted(load)
</script>

<template>
  <section class="company-form-page">
    <PageHeader :title="editing ? 'Editar empresa' : 'Nova empresa'" :description="editing ? 'Atualize os dados cadastrais. O status é alterado na listagem.' : 'Cadastre uma empresa fornecedora.'" />
    <LoadingSpinner v-if="loading" label="Carregando empresa" />
    <FeedbackState v-else-if="error" kind="error" title="Não foi possível carregar a empresa" :message="error" @retry="load" />
    <CompanyForm v-else :company="company" :saving="saving" :server-errors="serverErrors" @submit="save" />
  </section>
</template>

<style scoped src="./CompanyFormPage.css"></style>
