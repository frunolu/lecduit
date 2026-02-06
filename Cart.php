<?php
class Cart {
    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $this->clear();
        }
    }

    /**
     * Přidání zážitku do košíku
     */
    public function add($id, $title, $price) {
        global $market_id;

        // Pokud uživatel změnil doménu (market), vymažeme starý košík v jiné měně
        if ($_SESSION['cart_market'] !== $market_id) {
            $this->clear();
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id'    => $id,
                'title' => $title,
                'price' => $price,
                'qty'   => 1
            ];
        }
    }

    /**
     * Odstranění položky
     */
    public function remove($id) {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    /**
     * Celková suma košíku
     */
    public function getTotal() {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }

    /**
     * Počet kusů v košíku (pro ikonku v menu)
     */
    public function getCount() {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['qty'];
        }
        return $count;
    }

    public function getItems() {
        return $_SESSION['cart'];
    }

    public function clear() {
        global $market_id;
        $_SESSION['cart'] = [];
        $_SESSION['cart_market'] = $market_id;
    }
}
