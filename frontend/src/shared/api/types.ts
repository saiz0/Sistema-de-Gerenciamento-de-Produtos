export type Status = 'active' | 'inactive'
export type DeletedFilter = 'without' | 'only' | 'with'

export interface PaginationMeta {
  current_page: number
  per_page: number
  total: number
  last_page: number
}

export interface ApiSuccess<T> {
  success: true
  message: string
  data: T
}

export interface ApiListSuccess<T> extends ApiSuccess<T[]> {
  meta: PaginationMeta
}

export interface ApiMessageSuccess {
  success: true
  message: string
}

export interface ApiErrorBody {
  success: false
  message: string
  code?: string
  errors?: Record<string, string[]>
}

export interface ListFilters {
  name?: string
  status?: Status | ''
  deleted?: DeletedFilter
  page?: number
  per_page?: number
}
