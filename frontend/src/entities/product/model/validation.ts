import { isValidPrice, lengthBetween, maxLength, required, type FieldErrors } from '../../../shared/validation/validators'
import type { ProductPayload } from './types'

type ProductField = 'company_id' | 'name' | 'description' | 'price' | 'internal_code' | 'status'

export function validateProduct(payload: ProductPayload): FieldErrors<ProductField> {
  const errors: FieldErrors<ProductField> = {}
  if (!Number.isInteger(payload.company_id) || payload.company_id < 1) errors.company_id = 'Selecione uma empresa apta.'
  errors.name = required(payload.name, 'Nome') ?? lengthBetween(payload.name, 'Nome', 3, 150)
  if (payload.description) errors.description = maxLength(payload.description, 'Descrição', 2000)
  errors.price = required(payload.price, 'Preço') ?? (!isValidPrice(payload.price) ? 'Informe um preço maior que zero, com até duas casas decimais.' : undefined)
  errors.internal_code = required(payload.internal_code, 'Código interno') ?? maxLength(payload.internal_code, 'Código interno', 100)
  if (payload.status && !['active', 'inactive'].includes(payload.status)) errors.status = 'Selecione um status válido.'

  return Object.fromEntries(Object.entries(errors).filter(([, value]) => value)) as FieldErrors<ProductField>
}
