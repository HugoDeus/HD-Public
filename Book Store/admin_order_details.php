<?php
include('connection.php');
include('html_pages.php');

session_start();

include('restricted.php');
checkPermissions();

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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
     <!-- Navbar -->
     <nav class="navbar navbar-expand-sm navbar-light bg-success">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">Homepage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="books.php">Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="about.php">About Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main>
    <div>
        <a href="javascript:history.go(-1)" class="btn btn-primary">&#8592; Back</a>
    </div>
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-success">Order Details</h2>
        <?php
        if (isset($_GET['id'])) {
            $transaction_id = $_GET['id'];
            $sql = "SELECT * FROM transactions WHERE id = $transaction_id";
            $result = mysqli_query($conn, $sql);

            // Transaction details
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "<div class='container alert-secondary'>";
                echo "<div class='row mb-3'>";
                echo "<div class='col-md-6'>";
                echo "<p class='fs-2 text-center fw-bolder'>Transaction ID: {$row['id']}</p>";
                echo "<p>Transaction Date: {$row['transaction_date']}</p>";
                echo "<p class='fw-bolder'>Total Price: {$row['total_value']} €</p>";
                echo "</div>";
                echo "</div>";
                echo "<hr>";
                // Items
                $sql_items = "SELECT * FROM transaction_items WHERE transaction_id = $transaction_id";
                $result_items = mysqli_query($conn, $sql_items);

                if ($result_items && mysqli_num_rows($result_items) > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-secondary'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Product Name</th>";
                    echo "<th>Author</th>";
                    echo "<th>Quantity</th>";
                    echo "<th>Unit Price</th>";
                    echo "<th>Total</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while ($item = mysqli_fetch_assoc($result_items)) {
                        echo "<tr>";
                        echo "<td>{$item['product_name']}</td>";
                        echo "<td>{$item['author']}</td>";
                        echo "<td>{$item['quantity']}</td>";
                        echo "<td class='text-nowrap'>{$item['unit_price']} €</td>";
                        echo "<td class='text-nowrap'>{$item['total']} €</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No items found for this order.</p>";
                }
                // Shipping details
                $sql_shipping = "SELECT * FROM shipments WHERE transaction_id = $transaction_id";
                $result_shipping = mysqli_query($conn, $sql_shipping);

                if ($result_shipping && mysqli_num_rows($result_shipping) > 0) {
                    $row_shipping = mysqli_fetch_assoc($result_shipping);
                    echo "<hr>";
                    echo "<div class='row mb-3'>";
                    echo "<div class='col-md-6'>";
                    echo "<p class='fs-2 text-center fw-bolder'>Shipping Details:</p>";
                    echo "<p>Name: {$row_shipping['firstname']} {$row_shipping['lastname']}</p>";
                    echo "<p>Email: {$row_shipping['email']}</p>";
                    echo "<p>Nif: {$row_shipping['nif']}</p>";
                    echo "<p>Address: {$row_shipping['address']}</p>";
                    echo"<p>Door/Floor: {$row_shipping['door_floor']}</p>";
                    echo "<p>City: {$row_shipping['city']}</p>";
                    echo "<p>Zip Code: {$row_shipping['zipcode']}</p>";
                    echo "<p>Country: {$row_shipping['country']}</p>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "<p class='text-center text-danger fw-bolder fs-2'>No shipping details found for this order.</p>";
                }
            } else {
                echo "<p class='text-center text-danger fw-bolder fs-2'>Order not found.</p>";
            }
        } else {
            echo "<p class='text-center text-danger fw-bolder fs-2'>No order ID specified.</p>";
        }
        ?>
        <div class="text-center">
        <a href="javascript:history.go(-1)" class="btn btn-danger">&#8592; Back</a>
    </div>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>