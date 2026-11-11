import { NextRequest, NextResponse } from 'next/server';
import { revalidateTag } from 'next/cache';
import { timingSafeEqual } from 'node:crypto';

function isValidToken(token: string | null): boolean {
  const secret = process.env.REVALIDATE_SECRET;
  if (!secret || !token) return false;
  const a = Buffer.from(token);
  const b = Buffer.from(secret);
  return a.length === b.length && timingSafeEqual(a, b);
}

/** Called by the Laravel ProductObserver whenever a product changes in Filament. */
export async function POST(request: NextRequest) {
  if (!isValidToken(request.headers.get('x-revalidate-token'))) {
    return NextResponse.json({ message: 'Invalid token' }, { status: 401 });
  }

  revalidateTag('products', 'max');
  return NextResponse.json({ revalidated: true, now: Date.now() });
}
