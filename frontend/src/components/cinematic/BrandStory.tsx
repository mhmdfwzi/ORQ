'use client';

import { useCatalog } from '@/components/catalog/CatalogContext';

export default function BrandStory() {
  const { brand } = useCatalog();

  return (
    <section id="story" className="scroll-mt-20 border-b border-[#221F1C] py-5 sm:py-8">
      <details className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <summary className="min-h-11 cursor-pointer rounded-lg border border-[#302820] bg-[#14110E] px-4 py-3 text-sm font-bold text-[#ECC480] sm:text-base">
          {brand.title}
        </summary>
        <div className="space-y-5 px-2 pt-5 text-sm leading-relaxed text-[#B9B1A5] sm:px-4">
          <p>{brand.name_intro}</p>
          <div className="grid gap-3 md:grid-cols-3">
            {brand.pillars.map((pillar) => (
              <article key={pillar.letter} className="space-y-2 rounded-xl border border-[#282420] bg-[#12100E] p-4">
                <h3 className="font-bold text-[#ECC480]"><bdi>{pillar.letter}</bdi> · {pillar.ar}</h3>
                <p>{pillar.meaning}</p>
                <p>{pillar.story}</p>
              </article>
            ))}
          </div>
          <p>{brand.story_intro}</p>
          <p className="font-semibold text-white">{brand.closing}</p>
        </div>
      </details>
    </section>
  );
}
