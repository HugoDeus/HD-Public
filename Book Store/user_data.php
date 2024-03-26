<?php

include('connection.php');
include("update_cart.php");

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

$cartInfo = calculateCart();

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $name = $_SESSION['firstname'];
    $last = $_SESSION['lastname'];
    $email = $_SESSION['email'];
}


?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
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
            <div class="welcome">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



    <header>

        <!-- Welcome  -->
        <div class="container-fluid mx-0 row justify-content-between align-items-center bgspace">
            <div class="col-sm-6 col-4 text-start welcome">
                <span class="text-user my-1">Welcome, <?php echo $userName ?? 'Guest'; ?></span>
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="btn btn-sm btn-success text-nowrap my-1" href="user_data.php?id=<?php echo $id; ?>">My Profile</a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 col-4 text-end">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <?php if ($_SESSION['permissions'] === 'Admin') : ?>
                        <a href="message.php" class="btn btn-sm btn-success my-1">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm btn-danger text-nowrap my-1">Log out</a>
                <?php else : ?>
                    <a href="login.php" class="btn btn-info btn-sm my-1" data-bs-toggle="modal" data-bs-target="#loginmodal" type="button" role="tab" aria-controls="loginmodal" aria-selected="false">Login/Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="container mt-5 alert-secondary rounded-3">
            <div class="d-flex justify-content-around">
                <a href="edit_userdata.php" class="btn btn-success mb-5 mt-2">Update your details</a>
                <button type="button" class="btn btn-primary mb-5 mt-2" data-bs-toggle="modal" data-bs-target="#purchaseHistoryModal">
                    Purchase History
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="name" class="form-label">First Name:</label>
                    <span class="form-control bg-dark text-white" id="name"><?php echo $name; ?></span>
                </div>
                <div class="col-md-6">
                    <label for="last" class="form-label">Last Name:</label>
                    <span class="form-control bg-dark text-white" id="last"><?php echo $last; ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email:</label>
                    <span class="form-control bg-dark text-white" id="email"><?php echo $email; ?></span>
                </div>
            </div>
            <?php
            $sql = "SELECT * FROM contacts WHERE user_id = $id";
            $sql_query = $conn->query($sql);
            if (mysqli_num_rows($sql_query) == 0) {
            ?>
                <div class="col-md-12 mt-5 text-center">
                    <h2>You have no contact details</h2>
                    <a href="details_contact.php" class="btn btn-success mt-2">Add contact details</a>
                </div>
            <?php
            } else {
                $row = mysqli_fetch_assoc($sql_query);
            ?>
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['address']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <label for="door_floor" class="form-label">Door/Floor:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['door_floor']; ?></span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">City:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['city']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <label for="zipcode" class="form-label">Zip-Code:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['zipcode']; ?></span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telephone:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['phone']; ?></span>
                        </div>
                        <div class="col-md-6 mb-5">
                            <label for="nif" class="form-label">NIF:</label>
                            <span class="form-control bg-dark text-white"><?php echo $row['nif']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="index.php" class="btn btn-success mb-5">Go Back to Homepage</a>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="purchaseHistoryModal" tabindex="-1" aria-labelledby="purchaseHistoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-success">
                        <h5 class="modal-title" id="purchaseHistoryModalLabel">Purchase History Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        $id = $_SESSION['id'];
                        $sql = "SELECT * FROM transactions WHERE user_id = $id ORDER BY transaction_date DESC";
                        $result = mysqli_query($conn, $sql);

                        if (!$result) {
                            echo "ERROR trying to acess history .";
                        } else {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<div class='table-responsive'>";
                                echo "<table class='table table-secondary' border='1'>";
                                echo "<tr><th>Number Transaction</th><th>Transaction Date</th><th>Total Price</th><th>Details</th></tr>";
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['transaction_date'] . "</td>";
                                    echo "<td>" . $row['total_value'] . "</td>";
                                    echo "<td><a class=btn-sm btn-info href='transaction_details.php?id=" . $row['id'] . "'>Details</a></td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                                echo "</div>";
                            } else {
                                echo "<p>You dont have purchases.</p>";
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>