<?php

namespace BookShop;

SessionContext::create();

class ShoppingCart extends BaseObject {
    public static function  add(int $bookId) {
        $cart = self::getCart();
        $cart[$bookId] = $bookId;
        self::storeCart($cart);
    }

    public static function  remove(int $bookId) {
        $cart = self::getCart();
        unset($cart[$bookId]);
        self::storeCart($cart);
    }

    public static function clear() {
        self::storeCart(array());
    }

    public static function contains(int $bookId) : bool {
        $cart = self::getCart();
        return array_key_exists($bookId, $cart);
    }

    public static function size() : int {
        return sizeof(self::getCart());
    }

    public static function getAll() : array {
        return self::getCart();
    }

    public static function getCart() : array {
        // hier machen wir unseren Zustand, unser abstrahiertes Sessionobjekt
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    }

    public static function storeCart(array $cart) {
        // hier machen wir unseren Zustand, unser abstrahiertes Sessionobjekt
        $_SESSION['cart'] = $cart;
    }
}
