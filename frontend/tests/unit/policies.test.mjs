import assert from 'node:assert/strict'
import test from 'node:test'

import { companyActions, canReceiveProducts } from '../../src/entities/company/model/policies.ts'
import { productActions } from '../../src/entities/product/model/policies.ts'

const company = { id: 1, name: 'Empresa', cnpj: '', email: '', phone: '', status: 'active', created_at: null, updated_at: null, deleted_at: null }
const product = { id: 1, company_id: 1, name: 'Produto', description: null, price: '1.00', internal_code: 'P-1', status: 'active', created_at: null, updated_at: null, deleted_at: null }

test('empresa excluída só oferece restauração e exclusão física quando não possui produtos', () => {
  const deleted = { ...company, deleted_at: '2026-08-27T00:00:00Z' }
  assert.deepEqual(companyActions(deleted, false), { edit: false, activate: false, deactivate: false, remove: false, restore: true, forceDelete: true })
  assert.equal(companyActions(deleted, true).forceDelete, false)
})

test('somente empresa ativa e não excluída pode receber produtos', () => {
  assert.equal(canReceiveProducts(company), true)
  assert.equal(canReceiveProducts({ ...company, status: 'inactive' }), false)
  assert.equal(canReceiveProducts({ ...company, deleted_at: '2026-08-27T00:00:00Z' }), false)
})

test('produto não pode ser editado ou ativado quando a empresa está indisponível', () => {
  const inactiveProduct = { ...product, status: 'inactive' }
  assert.equal(productActions(inactiveProduct, company).activate, true)
  assert.equal(productActions(inactiveProduct, { ...company, status: 'inactive' }).activate, false)
  assert.equal(productActions(product, { ...company, deleted_at: '2026-08-27T00:00:00Z' }).edit, false)
})
