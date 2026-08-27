import type { Company } from './types'

export function companyActions(company: Company, hasProducts: boolean | null = null) {
  const deleted = company.deleted_at !== null

  return {
    edit: !deleted,
    activate: !deleted && company.status === 'inactive',
    deactivate: !deleted && company.status === 'active',
    remove: !deleted,
    restore: deleted,
    forceDelete: deleted && hasProducts === false,
  }
}

export function canReceiveProducts(company: Company): boolean {
  return company.deleted_at === null && company.status === 'active'
}
