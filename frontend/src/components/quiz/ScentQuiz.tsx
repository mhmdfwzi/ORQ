'use client';

import Link from 'next/link';
import { useMemo, useRef, useState } from 'react';
import { scoreQuiz, type Quiz } from '@/lib/scent-quiz';

export type QuizProduct = {
  slug: string;
  name: string;
  tagline: string;
  notes: { top: string[]; heart: string[]; base: string[] };
  when: { intensity: string; occasion: string; season: string };
  image?: string | null;
  imageAlt: string;
};

type Answers = Record<string, string>;

function track(name: string, data: Record<string, unknown> = {}) {
  const w = window as unknown as { orqTrack?: (n: string, d: Record<string, unknown>) => void };
  w.orqTrack?.(name, data);
}

export function ScentQuiz({ quiz, products }: { quiz: Quiz; products: QuizProduct[] }) {
  const [answers, setAnswers] = useState<Answers>({});
  const [step, setStep] = useState(0);
  const started = useRef(false);
  const bySlug = useMemo(() => new Map(products.map((product) => [product.slug, product])), [products]);
  const total = quiz.questions.length;
  const rank = (currentAnswers: Answers) => scoreQuiz(quiz, currentAnswers).flatMap(({ slug }) => {
    const product = bySlug.get(slug);
    return product ? [product] : [];
  });

  if (step >= total) {
    const [top, ...alternatives] = rank(answers).slice(0, 3);
    return (
      <div className="space-y-5" aria-live="polite">
        {top ? <>
          <h2 className="text-lg font-bold text-[#ECC480]">شفرتك هي</h2>
          <ResultCard product={top} main />
        </> : <p>مفيش عطور متاحة للترشيح حاليًا.</p>}
        {alternatives.length > 0 && (
          <>
            <h3 className="pt-2 text-lg text-white/70">وممكن كمان تعجبك</h3>
            <div className="grid gap-3 sm:grid-cols-2">
              {alternatives.map((p) => (
                <ResultCard key={p.slug} product={p} />
              ))}
            </div>
          </>
        )}
        <button
          type="button"
          onClick={() => {
            setAnswers({});
            setStep(0);
            started.current = false;
          }}
          className="min-h-11 rounded-full border border-white/20 px-5 text-white/70 hover:border-[#C9A24A]"
        >
          جرّب تاني
        </button>
      </div>
    );
  }

  const q = quiz.questions[step];
  return (
    <div aria-live="polite">
      <div
        className="h-1 overflow-hidden rounded bg-white/10"
        role="progressbar"
        aria-label="تقدّم اختبار العطر"
        aria-valuemin={0}
        aria-valuemax={total}
        aria-valuenow={step + 1}
      >
        <span
          className="block h-full bg-[#C9A24A] transition-[width] duration-300 motion-reduce:transition-none"
          style={{ width: `${((step + 1) / total) * 100}%` }}
        />
      </div>
      <p className="mt-4 text-sm text-white/60">
        سؤال {step + 1} من {total}
      </p>
      <h2 className="mb-5 mt-1 text-2xl font-bold leading-snug md:text-3xl">{q.q}</h2>
      <div className="grid gap-3 sm:grid-cols-2">
        {q.options.map((o) => (
          <button
            key={o.id}
            type="button"
            onClick={() => {
              const next = { ...answers, [q.id]: o.id };
              setAnswers(next);
              if (!started.current) {
                started.current = true;
                track('quiz_start');
              }
              if (step + 1 >= total) {
                track('quiz_complete', { result: rank(next)[0]?.slug });
              }
              setStep(step + 1);
            }}
            className={`min-h-14 rounded-2xl border bg-white/[0.04] px-5 py-3 text-right text-lg transition hover:border-[#C9A24A] focus-visible:border-[#C9A24A] focus-visible:outline-none ${
              answers[q.id] === o.id ? 'border-[#C9A24A]' : 'border-white/15'
            }`}
          >
            {o.label}
          </button>
        ))}
      </div>
      {step > 0 && (
        <button
          type="button"
          onClick={() => setStep(step - 1)}
          className="mt-5 min-h-11 rounded-full border border-white/20 px-5 text-white/70"
        >
          رجوع
        </button>
      )}
    </div>
  );
}

function ResultCard({ product, main = false }: { product: QuizProduct; main?: boolean }) {
  const notes = [...product.notes.top, ...product.notes.heart, ...product.notes.base].slice(0, 5).join('، ');
  return (
    <article
      className={`rounded-2xl border bg-white/[0.04] p-5 ${main ? 'border-[#C9A24A]' : 'border-white/15'}`}
    >
      {main && product.image && (
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={product.image}
          alt={product.imageAlt}
          width={600}
          height={600}
          loading="lazy"
          className="mb-4 aspect-square w-full rounded-xl object-cover"
        />
      )}
      <h3 className={`font-bold ${main ? 'text-3xl' : 'text-xl'}`}>عطر {product.name}</h3>
      <p className="mt-1 text-white/85">{product.tagline}</p>
      <p className="mt-2 text-sm text-white/60">{notes}</p>
      <p className="mt-1 text-sm text-white/60">
        {product.when.occasion} · {product.when.season}
      </p>
      <Link
        href={`/products/${product.slug}`}
        onClick={() => track('quiz_click', { slug: product.slug })}
        className={
          main
            ? 'mt-4 inline-flex min-h-12 items-center rounded-full bg-[#C9A24A] px-6 font-bold text-[#111]'
            : 'mt-3 inline-block text-[#C9A24A] underline'
        }
      >
        {main ? 'اكتشف عطرك' : 'شوف التفاصيل'}
      </Link>
    </article>
  );
}
