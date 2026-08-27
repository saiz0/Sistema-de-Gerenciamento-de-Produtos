import assert from 'node:assert/strict'
import test from 'node:test'

import { createCompanyApi } from '../../src/entities/company/api/companyApi.ts'
import { ApiError } from '../../src/shared/api/ApiError.ts'
import { HttpClient } from '../../src/shared/api/httpClient.ts'

test('envia filtros de listagem e interpreta paginação', async (context) => {
  const originalFetch = globalThis.fetch
  context.after(() => { globalThis.fetch = originalFetch })
  globalThis.fetch = async (url) => {
    assert.equal(String(url), 'http://api.test/companies?name=Acme&status=active&deleted=without&page=2&per_page=15')
    return Response.json({ success: true, message: 'Lista', data: [], meta: { current_page: 2, per_page: 15, total: 0, last_page: 1 } })
  }

  const response = await createCompanyApi(new HttpClient('http://api.test')).list({ name: 'Acme', status: 'active', deleted: 'without', page: 2, per_page: 15 })
  assert.equal(response.meta.current_page, 2)
})

test('preserva mensagem e erros de campo retornados pela API', async (context) => {
  const originalFetch = globalThis.fetch
  context.after(() => { globalThis.fetch = originalFetch })
  globalThis.fetch = async () => Response.json({ success: false, message: 'Os dados informados são inválidos.', code: 'validation_error', errors: { cnpj: ['O CNPJ informado é inválido.'] } }, { status: 422 })

  await assert.rejects(
    () => createCompanyApi(new HttpClient('http://api.test')).create({ name: 'Empresa', cnpj: 'x', email: 'a@b.com', phone: '71999999999' }),
    (error) => error instanceof ApiError && error.fieldErrors.cnpj?.[0] === 'O CNPJ informado é inválido.',
  )
})
