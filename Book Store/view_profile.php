<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql_get_user = "SELECT id, firstname, lastname, email, user, permissions FROM users WHERE id = $user_id";
    $result = $conn->query($sql_get_user);

    $sql_get_contact = "SELECT * FROM contacts WHERE user_id = $user_id";
    $result_contacts = $conn->query($sql_get_contact);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuário não encontrado.";
        exit();
    }
} else {
    echo "ID do usuário não especificado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['permission'])) {
        $selected_permission = $_POST['permission'];
        $sql_update_permissions = "UPDATE users SET permissions = '$selected_permission' WHERE id = $user_id";
        $conn->query($sql_update_permissions);
        header("location: view_profile.php?id=$user_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
    <div>
        <a href="users.php" class="btn btn-primary">&#8592; Back</a>
    </div>
    <div class="container-fluid">
        <div class="text-center mt-3">
            <h2 class="text-success">User Details</h2>
        </div>
    </div>

    <div class="container">
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title text-success text-center"><?php echo $user['firstname'] . " " . $user['lastname']; ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p class="card-text"><strong>Username:</strong> <?php echo $user['user']; ?></p>
                <hr>
                <?php
                if ($result_contacts->num_rows < 1) {
                ?>
                    <h3 class="text-center">User no have contatcs details</h3>
                <?php
                } else {
                    $contact = $result_contacts->fetch_assoc();
                ?>
                    <p class="card-text"><strong>Address:</strong> <?php echo $contact['address']; ?></p>
                    <p class="card-text"><strong>Door/Floor:</strong> <?php echo $contact['door_floor']; ?></p>
                    <p class="card-text"><strong>Zip-Code:</strong> <?php echo $contact['zipcode']; ?></p>
                    <p class="card-text"><strong>City:</strong> <?php echo $contact['city']; ?></p>
                    <p class="card-text"><strong>Nif:</strong> <?php echo $contact['nif']; ?></p>
                    <p class="card-text"><strong>Telephone:</strong> <?php echo $contact['phone']; ?></p>
                    <p class="card-text"><strong>Registered date:</strong> <?php echo $contact['date']; ?></p>
                <?php
                }
                ?>
                <hr>
                <div class="container">
                    <form method="post" action="">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <input type="radio" name="permission" id="make_admin" value="Admin" <?php
                                                                                                    if ($user['permissions'] == 'Admin') {
                                                                                                    ?> checked <?php
                                                                                                            }
                                                                                                                ?>>
                                <label for="make_admin">Make Admin</label>
                            </div>
                            <div class="col-auto">
                                <input type="radio" name="permission" id="make_user" value="User" <?php
                                                                                                    if ($user['permissions'] == 'User') {
                                                                                                    ?> checked <?php
                                                                                                            }
                                                                                                                ?>>
                                <label for="make_user">Make User</label>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-3">
                            <div class="col-auto">
                                <button class="btn btn-success" type="button" onclick="confirmPermission()">Save</button>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-3" id="confirmation" style="display: none;">
                            <div class="col-auto">
                                <p class="bg-danger text-white text-center">Are you sure you want to change the privileges of this account?</p>
                                <div class="text-center">
                                    <button class="btn btn-primary" type="submit">Confirm</button>
                                    <button class="btn btn-danger" type="button" onclick="cancelConfirmation()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title text-center">Orders History</h5>
                <hr>
                <?php
                $sql_orders = "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY transaction_date DESC";
                $result_orders = $conn->query($sql_orders);

                if ($result_orders->num_rows > 0) {
                    while ($order = $result_orders->fetch_assoc()) {
                        echo '<div class="accordion" id="orderAccordion">';
                        echo '<div class="accordion-item">';
                        echo '<h2 class="accordion-header" id="heading' . $order['id'] . '">';
                        echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $order['id'] . '" aria-expanded="true" aria-controls="collapse' . $order['id'] . '">';
                        echo 'Order ID: ' . $order['id'] . ' | Date: ' . $order['transaction_date'] . ' | Total Price: ' . $order['total_value'] . ' €';
                        echo '</button>';
                        echo '</h2>';
                        echo '<div id="collapse' . $order['id'] . '" class="accordion-collapse collapse" aria-labelledby="heading' . $order['id'] . '" data-bs-parent="#orderAccordion">';
                        echo '<div class="accordion-body">';
                        echo '<a href="admin_order_details.php?id=' . $order['id'] . '">View Details</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No orders found for this user.</p>';
                }
                ?>
            </div>
        </div>

    </div>

    <div class="container">
        <p class="text-center mt-4"><a href="users.php"><button class="btn btn-danger">Exit -> Go Back Users</button></a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function confirmPermission() {
            document.getElementById('confirmation').style.display = 'block';
        }

        function cancelConfirmation() {
            document.getElementById('confirmation').style.display = 'none';
        }
    </script>
</body>

</html>