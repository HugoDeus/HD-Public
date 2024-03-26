<?php 

include('connection.php');
 
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

    <div class="container-fluid">
        <div class="text-center my-4">
            <h2 class="text-success fw-bolder">Admin Page</h2>
        </div>
    </div>

    <div class="container mb-2 bg-secondary">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link nav-admin" href="users.php">Users</a>
            </li>
            <li class="nav-item nav-admin">
                <a class="nav-link activeadmin" href="orders.php">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="message.php">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="adminbooks.php">Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="admin.php">Add Books</a>
            </li>
        </ul>
    </div>

    <main>
        <div class="container alert-dark mt-5">
            <h2 class="text-center mb-4">Admin Orders</h2>
            <?php
            $sql = "SELECT * FROM transactions ORDER BY transaction_date DESC";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='accordion mb-3' id='accordionOrder{$row['id']}'>";
                    echo "<div class='accordion-item'>";
                    echo "<h2 class='accordion-header' id='headingOrder{$row['id']}'>";
                    echo "<button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapseOrder{$row['id']}' aria-expanded='true' aria-controls='collapseOrder{$row['id']}'>";
                    echo "Order ID: {$row['id']} - Date: {$row['transaction_date']} - Total Price: {$row['total_value']} €";
                    echo "</button>";
                    echo "</h2>";
                    echo "<div id='collapseOrder{$row['id']}' class='accordion-collapse collapse' aria-labelledby='headingOrder{$row['id']}' data-bs-parent='#accordionOrder{$row['id']}'>";
                    echo "<div class='accordion-body d-flex justify-content-between'>";
                    echo "<a href='admin_order_details.php?id={$row['id']}' class='btn-sm btn-primary'>View Details</a>";
                    echo "Costumer: {$row['customer_name']}";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>No orders found.</p>";
            }
            ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>
