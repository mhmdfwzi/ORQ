export type Customer = { name: string; email: string | null; phone: string | null };

export type AccountSession = {
  googleAvailable: boolean;
  customer: Customer | null;
  csrfToken: string;
};

export type AccountOrder = {
  number: string;
  date: string;
  status: string;
  total: number;
  paymentMethod: string;
  paymentStatus: string;
  itemCount: number;
};

export type AccountOrderDetails = AccountOrder & {
  subtotal: number;
  shippingCost: number;
  discount: number;
  address: string | null;
  trackingNumber: string | null;
  shippingCarrier: string | null;
  timeline: { status: string; completed: boolean; date: string | null }[];
  items: { name: string; size: string | null; quantity: number; total: number }[];
};

export const accountApi = '/api/v1/account';

export class AccountError extends Error {
  constructor(message: string, public readonly status: number, public readonly fields: Record<string, string[]> = {}) {
    super(message);
  }
}

export async function accountRequest<T>(path: string, options: RequestInit = {}): Promise<T> {
  const headers = new Headers(options.headers);
  headers.set('Accept', 'application/json');
  const response = await fetch(accountApi + path, { ...options, headers, cache: 'no-store' });
  if (response.status === 204) return undefined as T;

  const body = await response.json().catch(() => null);
  if (response.ok && body) return body;

  let message = 'حصلت مشكلة في الاتصال. جرّب تاني.';
  if (response.status === 401) message = 'سجّل الدخول عشان تتابع حسابك.';
  if (response.status === 419) message = 'الجلسة انتهت. حدّث الصفحة وحاول تاني.';
  if (response.status === 422) message = 'راجع البيانات اللي كتبتها.';
  if (response.status === 429) message = 'محاولات كتير. استنى شوية وحاول تاني.';
  if (response.status === 503 && path === '/forgot-password') message = 'إرسال رابط الاستعادة غير متاح حاليًا.';
  if (response.status === 404 && path.startsWith('/orders/')) message = 'الطلب غير موجود.';
  throw new AccountError(message, response.status, body?.errors);
}

export function accountMutation<T>(path: string, session: AccountSession, data?: Record<string, FormDataEntryValue | string | null>, method = 'POST') {
  return accountRequest<T>(path, {
    method,
    headers: { 'X-CSRF-TOKEN': session.csrfToken, 'Content-Type': 'application/json' },
    body: data ? JSON.stringify(data) : undefined,
  });
}
