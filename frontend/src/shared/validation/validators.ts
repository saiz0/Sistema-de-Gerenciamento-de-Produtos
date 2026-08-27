export type FieldErrors<T extends string> = Partial<Record<T, string>>

export function required(value: string, label: string): string | undefined {
  return value.trim() ? undefined : `${label} é obrigatório.`
}

export function lengthBetween(value: string, label: string, min: number, max: number): string | undefined {
  const length = value.trim().length
  if (length < min || length > max) return `${label} deve ter entre ${min} e ${max} caracteres.`
}

export function maxLength(value: string, label: string, max: number): string | undefined {
  if (value.trim().length > max) return `${label} deve ter no máximo ${max} caracteres.`
}

export function isValidEmail(value: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim())
}

export function isValidPhone(value: string): boolean {
  const digits = value.replace(/\D/g, '')
  const areaCodes = new Set([
    '11', '12', '13', '14', '15', '16', '17', '18', '19',
    '21', '22', '24', '27', '28', '31', '32', '33', '34', '35', '37', '38',
    '41', '42', '43', '44', '45', '46', '47', '48', '49', '51', '53', '54', '55',
    '61', '62', '63', '64', '65', '66', '67', '68', '69', '71', '73', '74', '75', '77', '79',
    '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92', '93', '94', '95', '96', '97', '98', '99',
  ])
  return /^\d{10,11}$/.test(digits) && areaCodes.has(digits.slice(0, 2))
}

export function isValidCnpj(value: string): boolean {
  const digits = value.replace(/\D/g, '')
  if (!/^\d{14}$/.test(digits) || /^(\d)\1{13}$/.test(digits)) return false

  const calculateDigit = (length: number): number => {
    let factor = length - 7
    let total = 0
    for (let index = 0; index < length; index += 1) {
      total += Number(digits[index]) * factor--
      if (factor < 2) factor = 9
    }
    const remainder = total % 11
    return remainder < 2 ? 0 : 11 - remainder
  }

  return calculateDigit(12) === Number(digits[12]) && calculateDigit(13) === Number(digits[13])
}

export function isValidPrice(value: string): boolean {
  const normalized = value.trim().replace(',', '.')
  if (!/^\d+(?:\.\d{1,2})?$/.test(normalized)) return false
  const [integer = ''] = normalized.split('.')
  return integer.replace(/^0+/, '').length <= 13 && Number(normalized) > 0
}
