import type { ApiErrorBody } from './types'

const DEFAULT_MESSAGE = 'Não foi possível concluir a operação. Tente novamente.'

export class ApiError extends Error {
  readonly status: number
  readonly code?: string
  readonly fieldErrors: Record<string, string[]>

  constructor(status: number, body?: Partial<ApiErrorBody>) {
    super(body?.message || DEFAULT_MESSAGE)
    this.name = 'ApiError'
    this.status = status
    this.code = body?.code
    this.fieldErrors = body?.errors ?? {}
  }
}

export function getErrorMessage(error: unknown): string {
  if (error instanceof ApiError) return error.message
  if (error instanceof Error && error.message) return error.message
  return DEFAULT_MESSAGE
}
