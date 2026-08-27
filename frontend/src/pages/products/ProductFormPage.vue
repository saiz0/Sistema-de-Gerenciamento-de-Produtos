<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import LoadingSpinner from '../../components/atoms/LoadingSpinner.vue'
import FeedbackState from '../../components/molecules/FeedbackState.vue'
import PageHeader from '../../components/molecules/PageHeader.vue'
import ProductForm from '../../components/organisms/ProductForm.vue'
import { listAllCompanies } from '../../entities/company/api/listAllCompanies'
import type { Company } from '../../entities/company/model/types'
import { productApi } from '../../entities/product/api/productApi'
import type { Product, ProductPayload } from '../../entities/product/model/types'
import { ApiError, getErrorMessage } from '../../shared/api/ApiError'
import { notify } from '../../shared/model/toast'

const route = useRoute()
const router = useRouter()
const id = computed(() => Number(route.params.id || 0))
const editing = computed(() => id.value > 0)
const product = ref<Product>()
const companies = ref<Company[]>([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const serverErrors = ref<Record<string, string>>({})

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    const [availableCompanies, productResponse] = await Promise.all([
      listAllCompanies({ status: 'active', deleted: 'without' }),
      editing.value ? productApi.get(id.value) : Promise.resolve(undefined),
    ])
    companies.value = availableCompanies
    product.value = productResponse?.data

    if (product.value && !companies.value.some((company) => company.id === product.value?.company_id)) {
      error.value = 'Este produto não pode ser editado porque a empresa vinculada está inativa ou excluída.'
    }
  } catch (caught) { error.value = getErrorMessage(caught) }
  finally { loading.value = false }
}

async function save(payload: ProductPayload): Promise<void> {
  saving.value = true
  serverErrors.value = {}
  try {
    const data = { company_id: payload.company_id, name: payload.name, description: payload.description, price: payload.price, internal_code: payload.internal_code }
    const response = editing.value ? await productApi.update(id.value, data) : await productApi.create(payload)
    notify(response.message)
    await router.push('/products')
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
  <section class="product-form-page">
    <PageHeader :title="editing ? 'Editar produto' : 'Novo produto'" :description="editing ? 'Atualize os dados do produto. O status é alterado na listagem.' : 'Cadastre um produto vinculado a uma empresa apta.'" />
    <LoadingSpinner v-if="loading" label="Carregando formulário" />
    <FeedbackState v-else-if="error" kind="error" title="Produto indisponível para edição" :message="error" @retry="load" />
    <FeedbackState v-else-if="companies.length === 0" title="Nenhuma empresa apta" message="Cadastre ou reative uma empresa antes de criar produtos." />
    <ProductForm v-else :product="product" :companies="companies" :saving="saving" :server-errors="serverErrors" @submit="save" />
  </section>
</template>

<style scoped src="./ProductFormPage.css"></style>
