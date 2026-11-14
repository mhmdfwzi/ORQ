'use client';

import React, { createContext, useContext, useState } from 'react';
import { CartItem, Product } from '@/types/product';

interface CartContextType {
  items: CartItem[];
  isOpen: boolean;
  openCart: () => void;
  closeCart: () => void;
  addToCart: (product: Product, size: '50ml' | '100ml', quantity?: number) => void;
  removeFromCart: (slug: string, size: '50ml' | '100ml') => void;
  updateQuantity: (slug: string, size: '50ml' | '100ml', quantity: number) => void;
  clearCart: () => void;
  totalAmount: number;
  totalItems: number;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);
  const [isOpen, setIsOpen] = useState<boolean>(false);

  const openCart = () => setIsOpen(true);
  const closeCart = () => setIsOpen(false);

  const addToCart = (product: Product, size: '50ml' | '100ml', quantity = 1) => {
    const variant = product.variants.find((v) => v.size === size) || product.variants[0];
    const price = variant ? variant.price : 1290;

    setItems((prevItems) => {
      const existing = prevItems.find((item) => item.slug === product.slug && item.size === size);
      if (existing) {
        return prevItems.map((item) =>
          item.slug === product.slug && item.size === size
            ? { ...item, quantity: item.quantity + quantity }
            : item
        );
      }
      return [
        ...prevItems,
        {
          slug: product.slug,
          name: product.name,
          size,
          price,
          quantity,
          image: product.image
        }
      ];
    });
    setIsOpen(true);
  };

  const removeFromCart = (slug: string, size: '50ml' | '100ml') => {
    setItems((prev) => prev.filter((item) => !(item.slug === slug && item.size === size)));
  };

  const updateQuantity = (slug: string, size: '50ml' | '100ml', quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(slug, size);
      return;
    }
    setItems((prev) =>
      prev.map((item) => (item.slug === slug && item.size === size ? { ...item, quantity } : item))
    );
  };

  const clearCart = () => setItems([]);

  const totalAmount = items.reduce((acc, item) => acc + item.price * item.quantity, 0);
  const totalItems = items.reduce((acc, item) => acc + item.quantity, 0);

  return (
    <CartContext.Provider
      value={{
        items,
        isOpen,
        openCart,
        closeCart,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        totalAmount,
        totalItems
      }}
    >
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
}
