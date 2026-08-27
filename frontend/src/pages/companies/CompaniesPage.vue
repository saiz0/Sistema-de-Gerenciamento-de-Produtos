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
import { companyApi } from '../../entities/company/api/companyApi'
import { companyActions } from '../../entities/company/model/policies'
import type { Company, CompanyFilters } from '../../entities/company/model/types'
import { productApi } from '../../entities/product/api/productApi'
import { getErrorMessage } from '../../shared/api/ApiError'
import type { PaginationMeta } from '../../shared/api/types'
import { notify } from '../../shared/model/toast'
import { formatCnpj, formatPhone } from '../../shared/utils/formatters'

type ConfirmAction = 'deactivate' | 'remove' | 'forceDelete'

const filters = reactive<CompanyFilters>({ name: '', status: '', deleted: 'without', page: 1, per_page: 15 })
const companies = ref<Company[]>([])
const meta = ref<PaginationMeta>({ current_page: 1, per_page: 15, total: 0, last_page: 1 })
const loading = ref(true)
const actionLoading = ref(false)
const error = ref('')
const hasProducts = reactive<Record<number, boolean | null>>({})
const confirmation = reactive<{ company?: Company; action?: ConfirmAction }>({})

async function loadCompanies(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    const response = await companyApi.list(filters)
    companies.value = response.data
    meta.value = response.meta
    await Promise.all(response.data.filter((company) => company.deleted_at).map(checkLinkedProducts))
  } catch (caught) { error.value = getErrorMessage(caught) }
  finally { loading.value = false }
}

async function checkLinkedProducts(company: Company): Promise<void> {
  hasProducts[company.id] = null
  try {
    const response = await productApi.list({ company_id: company.id, deleted: 'with', page: 1, per_page: 1 })
    hasProducts[company.id] = response.meta.total > 0
  } catch { hasProducts[company.id] = true }
}

function applyFilters(): void { filters.page = 1; void loadCompanies() }
function changePage(page: number): void { filters.page = page; void loadCompanies() }
function ask(company: Company, action: ConfirmAction): void { confirmation.company = company; confirmation.action = action }
function cancelConfirmation(): void { confirmation.company = undefined; confirmation.action = undefined }

async function run(company: Company, action: 'activate' | 'restore' | ConfirmAction): Promise<void> {
  actionLoading.value = true
  try {
    const response = await companyApi[action](company.id)
    notify(response.message)
    cancelConfirmation()
    await loadCompanies()
  } catch (caught) { notify(getErrorMessage(caught), 'error') }
  finally { actionLoading.value = false }
}

function confirmTitle(): string {
  if (confirmation.action === 'deactivate') return 'Inativar empresa?'
  if (confirmation.action === 'remove') return 'Excluir empresa?'
  return 'Excluir empresa definitivamente?'
}

function confirmMessage(): string {
  if (confirmation.action === 'deactivate') return 'Todos os produtos vinculados também serão inativados. Ao reativar a empresa, os produtos continuarão inativos.'
  if (confirmation.action === 'remove') return 'A empresa e seus produtos vinculados sairão das listagens padrão, mas poderão ser restaurados depois.'
  return 'Esta ação é irreversível. A empresa será removida permanentemente.'
}

onMounted(loadCompanies)
</script>

<template>
  <section class="companies-page">
    <PageHeader title="Empresas" description="Gerencie fornecedores, status operacional e registros excluídos.">
      <RouterLink class="companies-page__primary-link" to="/companies/new">Nova empresa</RouterLink>
    </PageHeader>

    <form class="companies-page__filters" aria-label="Filtros de empresas" @submit.prevent="applyFilters">
      <label>Nome<input v-model="filters.name" class="form-control" type="search" maxlength="150" placeholder="Buscar por nome" /></label>
      <label>Status<select v-model="filters.status" class="form-control"><option value="">Todos</option><option value="active">Ativo</option><option value="inactive">Inativo</option></select></label>
      <label>Registros<select v-model="filters.deleted" class="form-control"><option value="without">Não excluídos</option><option value="only">Somente excluídos</option><option value="with">Todos</option></select></label>
      <BaseButton type="submit" :loading="loading">Filtrar</BaseButton>
    </form>

    <LoadingSpinner v-if="loading && companies.length === 0" label="Carregando empresas" />
    <FeedbackState v-else-if="error" kind="error" title="Não foi possível carregar as empresas" :message="error" @retry="loadCompanies" />
    <FeedbackState v-else-if="companies.length === 0" title="Nenhuma empresa encontrada" message="Ajuste os filtros ou cadastre uma nova empresa." />
    <div v-else class="companies-page__table-wrap" :aria-busy="loading">
      <table>
        <caption class="sr-only">Empresas cadastradas</caption>
        <thead><tr><th>Empresa</th><th>Contato</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
          <tr v-for="company in companies" :key="company.id">
            <td data-label="Empresa"><strong>{{ company.name }}</strong><span>{{ formatCnpj(company.cnpj) }}</span></td>
            <td data-label="Contato"><span>{{ company.email }}</span><span>{{ formatPhone(company.phone) }}</span></td>
            <td data-label="Status"><StatusBadge :status="company.status" :deleted="Boolean(company.deleted_at)" /></td>
            <td data-label="Ações"><div class="companies-page__actions">
              <RouterLink v-if="companyActions(company).edit" :to="`/companies/${company.id}/edit`">Editar</RouterLink>
              <BaseButton v-if="companyActions(company).activate" variant="ghost" @click="run(company, 'activate')">Reativar</BaseButton>
              <BaseButton v-if="companyActions(company).deactivate" variant="ghost" @click="ask(company, 'deactivate')">Inativar</BaseButton>
              <BaseButton v-if="companyActions(company).remove" variant="ghost" @click="ask(company, 'remove')">Excluir</BaseButton>
              <BaseButton v-if="companyActions(company).restore" variant="secondary" @click="run(company, 'restore')">Restaurar</BaseButton>
              <BaseButton v-if="companyActions(company, hasProducts[company.id] ?? null).forceDelete" variant="danger" @click="ask(company, 'forceDelete')">Excluir definitivamente</BaseButton>
            </div></td>
          </tr>
        </tbody>
      </table>
    </div>
    <PaginationBar :current-page="meta.current_page" :last-page="meta.last_page" :total="meta.total" :loading="loading" @change="changePage" />

    <ConfirmDialog :open="Boolean(confirmation.action)" :title="confirmTitle()" :message="confirmMessage()" :confirm-label="confirmation.action === 'forceDelete' ? 'Excluir definitivamente' : 'Confirmar'" :danger="confirmation.action !== 'deactivate'" :loading="actionLoading" @cancel="cancelConfirmation" @confirm="confirmation.company && confirmation.action && run(confirmation.company, confirmation.action)" />
  </section>
</template>

<style scoped src="./CompaniesPage.css"></style>
