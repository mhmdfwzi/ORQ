import type { CartItem } from '@/types/product';
import { accountApi, accountRequest, type AccountSession } from '@/lib/account';
import { checkoutAttribution } from '@/lib/attribution';

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://127.0.0.1:8001/api/v1';

export interface CheckoutForm {
  name: string;
  phone: string;
  governorate: string;
  address: string;
  notes?: string;
  whatsapp_confirmation_requested?: boolean;
  payment_method?: 'cod' | 'card';
  billing_email?: string;
  checkout_key?: string;
}

export interface PlacedOrder {
  orderNumber: string;
  total: number;
  paymentMethod: 'cod' | 'card';
  checkoutUrl?: string | null;
  checkoutKey?: string;
}

export interface PaymentStatus {
  orderNumber: string;
  total: number;
  status: 'paid' | 'pending' | 'declined' | 'awaiting_confirmation' | 'expired' | 'review' | 'refunded';
}

interface CheckoutSession {
  items: CartItem[];
  url: string | null;
}

export function saveCheckoutSession(key: string, session: CheckoutSession) {
  try {
    sessionStorage.setItem('orq-checkout-' + key, JSON.stringify(session));
  } catch {
    // Browser storage is optional. Payment status remains available from the API.
  }
}

export function readCheckoutSession(key: string): CheckoutSession | null {
  try {
    return JSON.parse(sessionStorage.getItem('orq-checkout-' + key) ?? 'null');
  } catch {
    return null;
  }
}

export function removeCheckoutSession(key: string) {
  try {
    sessionStorage.removeItem('orq-checkout-' + key);
  } catch {
    // The browser may have storage disabled.
  }
}

export type FieldErrors = Record<string, string[]>;

export class OrderError extends Error {
  constructor(message: string, public readonly fieldErrors: FieldErrors = {}) {
    super(message);
  }
}

/**
 * Guest and customer checkout. Only slug/size/quantity are sent: the server resolves
 * prices and stock itself, so the cart's displayed prices are never trusted.
 */
export async function placeOrder(form: CheckoutForm, items: CartItem[]): Promise<PlacedOrder> {
  let res: Response;
  try {
    const attribution = checkoutAttribution();
    const session = await accountRequest<AccountSession>('/session');
    const headers = new Headers({ 'Content-Type': 'application/json', Accept: 'application/json' });
    if (session.customer) headers.set('X-CSRF-TOKEN', session.csrfToken);
    res = await fetch((session.customer ? accountApi : API_URL) + '/orders', {
      method: 'POST',
      headers,
      body: JSON.stringify({
        ...form,
        attribution,
        items: items.map(({ slug, size, quantity }) => ({ slug, size, quantity })),
      }),
    });
  } catch {
    throw new OrderError('تعذر الاتصال بالخادم. تأكد من الإنترنت وحاول مرة أخرى.');
  }

  const body = await res.json().catch(() => null);

  if (res.status === 201 && body?.data) {
    return body.data;
  }
  if (res.status === 422) {
    throw new OrderError(body?.message ?? 'بيانات الطلب غير صحيحة.', body?.errors ?? {});
  }
  if ([401, 419].includes(res.status)) {
    throw new OrderError('جلسة حسابك انتهت أثناء الطلب. حدّث الصفحة وحاول مرة تانية.');
  }
  if (res.status === 429) {
    throw new OrderError('محاولات كثيرة. برجاء الانتظار دقيقة ثم المحاولة مرة أخرى.');
  }
  if ([409, 503].includes(res.status) && body?.message) {
    throw new OrderError(body.message);
  }
  throw new OrderError('حدث خطأ غير متوقع. حاول مرة أخرى.');
}

export async function getPaymentStatus(key: string, signal: AbortSignal): Promise<PaymentStatus | null> {
  const response = await fetch(API_URL + '/payments/paymob/status/' + encodeURIComponent(key), {
    headers: { Accept: 'application/json' },
    cache: 'no-store',
    signal,
  });

  if (response.status === 404) return null;
  if (!response.ok) throw new OrderError('تعذّر التحقق من حالة الدفع حالياً.');
  const body = await response.json();
  return body.data;
}
