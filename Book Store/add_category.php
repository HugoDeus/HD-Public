<?php

include("connection.php");
include("html_pages.php");

session_start();
include('restricted.php');
checkPermissions();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addCategory'])) {
    $category = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['addCategory']))));

    if (!empty($category)) {
        $sql_category = "SELECT category FROM categories WHERE category='$category'";
        $check = $conn->query($sql_category);

        if ($check->num_rows >= 1) {
            ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-danger">This Subcategory already exist in database</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="admin.php">Go Back</a></button>
                    </div>
                </div>
<?php
        } else {
            $insert = "INSERT INTO categories (category) VALUE ('$category')";
            $send = $conn->query($insert);

            if ($send == true) {
    ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-success">Category added Successfully</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="admin.php">Go Back</a></button>
                    </div>
                </div>
<?php
            } else {
                ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-danger">ERROR = Category not added</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="admin.php">Go Back</a></button>
                    </div>
                </div>
<?php
            }
        }
    }
} else {
    echo "ERROR: " .$conn->error;
}

?>