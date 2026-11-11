export interface BrandPillar {
  letter: string;
  en: string;
  ar: string;
  meaning: string;
  story: string;
}

/** "شفرة ORQ" brand story, edited in the Filament admin (Settings → قصة البراند). */
export interface BrandStory {
  eyebrow: string;
  title: string;
  name_intro: string;
  story_intro: string;
  pillars: BrandPillar[];
  closing: string;
  signature: string;
}

