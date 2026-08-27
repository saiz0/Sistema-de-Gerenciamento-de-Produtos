import { httpClient, type HttpClient } from '../../../shared/api/httpClient'
import type { ApiListSuccess, ApiMessageSuccess, ApiSuccess } from '../../../shared/api/types'
import type { Company, CompanyFilters, CompanyPayload } from '../model/types'

export function createCompanyApi(client: HttpClient) {
  return {
    list: (filters: CompanyFilters = {}) =>
      client.get<ApiListSuccess<Company>>('/companies', filters),
    get: (id: number) => client.get<ApiSuccess<Company>>(`/companies/${id}`),
    create: (payload: CompanyPayload) =>
      client.post<ApiSuccess<Company>>('/companies', payload),
    update: (id: number, payload: Omit<CompanyPayload, 'status'>) =>
      client.put<ApiSuccess<Company>>(`/companies/${id}`, payload),
    activate: (id: number) => client.patch<ApiSuccess<Company>>(`/companies/${id}/activate`),
    deactivate: (id: number) => client.patch<ApiSuccess<Company>>(`/companies/${id}/deactivate`),
    remove: (id: number) => client.delete<ApiMessageSuccess>(`/companies/${id}`),
    restore: (id: number) => client.post<ApiSuccess<Company>>(`/companies/${id}/restore`),
    forceDelete: (id: number) =>
      client.delete<ApiMessageSuccess>(`/companies/${id}/force`, { confirmed: true }),
  }
}

export const companyApi = createCompanyApi(httpClient)
