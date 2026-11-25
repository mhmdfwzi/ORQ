const API_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://127.0.0.1:8001/api/v1';
const fields = ['utm_source', 'utm_medium', 'utm_campaign'] as const;
const identifier = /^[\p{L}\p{N}][\p{L}\p{M}\p{N} _.-]{0,99}$/u;
const sessionIdPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
const sessionKey = 'orq-funnel-session';
const campaignKey = 'orq-last-campaign';
const minute = 60_000;

type Campaign = Record<(typeof fields)[number], string>;
type Stage = 'visit' | 'cart' | 'checkout';

function campaignFromSearch(): Campaign | undefined {
  const params = new URLSearchParams(window.location.search);
  const source = params.get('utm_source')?.trim().toLowerCase() ?? '';
  const medium = params.get('utm_medium')?.trim().toLowerCase() ?? '';
  const campaign = params.get('utm_campaign')?.trim().toLowerCase() ?? '';

  if (![source, medium, campaign].every((value) => identifier.test(value))) return undefined;

  return { utm_source: source, utm_medium: medium, utm_campaign: campaign };
}

function lastCampaign(now: number): Campaign | undefined {
  const incoming = campaignFromSearch();

  try {
    if (incoming) {
      localStorage.setItem(campaignKey, JSON.stringify({ values: incoming, expires: now + 30 * 24 * 60 * minute }));
      return incoming;
    }

    const saved = JSON.parse(localStorage.getItem(campaignKey) ?? 'null');
    if (saved?.expires > now && fields.every((field) => typeof saved.values?.[field] === 'string' && identifier.test(saved.values[field]))) {
      return { utm_source: saved.values.utm_source, utm_medium: saved.values.utm_medium, utm_campaign: saved.values.utm_campaign };
    }
    localStorage.removeItem(campaignKey);
  } catch {
    // Tracking must never interrupt shopping when browser storage is unavailable.
  }

  return incoming;
}

export function checkoutAttribution(): (Partial<Campaign> & { session_id?: string }) | undefined {
  if (typeof window === 'undefined' || navigator.doNotTrack === '1'
    || (navigator as Navigator & { globalPrivacyControl?: boolean }).globalPrivacyControl
    || window.location.pathname.startsWith('/preview')) return undefined;

  const now = Date.now();
  const campaign = lastCampaign(now);

  try {
    const saved = JSON.parse(sessionStorage.getItem(sessionKey) ?? 'null');
    const id = saved?.expires > now && typeof saved.id === 'string' && sessionIdPattern.test(saved.id)
      ? saved.id
      : crypto.randomUUID();
    sessionStorage.setItem(sessionKey, JSON.stringify({ id, expires: now + 30 * minute }));
    return { ...campaign, session_id: id };
  } catch {
    return campaign;
  }
}

const pendingEvents = new Set<string>();

export function trackStorefrontStage(stage: Stage) {
  const sessionId = checkoutAttribution()?.session_id;
  if (!sessionId) return;

  // A visit is also sent on the first cart action if the page request failed.
  if (stage !== 'visit') sendEvent(sessionId, 'visit');
  sendEvent(sessionId, stage);
}

function sendEvent(sessionId: string, stage: Stage) {
  const day = new Intl.DateTimeFormat('en-CA', { timeZone: 'Africa/Cairo' }).format(new Date());
  const key = `${sessionId}:${stage}:${day}`;
  if (pendingEvents.has(key)) return;
  pendingEvents.add(key);

  void fetch(API_URL + '/funnel', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ session_id: sessionId, stage }),
    keepalive: true,
  }).then((response) => {
    if (!response.ok) pendingEvents.delete(key);
  }).catch(() => pendingEvents.delete(key));
}
