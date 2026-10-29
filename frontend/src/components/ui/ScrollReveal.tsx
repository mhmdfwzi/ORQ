'use client';

import React, { useEffect, useRef, useState } from 'react';

interface ScrollRevealProps {
  children: React.ReactNode;
  delay?: number; // in milliseconds
  direction?: 'up' | 'down' | 'left' | 'right' | 'fade' | 'zoom';
  className?: string;
  threshold?: number;
}

export default function ScrollReveal({
  children,
  delay = 0,
  direction = 'up',
  className = '',
  threshold = 0.12,
}: ScrollRevealProps) {
  const [isVisible, setIsVisible] = useState(false);
  const ref = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;

    // Respect user's reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      setIsVisible(true);
      return;
    }

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true);
          observer.unobserve(entry.target);
        }
      },
      {
        threshold,
        rootMargin: '0px 0px -40px 0px',
      }
    );

    observer.observe(el);

    return () => {
      observer.disconnect();
    };
  }, [threshold]);

  // Direction transform styles
  const getTransform = () => {
    if (isVisible) return 'none';
    switch (direction) {
      case 'up':
        return 'translateY(28px)';
      case 'down':
        return 'translateY(-28px)';
      case 'left':
        return 'translateX(28px)';
      case 'right':
        return 'translateX(-28px)';
      case 'zoom':
        return 'scale(0.94)';
      case 'fade':
      default:
        return 'none';
    }
  };

  return (
    <div
      ref={ref}
      style={{
        transitionDuration: '850ms',
        transitionDelay: `${delay}ms`,
        transitionTimingFunction: 'cubic-bezier(0.16, 1, 0.3, 1)',
        transform: getTransform(),
        opacity: isVisible ? 1 : 0,
        filter: isVisible ? 'none' : 'blur(4px)',
      }}
      className={`transition-all ${className}`}
    >
      {children}
    </div>
  );
}

