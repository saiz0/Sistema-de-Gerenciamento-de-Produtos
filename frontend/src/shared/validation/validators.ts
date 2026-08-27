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
  return /^\d{10,11}$/.test(digits) && digits.slice(0, 2) !== '00'
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
  return Number(normalized) > 0
}
