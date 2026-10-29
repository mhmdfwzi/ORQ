'use client';

import React from 'react';

interface SculptedPerfumeBottleProps {
  className?: string;
  isGlowing?: boolean;
  isRevealing?: boolean;
}

export default function SculptedPerfumeBottle({
  className = '',
  isGlowing = false,
  isRevealing = false,
}: SculptedPerfumeBottleProps) {
  return (
    <div
      className={`relative select-none flex items-center justify-center transition-all duration-1000 ${className}`}
    >
      {/* Outer Ethereal Glow */}
      <div
        className={`absolute -inset-10 rounded-full bg-gradient-to-t from-[#C4A265]/25 via-[#C4A265]/10 to-transparent blur-3xl pointer-events-none transition-opacity duration-1000 ${
          isGlowing ? 'opacity-100 scale-110' : 'opacity-0 scale-90'
        }`}
      />

      {/* SVG Sculpted Bottle (Pure Vector & Gradients - NO Rectangular Snippet/Box) */}
      <svg
        viewBox="0 0 320 540"
        className="w-full h-full max-h-[500px] drop-shadow-[0_20px_50px_rgba(0,0,0,0.9)] overflow-visible"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          {/* Cap Metallic Gradient */}
          <linearGradient id="goldCapGrad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="#8A6C32" />
            <stop offset="25%" stopColor="#ECC480" />
            <stop offset="50%" stopColor="#C4A265" />
            <stop offset="75%" stopColor="#F5E0B7" />
            <stop offset="100%" stopColor="#6E5322" />
          </linearGradient>

          {/* Cap Top Chamfer */}
          <linearGradient id="capTopGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stopColor="#ECC480" />
            <stop offset="100%" stopColor="#8A6C32" />
          </linearGradient>

          {/* Glass Body Dark Obsidian Gradient */}
          <linearGradient id="obsidianGlass" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="#080706" />
            <stop offset="8%" stopColor="#1C1814" />
            <stop offset="20%" stopColor="#2A2218" />
            <stop offset="50%" stopColor="#12100E" />
            <stop offset="80%" stopColor="#221C14" />
            <stop offset="92%" stopColor="#1C1814" />
            <stop offset="100%" stopColor="#080706" />
          </linearGradient>

          {/* Liquid Essence Inner Luminescence */}
          <radialGradient id="liquidEssence" cx="50%" cy="65%" r="45%">
            <stop offset="0%" stopColor="#E5B25D" stopOpacity="0.45" />
            <stop offset="40%" stopColor="#B88628" stopOpacity="0.25" />
            <stop offset="85%" stopColor="#3A280D" stopOpacity="0.08" />
            <stop offset="100%" stopColor="#0B0907" stopOpacity="0" />
          </radialGradient>

          {/* Left Edge Glass Highlight */}
          <linearGradient id="glassEdgeHighlight" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stopColor="#ECC480" stopOpacity="0.6" />
            <stop offset="50%" stopColor="#ECC480" stopOpacity="0.2" />
            <stop offset="100%" stopColor="#C4A265" stopOpacity="0.5" />
          </linearGradient>

          {/* Right Edge Glass Shadow */}
          <linearGradient id="glassRightEdge" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stopColor="#ECC480" stopOpacity="0.3" />
            <stop offset="100%" stopColor="#8A6C32" stopOpacity="0.1" />
          </linearGradient>

          {/* Label Plate Metallic Border */}
          <linearGradient id="labelBorderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#ECC480" />
            <stop offset="50%" stopColor="#8A6C32" />
            <stop offset="100%" stopColor="#D4AF37" />
          </linearGradient>

          {/* Moving Light Specular Sweep */}
          <linearGradient id="lightSweep" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor="#FFFFFF" stopOpacity="0" />
            <stop offset="45%" stopColor="#FFFFFF" stopOpacity="0" />
            <stop offset="50%" stopColor="#FFFFFF" stopOpacity="0.35" />
            <stop offset="55%" stopColor="#ECC480" stopOpacity="0.2" />
            <stop offset="100%" stopColor="#FFFFFF" stopOpacity="0" />
          </linearGradient>

          {/* Bottom Mist Fade Mask */}
          <linearGradient id="bottomFadeMask" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stopColor="white" stopOpacity="1" />
            <stop offset="85%" stopColor="white" stopOpacity="1" />
            <stop offset="100%" stopColor="white" stopOpacity="0.6" />
          </linearGradient>
        </defs>

        <g mask="url(#bottomFadeMask)">
          {/* ============================================================ */}
          {/* 1. THE HEAVY CRYSTAL BASE (قاعدة الزجاج الكريستالي الثقيل) */}
          {/* ============================================================ */}
          <path
            d="M 52 470 C 52 485, 65 500, 85 500 L 235 500 C 255 500, 268 485, 268 470 L 270 430 L 50 430 Z"
            fill="#12100E"
          />
          {/* Crystal Base Refraction Highlights */}
          <path
            d="M 68 465 L 252 465 L 244 485 L 76 485 Z"
            fill="#E5B25D"
            fillOpacity="0.15"
          />
          <line
            x1="85"
            y1="495"
            x2="235"
            y2="495"
            stroke="#ECC480"
            strokeWidth="1.5"
            strokeOpacity="0.4"
          />

          {/* ============================================================ */}
          {/* 2. THE MAIN BOTTLE BODY (جسم الزجاجة الأوبسيديان المنحوت) */}
          {/* ============================================================ */}
          {/* Fluted / Sculpted Rounded Rectangle Body */}
          <path
            d="M 60 170 C 50 170, 48 185, 48 200 L 52 470 C 52 485, 65 500, 85 500 L 235 500 C 255 500, 268 485, 268 470 L 272 200 C 272 185, 270 170, 260 170 Z"
            fill="url(#obsidianGlass)"
            stroke="#C4A265"
            strokeWidth="1"
            strokeOpacity="0.3"
          />

          {/* Inner Liquid Scent Luminescence */}
          <ellipse cx="160" cy="360" rx="95" ry="115" fill="url(#liquidEssence)" />

          {/* Left Vertical Precision Glass Highlight Line */}
          <path
            d="M 54 185 L 56 470"
            stroke="url(#glassEdgeHighlight)"
            strokeWidth="2.5"
            strokeLinecap="round"
          />
          <path
            d="M 64 175 L 68 475"
            stroke="#ECC480"
            strokeWidth="0.8"
            strokeOpacity="0.3"
          />

          {/* Right Edge Precision Highlight Line */}
          <path
            d="M 266 185 L 264 470"
            stroke="url(#glassRightEdge)"
            strokeWidth="2"
            strokeLinecap="round"
          />

          {/* ============================================================ */}
          {/* 3. THE LUXURY EMBOSSED LABEL (لوحة الشعار الذهبية المحفورة) */}
          {/* ============================================================ */}
          <g transform="translate(85, 250)">
            {/* Label Dark Surface Plate */}
            <rect
              x="0"
              y="0"
              width="150"
              height="160"
              rx="6"
              fill="#0A0908"
              stroke="url(#labelBorderGrad)"
              strokeWidth="1.5"
            />
            {/* Inner Gold Thin Border */}
            <rect
              x="5"
              y="5"
              width="140"
              height="150"
              rx="4"
              fill="none"
              stroke="#C4A265"
              strokeWidth="0.6"
              strokeOpacity="0.5"
            />

            {/* Brand Logo "O R Q" */}
            <text
              x="75"
              y="50"
              textAnchor="middle"
              fill="url(#goldCapGrad)"
              fontSize="26"
              fontFamily="serif"
              fontWeight="bold"
              letterSpacing="6"
            >
              ORQ
            </text>

            {/* Perfume Name */}
            <text
              x="75"
              y="80"
              textAnchor="middle"
              fill="#F2EBDD"
              fontSize="14"
              fontWeight="bold"
              letterSpacing="3"
            >
              ROYAL OUD
            </text>

            {/* Arabic Name */}
            <text
              x="75"
              y="102"
              textAnchor="middle"
              fill="#ECC480"
              fontSize="12"
              fontWeight="500"
            >
              متمرد
            </text>

            {/* Divider Line */}
            <line x1="45" y1="116" x2="105" y2="116" stroke="#C4A265" strokeWidth="0.8" strokeOpacity="0.6" />

            {/* Concentration & Volume */}
            <text
              x="75"
              y="134"
              textAnchor="middle"
              fill="#A0988C"
              fontSize="8.5"
              fontFamily="monospace"
              letterSpacing="1.5"
            >
              EAU DE PARFUM
            </text>
            <text
              x="75"
              y="146"
              textAnchor="middle"
              fill="#A0988C"
              fontSize="8"
              fontFamily="monospace"
              letterSpacing="1"
            >
              100 ML • 3.4 FL.OZ
            </text>
          </g>

          {/* ============================================================ */}
          {/* 4. THE SHOULDERS & COLLAR (أكتاف الزجاج وعنق الزجاجة) */}
          {/* ============================================================ */}
          {/* Curved Bottle Shoulders */}
          <path
            d="M 60 170 C 85 165, 115 155, 130 145 L 190 145 C 205 155, 235 165, 260 170 Z"
            fill="#1E1914"
            stroke="#ECC480"
            strokeWidth="0.6"
            strokeOpacity="0.4"
          />

          {/* Neck Ring (حلقة العنق المعدنية المذهبة) */}
          <rect
            x="132"
            y="132"
            width="56"
            height="14"
            rx="2"
            fill="url(#goldCapGrad)"
            stroke="#4A3816"
            strokeWidth="0.5"
          />

          {/* ============================================================ */}
          {/* 5. THE SOLID GOLD CYLINDRICAL CAP (الغطاء المعدني الأسطواني الفاخر) */}
          {/* ============================================================ */}
          {/* Cap Base Cylinder */}
          <rect
            x="122"
            y="42"
            width="76"
            height="90"
            rx="4"
            fill="url(#goldCapGrad)"
            stroke="#4A3816"
            strokeWidth="0.8"
          />

          {/* Metallic Micro-ridges / Textures on Cap */}
          <line x1="126" y1="55" x2="194" y2="55" stroke="#F5E0B7" strokeWidth="0.5" strokeOpacity="0.6" />
          <line x1="126" y1="115" x2="194" y2="115" stroke="#4A3816" strokeWidth="0.8" strokeOpacity="0.8" />

          {/* Cap Beveled Top Ellipse */}
          <ellipse
            cx="160"
            cy="42"
            rx="38"
            ry="9"
            fill="url(#capTopGrad)"
            stroke="#F5E0B7"
            strokeWidth="0.8"
          />
          {/* Engraved Monogram on Cap Top */}
          <ellipse
            cx="160"
            cy="42"
            rx="24"
            ry="5.5"
            fill="none"
            stroke="#8A6C32"
            strokeWidth="0.6"
          />

          {/* ============================================================ */}
          {/* 6. SPECULAR LIGHT SWEEP (شعاع انعكاس الضوء السينمائي المتحرك) */}
          {/* ============================================================ */}
          <rect
            x="45"
            y="40"
            width="230"
            height="465"
            fill="url(#lightSweep)"
            className={`pointer-events-none transition-transform duration-1000 ${
              isRevealing ? 'translate-x-full' : '-translate-x-full'
            }`}
          />
        </g>
      </svg>
    </div>
  );
}

