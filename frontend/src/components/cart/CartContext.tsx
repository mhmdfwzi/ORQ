'use client';

import React, { createContext, useContext, useState } from 'react';
import { CartItem, Product } from '@/types/product';

interface CartContextType {
  items: CartItem[];
  isOpen: boolean;
  openCart: () => void;
  closeCart: () => void;
  addToCart: (product: Product, size: '50ml' | '100ml', quantity?: number) => void;
  addPairToCart: (first: Product, second: Product) => boolean;
  removeFromCart: (slug: string, size: '50ml' | '100ml') => void;
  updateQuantity: (slug: string, size: '50ml' | '100ml', quantity: number) => void;
  clearCart: () => void;
  restoreCart: (savedItems: CartItem[]) => void;
  totalAmount: number;
  totalItems: number;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

function purchasableItem(product: Product, size: CartItem['size'], quantity: number): CartItem | undefined {
  const variant = product.variants.find((item) => item.size === size && item.inStock);
  if (size !== '50ml' || !variant) return undefined;

  return { slug: product.slug, name: product.name, size, price: variant.price, quantity, image: product.image };
}

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);
  const [isOpen, setIsOpen] = useState<boolean>(false);

  const openCart = () => setIsOpen(true);
  const closeCart = () => setIsOpen(false);

  const addItems = (additions: CartItem[]) => {
    setItems((current) => additions.reduce((next, addition) => {
      const existing = next.findIndex((item) => item.slug === addition.slug && item.size === addition.size);
      if (existing === -1) return [...next, addition];
      return next.map((item, index) => index === existing ? { ...item, quantity: item.quantity + addition.quantity } : item);
    }, current));
    setIsOpen(true);
  };

  const addToCart = (product: Product, size: CartItem['size'], quantity = 1) => {
    const item = purchasableItem(product, size, quantity);
    if (item) addItems([item]);
  };

  const addPairToCart = (first: Product, second: Product) => {
    const firstItem = purchasableItem(first, '50ml', 1);
    const secondItem = purchasableItem(second, '50ml', 1);
    if (!firstItem || !secondItem) return false;
    addItems([firstItem, secondItem]);
    return true;
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
        addPairToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        restoreCart: (savedItems) => {
          setItems((current) => current.length ? current : savedItems.filter((item) => item.size === '50ml'));
          openCart();
        },
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
