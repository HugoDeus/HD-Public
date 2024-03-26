<?php

include('connection.php');
include('update_cart.php');

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
    
    $sql_users = "SELECT * FROM users WHERE id = $id";
    $query_users = $conn->query($sql_users);
    $user_row = mysqli_fetch_assoc($query_users);
    
    $sql_contacts = "SELECT * FROM contacts WHERE user_id = $id";
    $query_contacts = $conn->query($sql_contacts);
    $contact_row = mysqli_fetch_assoc($query_contacts);
}
$cartInfo = calculateCart();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subcategory</title>
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

        <!-- Welcome -->
        <div class="container-fluid mx-0 row justify-content-between align-items-center bgspace mb-3">
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
    <main class="my-auto">
        <form action="update_user_data.php" method="post">
            <div class="container alert-secondary rounded-3">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label">First Name:</label>
                        <input type="text" class="form-control" id="name" name="firstname" value="<?php echo $user_row['firstname']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="last" class="form-label">Last Name:</label>
                        <input type="text" class="form-control" id="last" name="lastname" value="<?php echo $user_row['lastname']; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_row['email']; ?>">
                    </div>
                    <?php 
                    if (!empty($contact_row == 0)){
                        header('Location: details_contact.php');
                    } else {
                    ?>
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $contact_row['address']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="door_floor" class="form-label">Door/Floor:</label>
                                <input type="text" class="form-control" id="door_floor" name="door_floor" value="<?php echo $contact_row['door_floor']; ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City:</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo $contact_row['city']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="zipcode" class="form-label">Zip-Code:</label>
                                <input type="number" class="form-control" id="zipcode" name="zipcode" value="<?php echo $contact_row['zipcode']; ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telephone:</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $contact_row['phone']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="nif" class="form-label">NIF:</label>
                                <input type="number" class="form-control" id="nif" name="nif" value="<?php echo $contact_row['nif']; ?>">
                            </div>
                        </div>
                        <?php 
                    }
                        ?>
                        <div class="text-center mt-3">
                            <a href="user_data.php" class="btn btn-danger me-2">Go Back</a>
                            <input type="submit" class="btn btn-primary ms-2" value="Update Data">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
</body>

</html>
