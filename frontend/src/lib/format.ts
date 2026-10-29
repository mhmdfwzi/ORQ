/**
 * Deterministic price formatter to prevent SSR/Client hydration mismatches.
 * Uses standard thousand separators without relying on system locale.
 */
export function formatPrice(amount: number): string {
  if (typeof amount !== 'number' || isNaN(amount)) return '0';
  return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

