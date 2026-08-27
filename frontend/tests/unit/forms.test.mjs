import assert from 'node:assert/strict'
import test from 'node:test'

import { validateCompany } from '../../src/entities/company/model/validation.ts'
import { validateProduct } from '../../src/entities/product/model/validation.ts'

test('valida todos os campos obrigatórios da empresa', () => {
  const errors = validateCompany({ name: 'A', cnpj: '123', email: 'invalido', phone: '9999', status: 'active' })
  assert.deepEqual(Object.keys(errors).sort(), ['cnpj', 'email', 'name', 'phone'])
})

test('valida vínculo, limites e preço do produto', () => {
  const errors = validateProduct({ company_id: 0, name: 'A', description: 'x'.repeat(2001), price: '0', internal_code: '', status: 'active' })
  assert.deepEqual(Object.keys(errors).sort(), ['company_id', 'description', 'internal_code', 'name', 'price'])
})
