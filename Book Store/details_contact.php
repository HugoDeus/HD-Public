<?php

include('connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $address = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['address']))));
     $door_floor = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['door_floor']))));
     $city = ucfirst(strtoLower(trim(mysqli_real_escape_string($conn, $_POST['city']))));
     $zipcode = $_POST['zipcode'];
     $phone = $_POST['phone'];
     $nif = $_POST['nif'];
     $user_id = $_SESSION['id'];
 
     if (!empty($address) && !empty($door_floor) && !empty($city) && !empty($zipcode) && !empty($phone) && !empty($nif)) {
 
         $insert = "INSERT INTO  contacts (address, door_floor, city, zipcode, phone, nif, user_id, date) VALUES ('$address', '$door_floor', '$city', '$zipcode', '$phone', '$nif', '$user_id', NOW())";
         mysqli_query($conn, $insert);

         header('Location: user_data.php');
     }
 }
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container text-center mt-5 text-success">
        <h2 class="mb-4 fw-bolder">Details Contact</h2>
    </div>
    <form action="" method="post" class="container alert-secondary p-1 rounded-3">
        <div class="container col-md-12">
            <div class="row d-flex">
                <label for="address" class="form-label col align-self-center fs-4">Address:</label>
                <textarea class="col-9" type="text" name="address" placeholder="Write your Address" rows="4" style="resize: none;" required></textarea>
            </div>
        </div>
        <div class="container col-md-12 mt-4">
            <div class="row d-flex">
                <label for="" class="col form-label fs-4">Door/Floor:</label>
                <input class="col-9" type="text" name="door_floor" placeholder="Door/floor" required>
            </div>
        </div>
        <div class="container col-md-12 mt-4">
            <div class="row d-flex">
                <label for="" class="col form-label fs-4">City:</label>
                <input class="col-9" type="text" name="city" placeholder="City" required>
            </div>
        </div>
        <div class="container col-md-12 mt-4">
            <div class="row d-flex">
                <label for="" class="col form-label fs-4">Zip-Code:</label>
                <input class="col-9" type="number" name="zipcode" placeholder="Zip-Code" required>
            </div>
        </div>
        <div class="container col-md-12 mt-4">
            <div class="row d-flex">
                <label for="" class="col form-label fs-4">Telephone:</label>
                <input class="col-9" type="number" name="phone" placeholder="Telephone number" required>
            </div>
        </div>
        <div class="container col-md-12 mt-4">
            <div class="row d-flex">
                <label for="" class="col form-label fs-4">Nif:</label>
                <input class="col-9" type="number" name="nif" placeholder="Nif" required>
            </div>
        </div>
        <div class="col text-center mt-5">
            <button type="submit" class="btn btn-success">Send my Details</a>

        </div>
    </form>
    <div class="text-center mt-4">
        <a href="user_data.php" class="btn btn-danger">&#8592; Go to My Profile</a>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>