<?php 

include("connection.php");

session_start();

if(!isset($_SESSION['cart_order'])){
    $_SESSION['cart_order'] = [];
}
$_SESSION['cart_order']['$totalPrice'];

if(!isset($_SESSION['user_name'])){
    $_SESSION['user_name'];
}

?>