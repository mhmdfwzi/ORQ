import type { CartItem } from '@/types/product';

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://127.0.0.1:8001/api/v1';

export interface CheckoutForm {
  name: string;
  phone: string;
  governorate: string;
  address: string;
  notes?: string;
}

export interface PlacedOrder {
  orderNumber: string;
  total: number;
}

export type FieldErrors = Record<string, string[]>;

export class OrderError extends Error {
  constructor(message: string, public readonly fieldErrors: FieldErrors = {}) {
    super(message);
  }
}

/**
 * Guest checkout. Only slug/size/quantity are sent: the server resolves
 * prices and stock itself, so the cart's displayed prices are never trusted.
 */
export async function placeOrder(form: CheckoutForm, items: CartItem[]): Promise<PlacedOrder> {
  let res: Response;
  try {
    res = await fetch(`${API_URL}/orders`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        ...form,
        items: items.map(({ slug, size, quantity }) => ({ slug, size, quantity })),
      }),
    });
  } catch {
    throw new OrderError('تعذر الاتصال بالخادم. تأكد من الإنترنت وحاول مرة أخرى.');
  }

  const body = await res.json().catch(() => null);

  if (res.status === 201 && body?.data) {
    return { orderNumber: body.data.orderNumber, total: body.data.total };
  }
  if (res.status === 422) {
    throw new OrderError(body?.message ?? 'بيانات الطلب غير صحيحة.', body?.errors ?? {});
  }
  if (res.status === 429) {
    throw new OrderError('محاولات كثيرة. برجاء الانتظار دقيقة ثم المحاولة مرة أخرى.');
  }
  throw new OrderError('حدث خطأ غير متوقع. حاول مرة أخرى.');
}
