<?php

include('connection.php');

function addToCart($productName, $price) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = ['name' => $productName, 'price' => $price];
}

function removeFromCart($productName) {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => &$item) {
            if ($item['name'] === $productName) {
                if ($item['quantity'] > 1) {
                    $item['quantity'] -= 1;
                } else {
                    unset($_SESSION['cart'][$key]);
                }
                break;
            }
        }
    }
}

function calculateCart() {
    $totalItems = 0;
    $totalPrice = 0;
    
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $totalItems++;
            $totalPrice += (float)$item['price']; 
        }
    }
    
    return ['totalItems' => $totalItems, 'totalPrice' => $totalPrice];
}

function update_cart($productName, $quantity) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productName]);
        return;
    }
    if (isset($_SESSION['cart'][$productName])) {
        $_SESSION['cart'][$productName]['quantity'] = $quantity;
    }
}
?>