<?php
include('connection.php');
include('html_pages.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST["nome1"]))));
    $lastname = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST["nome2"]))));
    $username = trim(mysqli_real_escape_string($conn, $_POST["username"]));
    $email = trim(mysqli_real_escape_string($conn, $_POST["email"]));
    $password = trim(mysqli_real_escape_string($conn, $_POST["password"]));

    if (!empty($name) && !empty($lastname) && !empty($username) && !empty($email) && !empty($password)) {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='my-auto text-center'>";
            echo "<p class='text-center bg-dark text-danger'>This email is not valid</p>" . $conn->error;

            echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            echo "</div>";
        } else {

            $emailcheck = "SELECT email FROM users WHERE email='$email'";
            $check = mysqli_query($conn, $emailcheck);

            if (mysqli_num_rows($check) > 0) {
                echo "<div class='my-auto text-center'>";
                echo "<p class='text-center bg-dark text-danger'>This email is already in use</p>" . $conn->error;

                echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
                echo "</div>";
            } else {

                $usercheck = "SELECT user FROM users WHERE user='$username'";
                $checku = mysqli_query($conn, $usercheck);

                if (mysqli_num_rows($checku) > 0) {
                    echo "<div class='my-auto text-center'>";
                    echo "<p class='text-center bg-dark text-danger'>This email is already in use</p>" . $conn->error;
        
                    echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
                    echo "</div>";
                } else {
                    $passhash = password_hash($password, PASSWORD_DEFAULT);

                    $insert = "INSERT INTO users (firstname, lastname, user, email, password,permissions, registered_date) VALUES ('$name', '$lastname', '$username', '$email', '$passhash', 'User', NOW())";
                    $send = mysqli_query($conn, $insert);

                    echo "<div class='my-auto text-center'>";
                    echo "<p class='text-center alert-secondary text-success'>Register Successfully</p>" . $conn->error;
                    echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
                    echo "</div>";
                    header("refresh:3;url=login.php");
                    exit();
                }
            }
        }
    } else {
        echo "<div class='my-auto text-center'>";
        echo "<p class='text-center bg-dark text-danger'>Please fill all the fiels of the form</p>" . $conn->error;

        echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
        echo "</div>";
    }
}
