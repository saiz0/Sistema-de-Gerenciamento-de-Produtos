import { ApiError } from './ApiError.ts'
import type { ApiErrorBody } from './types.ts'

type QueryValue = string | number | boolean | null | undefined

function buildQuery(query?: object): string {
  const parameters = new URLSearchParams()

  Object.entries(query ?? {}).forEach(([key, value]: [string, QueryValue]) => {
    if (value !== undefined && value !== null && value !== '') {
      parameters.set(key, String(value))
    }
  })

  const result = parameters.toString()
  return result ? `?${result}` : ''
}

export class HttpClient {
  private readonly baseUrl: string

  constructor(baseUrl: string) {
    this.baseUrl = baseUrl
  }

  get<T>(path: string, query?: object): Promise<T> {
    return this.request<T>(`${path}${buildQuery(query)}`)
  }

  post<T>(path: string, body?: unknown): Promise<T> {
    return this.request<T>(path, { method: 'POST', body: JSON.stringify(body ?? {}) })
  }

  put<T>(path: string, body: unknown): Promise<T> {
    return this.request<T>(path, { method: 'PUT', body: JSON.stringify(body) })
  }

  patch<T>(path: string, body?: unknown): Promise<T> {
    return this.request<T>(path, { method: 'PATCH', body: JSON.stringify(body ?? {}) })
  }

  delete<T>(path: string, body?: unknown): Promise<T> {
    return this.request<T>(path, {
      method: 'DELETE',
      body: body === undefined ? undefined : JSON.stringify(body),
    })
  }

  private async request<T>(path: string, init: RequestInit = {}): Promise<T> {
    let response: Response

    try {
      response = await fetch(`${this.baseUrl}${path}`, {
        ...init,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          ...init.headers,
        },
      })
    } catch {
      throw new ApiError(0, {
        message: 'Não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.',
      })
    }

    const body = await response.json().catch(() => undefined) as T | ApiErrorBody | undefined

    if (!response.ok) {
      throw new ApiError(response.status, body as ApiErrorBody | undefined)
    }

    return body as T
  }
}

const configuredBaseUrl = import.meta.env?.VITE_API_BASE_URL || 'http://localhost:8000/api/v1'

export const httpClient = new HttpClient(configuredBaseUrl.replace(/\/$/, ''))
