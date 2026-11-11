'use client';

import React from 'react';
import ScrollReveal from '@/components/ui/ScrollReveal';
import { useCatalog } from '@/components/catalog/CatalogContext';

/**
 * "شفرة ORQ" — the O · R · Q brand story. Content comes from the Laravel
 * admin (Settings → قصة البراند); styling follows the dark/gold house look.
 */
export default function BrandStory() {
  const { brand } = useCatalog();

  return (
    <section
      id="story"
      aria-labelledby="brand-story-title"
      className="py-16 sm:py-20 bg-gradient-to-b from-[#0A0908] via-[#0E0C0A] to-[#070707] border-b border-[#1E1B18] scroll-mt-20"
    >
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <ScrollReveal direction="up">
          <header className="text-center max-w-2xl mx-auto space-y-3">
            <span className="font-serif tracking-[0.3em] text-xs text-[#C4A265] uppercase block">{brand.eyebrow}</span>
            <h2 id="brand-story-title" className="text-2xl sm:text-3xl font-bold text-white leading-snug">
              {brand.title}
            </h2>
            <p className="text-sm text-zinc-400 leading-relaxed">{brand.name_intro}</p>
          </header>
        </ScrollReveal>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {brand.pillars.map((pillar, idx) => (
            <ScrollReveal key={pillar.letter + idx} delay={idx * 140} direction="up">
              <article className="h-full rounded-2xl border border-[#282420] bg-[#12100E] p-6 space-y-4 transition hover:border-[#C4A265]/50">
                <div className="flex items-end gap-3" dir="ltr">
                  <span
                    className="font-serif text-6xl leading-none text-transparent bg-clip-text bg-gradient-to-b from-[#ECC480] to-[#8C6D35]"
                    aria-hidden="true"
                  >
                    {pillar.letter}
                  </span>
                  <span className="pb-1 text-xs tracking-[0.2em] uppercase text-[#C4A265]">{pillar.en}</span>
                </div>
                <h3 className="text-lg font-bold text-white">{pillar.ar}</h3>
                <p className="text-xs text-[#ECC480] leading-relaxed">{pillar.meaning}</p>
                <p className="text-xs text-zinc-400 leading-relaxed">{pillar.story}</p>
              </article>
            </ScrollReveal>
          ))}
        </div>

        <ScrollReveal direction="up" delay={100}>
          <div className="text-center max-w-3xl mx-auto space-y-5">
            <p className="text-sm text-zinc-400 leading-loose">{brand.story_intro}</p>
            <div className="w-12 h-px bg-[#C4A265]/60 mx-auto" aria-hidden="true" />
            <p className="text-lg sm:text-xl font-serif text-white leading-relaxed">{brand.closing}</p>
            <p className="text-xs text-[#C4A265] tracking-wide">{brand.signature}</p>
          </div>
        </ScrollReveal>
      </div>
    </section>
  );
}

