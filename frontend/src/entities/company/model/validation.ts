import { isValidCnpj, isValidEmail, isValidPhone, lengthBetween, required, type FieldErrors } from '../../../shared/validation/validators'
import type { CompanyPayload } from './types'

type CompanyField = 'name' | 'cnpj' | 'email' | 'phone' | 'status'

export function validateCompany(payload: CompanyPayload): FieldErrors<CompanyField> {
  const errors: FieldErrors<CompanyField> = {}
  errors.name = required(payload.name, 'Nome') ?? lengthBetween(payload.name, 'Nome', 3, 150)
  errors.cnpj = required(payload.cnpj, 'CNPJ') ?? (!isValidCnpj(payload.cnpj) ? 'Informe um CNPJ válido.' : undefined)
  errors.email = required(payload.email, 'Email') ?? (!isValidEmail(payload.email) ? 'Informe um email válido.' : undefined)
  errors.phone = required(payload.phone, 'Telefone') ?? (!isValidPhone(payload.phone) ? 'Informe um telefone com DDD e 10 ou 11 dígitos.' : undefined)
  if (payload.status && !['active', 'inactive'].includes(payload.status)) errors.status = 'Selecione um status válido.'

  return Object.fromEntries(Object.entries(errors).filter(([, value]) => value)) as FieldErrors<CompanyField>
}
