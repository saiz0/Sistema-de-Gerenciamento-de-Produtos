import type { Company } from '../../company/model/types'
import type { Product } from './types'

export function productActions(product: Product, company?: Company) {
  const deleted = product.deleted_at !== null
  const companyAvailable = company?.deleted_at === null && company.status === 'active'

  return {
    edit: !deleted && companyAvailable,
    activate: !deleted && product.status === 'inactive' && companyAvailable,
    deactivate: !deleted && product.status === 'active',
    remove: !deleted,
    restore: deleted && company?.deleted_at === null,
    forceDelete: deleted,
  }
}
