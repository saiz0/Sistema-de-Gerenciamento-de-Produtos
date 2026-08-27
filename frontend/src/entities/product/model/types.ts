import type { DeletedFilter, ListFilters, Status } from '../../../shared/api/types'

export interface Product {
  id: number
  company_id: number
  name: string
  description: string | null
  price: string
  internal_code: string
  status: Status
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
}

export interface ProductPayload {
  company_id: number
  name: string
  description: string | null
  price: string
  internal_code: string
  status?: Status
}

export interface ProductFilters extends ListFilters {
  company_id?: number | ''
  deleted?: DeletedFilter
}
