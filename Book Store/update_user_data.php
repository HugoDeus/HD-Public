<?php

include('connection.php');
include('html_pages.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    $firstname = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['firstname']))));
    $lastname = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['lastname']))));
    $email = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['email']))));
    $address = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['address']))));
    $door_floor = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['door_floor']))));
    $city = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['city']))));
    $zipcode = $_POST['zipcode'];
    $phone = $_POST['phone'];
    $nif = $_POST['nif'];

    $update_user_query = "UPDATE users SET firstname='$firstname', lastname='$lastname', email='$email' WHERE id='$id'";
    $result_user_update = mysqli_query($conn, $update_user_query);

    $update_contact_query = "UPDATE contacts SET address='$address', door_floor='$door_floor', city='$city', zipcode='$zipcode', phone='$phone', nif='$nif' WHERE user_id='$id'";
    $result_contact_update = mysqli_query($conn, $update_contact_query);

    if ($result_user_update && $result_contact_update) {
?>
        <div class="container-fluid mt-5">
            <div class="row text-center">
                <h3 class="text-success">Update data succefully</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="user_data.php">Go to Account</a></button>
            </div>
        </div>
<?php
    } else {
        ?>
        <div class="container-fluid mt-5">
            <div class="row text-center">
                <h3 class="text-danger">Failed to update user data</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="user_data.php">Go to Account</a></button>
            </div>
        </div>
<?php
    }
} else {
    ?>
        <div class="container-fluid mt-5">
            <div class="row text-center">
                <h3 class="text-danger">Acess Denied</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="user_data.php">Go to Account</a></button>
            </div>
        </div>
<?php
}

?>