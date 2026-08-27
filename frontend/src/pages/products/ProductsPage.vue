<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { RouterLink } from 'vue-router'

import BaseButton from '../../components/atoms/BaseButton.vue'
import LoadingSpinner from '../../components/atoms/LoadingSpinner.vue'
import StatusBadge from '../../components/atoms/StatusBadge.vue'
import ConfirmDialog from '../../components/molecules/ConfirmDialog.vue'
import FeedbackState from '../../components/molecules/FeedbackState.vue'
import PageHeader from '../../components/molecules/PageHeader.vue'
import PaginationBar from '../../components/molecules/PaginationBar.vue'
import { listAllCompanies } from '../../entities/company/api/listAllCompanies'
import type { Company } from '../../entities/company/model/types'
import { productApi } from '../../entities/product/api/productApi'
import { productActions } from '../../entities/product/model/policies'
import type { Product, ProductFilters } from '../../entities/product/model/types'
import { getErrorMessage } from '../../shared/api/ApiError'
import type { PaginationMeta } from '../../shared/api/types'
import { notify } from '../../shared/model/toast'
import { formatCurrency } from '../../shared/utils/formatters'

type ConfirmAction = 'remove' | 'forceDelete'

const filters = reactive<ProductFilters>({ name: '', status: '', company_id: '', deleted: 'without', page: 1, per_page: 15 })
const products = ref<Product[]>([])
const companies = ref<Company[]>([])
const companyById = reactive<Record<number, Company>>({})
const meta = ref<PaginationMeta>({ current_page: 1, per_page: 15, total: 0, last_page: 1 })
const loading = ref(true)
const actionLoading = ref(false)
const error = ref('')
const confirmation = reactive<{ product?: Product; action?: ConfirmAction }>({})

async function loadProducts(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    const [response, allCompanies] = await Promise.all([
      productApi.list(filters),
      listAllCompanies({ deleted: 'with' }),
    ])
    products.value = response.data
    meta.value = response.meta
    companies.value = allCompanies
    Object.keys(companyById).forEach((key) => delete companyById[Number(key)])
    allCompanies.forEach((company) => { companyById[company.id] = company })
  } catch (caught) { error.value = getErrorMessage(caught) }
  finally { loading.value = false }
}

function applyFilters(): void { filters.page = 1; void loadProducts() }
function changePage(page: number): void { filters.page = page; void loadProducts() }
function ask(product: Product, action: ConfirmAction): void { confirmation.product = product; confirmation.action = action }
function cancelConfirmation(): void { confirmation.product = undefined; confirmation.action = undefined }

async function run(product: Product, action: 'activate' | 'deactivate' | 'restore' | ConfirmAction): Promise<void> {
  actionLoading.value = true
  try {
    const response = await productApi[action](product.id)
    notify(response.message)
    cancelConfirmation()
    await loadProducts()
  } catch (caught) { notify(getErrorMessage(caught), 'error') }
  finally { actionLoading.value = false }
}

onMounted(loadProducts)
</script>

<template>
  <section class="products-page">
    <PageHeader title="Produtos" description="Gerencie o catálogo e os vínculos com empresas fornecedoras.">
      <RouterLink class="products-page__primary-link" to="/products/new">Novo produto</RouterLink>
    </PageHeader>

    <form class="products-page__filters" aria-label="Filtros de produtos" @submit.prevent="applyFilters">
      <label>Nome<input v-model="filters.name" class="form-control" type="search" maxlength="150" placeholder="Buscar por nome" /></label>
      <label>Status<select v-model="filters.status" class="form-control"><option value="">Todos</option><option value="active">Ativo</option><option value="inactive">Inativo</option></select></label>
      <label>Empresa<select v-model="filters.company_id" class="form-control"><option value="">Todas</option><option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option></select></label>
      <label>Registros<select v-model="filters.deleted" class="form-control"><option value="without">Não excluídos</option><option value="only">Somente excluídos</option><option value="with">Todos</option></select></label>
      <BaseButton type="submit" :loading="loading">Filtrar</BaseButton>
    </form>

    <LoadingSpinner v-if="loading && products.length === 0" label="Carregando produtos" />
    <FeedbackState v-else-if="error" kind="error" title="Não foi possível carregar os produtos" :message="error" @retry="loadProducts" />
    <FeedbackState v-else-if="products.length === 0" title="Nenhum produto encontrado" message="Ajuste os filtros ou cadastre um novo produto." />
    <div v-else class="products-page__table-wrap" :aria-busy="loading">
      <table>
        <caption class="sr-only">Produtos cadastrados</caption>
        <thead><tr><th>Produto</th><th>Empresa</th><th>Preço</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td data-label="Produto"><strong>{{ product.name }}</strong><span>{{ product.internal_code }}</span></td>
            <td data-label="Empresa"><span>{{ companyById[product.company_id]?.name ?? `Empresa #${product.company_id}` }}</span></td>
            <td data-label="Preço"><span>{{ formatCurrency(product.price) }}</span></td>
            <td data-label="Status"><StatusBadge :status="product.status" :deleted="Boolean(product.deleted_at)" /></td>
            <td data-label="Ações"><div class="products-page__actions">
              <RouterLink v-if="productActions(product, companyById[product.company_id]).edit" :to="`/products/${product.id}/edit`">Editar</RouterLink>
              <BaseButton v-if="productActions(product, companyById[product.company_id]).activate" variant="ghost" @click="run(product, 'activate')">Reativar</BaseButton>
              <BaseButton v-if="productActions(product, companyById[product.company_id]).deactivate" variant="ghost" @click="run(product, 'deactivate')">Inativar</BaseButton>
              <BaseButton v-if="productActions(product, companyById[product.company_id]).remove" variant="ghost" @click="ask(product, 'remove')">Excluir</BaseButton>
              <BaseButton v-if="productActions(product, companyById[product.company_id]).restore" variant="secondary" @click="run(product, 'restore')">Restaurar</BaseButton>
              <BaseButton v-if="productActions(product, companyById[product.company_id]).forceDelete" variant="danger" @click="ask(product, 'forceDelete')">Excluir definitivamente</BaseButton>
            </div></td>
          </tr>
        </tbody>
      </table>
    </div>
    <PaginationBar :current-page="meta.current_page" :last-page="meta.last_page" :total="meta.total" :loading="loading" @change="changePage" />

    <ConfirmDialog :open="Boolean(confirmation.action)" :title="confirmation.action === 'forceDelete' ? 'Excluir produto definitivamente?' : 'Excluir produto?'" :message="confirmation.action === 'forceDelete' ? 'Esta ação é irreversível. O produto será removido permanentemente.' : 'O produto sairá das listagens padrão, mas poderá ser restaurado depois.'" :confirm-label="confirmation.action === 'forceDelete' ? 'Excluir definitivamente' : 'Excluir'" danger :loading="actionLoading" @cancel="cancelConfirmation" @confirm="confirmation.product && confirmation.action && run(confirmation.product, confirmation.action)" />
  </section>
</template>

<style scoped src="./ProductsPage.css"></style>
