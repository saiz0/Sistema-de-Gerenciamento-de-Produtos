import { readonly, ref } from 'vue'

export type ToastKind = 'success' | 'error' | 'info'

export interface ToastMessage {
  id: number
  kind: ToastKind
  message: string
}

const messages = ref<ToastMessage[]>([])
let sequence = 0

export function notify(message: string, kind: ToastKind = 'success'): void {
  const id = ++sequence
  messages.value.push({ id, kind, message })
  window.setTimeout(() => dismissToast(id), 5000)
}

export function dismissToast(id: number): void {
  messages.value = messages.value.filter((toast) => toast.id !== id)
}

export const toastMessages = readonly(messages)
