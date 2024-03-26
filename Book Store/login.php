<?php

include("connection.php");
include('html_pages.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $pass = trim(mysqli_real_escape_string($conn, $_POST['password']));

    if (!empty($user) && !empty($pass)) {
        $sql = "SELECT id, firstname, lastname, user, email, password, permissions FROM users WHERE user='$user' or email='$user'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $passverify = $row['password'];

            if (password_verify($pass, $passverify)) {
                session_start();
                $_SESSION['id'] = $row['id'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_name'] = $row['user'];
                $_SESSION['permissions'] = $row['permissions'];
                header("Location: index.php");
                exit();
            } else {
                echo "<div class='my-auto text-center'>";
                echo "<p class='text-center bg-dark text-danger'>Wrong Password</p>" . $conn->error;

                echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
                echo "</div>";
            }
        } else {
            echo "<div class='my-auto text-center'>";
            echo "<p class='text-center bg-dark text-danger'>User or email doesn`t exist... </p>" . $conn->error;

            echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            echo "</div>";
        }
    } else {
        echo "<div class='my-auto text-center'>";
        echo "<p class='text-center bg-dark text-danger'>Please fill all the fiels of the form</p>" . $conn->error;

        echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
        echo "</div>";
    }
}
