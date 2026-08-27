import { httpClient, type HttpClient } from '../../../shared/api/httpClient'
import type { ApiListSuccess, ApiMessageSuccess, ApiSuccess } from '../../../shared/api/types'
import type { Product, ProductFilters, ProductPayload } from '../model/types'

export function createProductApi(client: HttpClient) {
  return {
    list: (filters: ProductFilters = {}) =>
      client.get<ApiListSuccess<Product>>('/products', filters),
    get: (id: number) => client.get<ApiSuccess<Product>>(`/products/${id}`),
    create: (payload: ProductPayload) =>
      client.post<ApiSuccess<Product>>('/products', payload),
    update: (id: number, payload: Omit<ProductPayload, 'status'>) =>
      client.put<ApiSuccess<Product>>(`/products/${id}`, payload),
    activate: (id: number) => client.patch<ApiSuccess<Product>>(`/products/${id}/activate`),
    deactivate: (id: number) => client.patch<ApiSuccess<Product>>(`/products/${id}/deactivate`),
    remove: (id: number) => client.delete<ApiMessageSuccess>(`/products/${id}`),
    restore: (id: number) => client.post<ApiSuccess<Product>>(`/products/${id}/restore`),
    forceDelete: (id: number) =>
      client.delete<ApiMessageSuccess>(`/products/${id}/force`, { confirmed: true }),
  }
}

export const productApi = createProductApi(httpClient)
