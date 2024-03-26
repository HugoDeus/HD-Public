<?php 
include("connection.php");
include("html_pages.php");
session_start();

function getUserName()
{
    if (isset($_SESSION['user_name'])) {
        return $_SESSION['user_name'];
    } elseif (isset($_SESSION['permissions'])) {
        switch ($_SESSION['permissions']) {
            case 'Admin':
                return 'Admin';
            case 'User':
                return 'User';
            default:
                return 'Guest';
        }
    } else {
        return 'Guest';
    }
}

$userName = getUserName();

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['nif']) && isset($_POST['address']) && isset($_POST['door_floor']) && isset($_POST['city']) && isset($_POST['zipcode']) && isset($_POST['country']) && isset($_POST['paymentMethod'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $nif = $_POST['nif'];
    $address = $_POST['address'];
    $door_floor = $_POST['door_floor'];
    $city = $_POST['city'];
    $zipcode = $_POST['zipcode'];
    $country = $_POST['country'];
    $paymentMethod = $_POST['paymentMethod'];
    $finalPrice = $_POST['finalPrice'];
    $costumerName = $firstname . " " . $lastname;

    if (isset($_SESSION['cart_products'])) {
        $products = $_SESSION['cart_products'];
        mysqli_begin_transaction($conn);
        $error = false;
        
        $sql_transactions = "INSERT INTO transactions (user_id, customer_name, payment_method, total_value, country, transaction_date) VALUES ('$id', '$costumerName', '$paymentMethod', '$finalPrice', '$country', NOW())";
        $result_transactions = mysqli_query($conn, $sql_transactions);

        if (!$result_transactions) {
            $error = true;
        }

        $transaction_id = mysqli_insert_id($conn);
        
        $sql_shipments = "INSERT INTO shipments (transaction_id, firstname, lastname, email, nif, address, door_floor, city, zipcode, country) VALUES ('$transaction_id', '$firstname', '$lastname', '$email', '$nif', '$address', '$door_floor', '$city', '$zipcode', '$country')";
        $result_shipments = mysqli_query($conn, $sql_shipments);

        if (!$result_shipments) {
            $error = true;
        }

        $sql_values = "";
        foreach ($products as $productName => $productData) {
            $author = $productData['author'];
            $image = $productData['image'];
            $quantity = $productData['quantity'];
            $price = $productData['price'];
            $total = number_format($productData['total'],2);

            $sql_values .= "('$transaction_id', '$productName', '$author', '$image', '$quantity', '$price', '$total'),";
        }

        $sql_values = rtrim($sql_values, ',');
        $sql_order = "INSERT INTO transaction_items (transaction_id, product_name, author, image, quantity, unit_price, total) VALUES $sql_values";
        $result_order = mysqli_query($conn, $sql_order);

        if (!$result_order || $error) {
            mysqli_rollback($conn);
            echo "<div class='container text-center my-auto'>";
            echo "ERROR: NO ORDER WAS MADE. PLEASE TRY AGAIN.";
            echo "<a class='btn btn-danger' href='index.php'>Go to Homepage</a>";
            echo "</div>";
            exit;
        } else {
            mysqli_commit($conn);
            echo "<h1 class='text-center text-success mt-5'>Order Complete Successfully.</h1>";
            echo "<h2 class='text-center text-success'>Thank you for your purchase.</h2>";
            unset($_SESSION['cart_products']);
            unset($_SESSION['cart']);
            unset($_SESSION['cart_order']);
            echo "<div class='container my-auto'>";
            echo "<div class=''table-responsive''>";
            echo "<h2 class='text-center text-success bg-white'>Order Details</h2>";
            echo "<table class='table table-success'>";
            echo "<tr>";
            echo "<th>Product</th>";
            echo "<th>Quantity</th>";
            echo "<th>Unit Price</th>";
            echo "<th>Total Price</th>";
            echo "</tr>";
            foreach ($products as $productName => $productData) {
                echo "<tr>";
                echo "<td>" . $productName . "</td>";
                echo "<td>" . $productData['quantity'] . "</td>";
                echo "<td>" . $productData['price'] . "€</td>";
                echo "<td>" . number_format($productData['total'],2) . "€</td>";
                echo "</tr>";
            }
            echo "<tr style='font-weight: bolder;'>";
            echo "<td colspan='3'>Total</td>";
            echo "<td>" . number_format($finalPrice,2) . "€</td>";
            echo "</tr>";
            echo "</table>";
            echo "<div class=bg-secondary text-white'>";
            echo "<h2 class='text-center text-success bg-white'>Shipping Details</h2>";
            echo "<div class='text-white m-2'>";
            echo "<p>Name: " . $firstname . " " . $lastname . "</p>";
            echo "<p>Email: " . $email . "</p>";
            echo "<p>NIF: " . $nif . "</p>";
            echo "<p>Address: " . $address . ", " . $door_floor . "</p>";
            echo "<p>City: " . $city . "</p>";
            echo "<p>Zip-Code: " . $zipcode . "</p>";
            echo "<p>Country: " . $country . "</p>";
            echo "</div>";
            echo "</div>";
            echo "<div class=bg-secondary text-white'>";
            echo "<h2 class='text-center text-success bg-white'>Payment Details</h2>";
            echo "<div class='text-white m-2'>";
            echo "<p>Payment Method: " . $paymentMethod . "</p>";
            echo "<p>Total Price: " . number_format($finalPrice,2) . "€</p>";
            echo "<p>Transaction Date: " . date('Y-m-d H:i:s') . "</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "<div class='text-center mb-4'>";
            echo "<a class='btn btn-success' href='index.php'>Go to Homepage</a>";
            echo "</div>";

            exit;
        }
    } else {
        echo "ERROR: No order was made. Please try again.";
        echo "<a href='index.php'>Go to Homepage</a>";
        exit;
    }
} 
?>
