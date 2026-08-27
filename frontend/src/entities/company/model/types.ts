import type { DeletedFilter, ListFilters, Status } from '../../../shared/api/types'

export interface Company {
  id: number
  name: string
  cnpj: string
  email: string
  phone: string
  status: Status
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
}

export interface CompanyPayload {
  name: string
  cnpj: string
  email: string
  phone: string
  status?: Status
}

export interface CompanyFilters extends ListFilters {
  deleted?: DeletedFilter
}
