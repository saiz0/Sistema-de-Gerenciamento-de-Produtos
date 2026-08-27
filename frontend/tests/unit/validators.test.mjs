import assert from 'node:assert/strict'
import test from 'node:test'

import { isValidCnpj, isValidEmail, isValidPhone, isValidPrice } from '../../src/shared/validation/validators.ts'

test('valida CNPJ com ou sem máscara', () => {
  assert.equal(isValidCnpj('11.222.333/0001-81'), true)
  assert.equal(isValidCnpj('11222333000180'), false)
  assert.equal(isValidCnpj('11111111111111'), false)
})

test('valida email e telefone com DDD', () => {
  assert.equal(isValidEmail('contato@empresa.com.br'), true)
  assert.equal(isValidEmail('email-invalido'), false)
  assert.equal(isValidPhone('(71) 99999-9999'), true)
  assert.equal(isValidPhone('(20) 99999-9999'), false)
  assert.equal(isValidPhone('9999-9999'), false)
})

test('aceita preço positivo com no máximo duas casas', () => {
  assert.equal(isValidPrice('249,90'), true)
  assert.equal(isValidPrice('1'), true)
  assert.equal(isValidPrice('0'), false)
  assert.equal(isValidPrice('10.999'), false)
  assert.equal(isValidPrice('12345678901234.00'), false)
})
