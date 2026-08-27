import type { DeletedFilter, Status } from '../../../shared/api/types'
import { companyApi } from './companyApi'
import type { Company } from '../model/types'

export async function listAllCompanies(filters: { status?: Status; deleted?: DeletedFilter } = {}): Promise<Company[]> {
  const companies: Company[] = []
  let page = 1
  let lastPage = 1

  do {
    const response = await companyApi.list({ ...filters, page, per_page: 100 })
    companies.push(...response.data)
    lastPage = response.meta.last_page
    page += 1
  } while (page <= lastPage)

  return companies
}
