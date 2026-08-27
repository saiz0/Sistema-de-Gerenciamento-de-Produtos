import assert from 'node:assert/strict'
import test from 'node:test'

import { HttpClient } from '../../src/shared/api/httpClient.ts'

test('envia confirmação explícita na exclusão definitiva', async (context) => {
  const originalFetch = globalThis.fetch
  context.after(() => { globalThis.fetch = originalFetch })
  globalThis.fetch = async (url, init) => {
    assert.equal(String(url), 'http://api.test/products/9/force')
    assert.equal(init?.method, 'DELETE')
    assert.deepEqual(JSON.parse(String(init?.body)), { confirmed: true })
    return Response.json({ success: true, message: 'Produto excluído definitivamente.' })
  }

  const response = await new HttpClient('http://api.test').delete('/products/9/force', { confirmed: true })
  assert.equal(response.success, true)
})

test('apresenta mensagem compreensível quando a API não está disponível', async (context) => {
  const originalFetch = globalThis.fetch
  context.after(() => { globalThis.fetch = originalFetch })
  globalThis.fetch = async () => { throw new TypeError('network error') }

  await assert.rejects(
    () => new HttpClient('http://api.test').get('/products'),
    /Não foi possível conectar ao servidor/,
  )
})
