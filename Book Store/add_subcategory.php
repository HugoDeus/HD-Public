<?php

include("connection.php");
include("html_pages.php");

session_start();
include('restricted.php');
checkPermissions();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subcategory_name']) && isset($_POST['category_id'])) {
    $categoryID = $_POST['category_id'];
    $subcategoryName = ucfirst(strtolower(trim(mysqli_escape_string($conn, $_POST['subcategory_name']))));

    $sql_subcategories = "SELECT * FROM subcategories WHERE subcategory='$subcategoryName' AND category_id='$categoryID'";
    $result = $conn->query($sql_subcategories);

    if ($result->num_rows >= 1) {
?>
        <div class="container-fluid mt-5">
            <div class="row text-center">
                <h3 class="text-danger">ERROR = Subcategory is already exist</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="admin.php">Go Back</a></button>
            </div>
        </div>
        <?php
    } else {
        $insert = "INSERT INTO subcategories (subcategory, category_id) VALUES ('$subcategoryName', '$categoryID')";
        $send = $conn->query($insert);

        if ($send == true) {
        ?>
            <div class="container-fluid mt-5">
                <div class="row text-center">
                    <h3 class="text-success">SubCategory added Successfully</h3>
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
                    <h3 class="text-danger">ERROR = SubCategory not added</h3>
                </div>
                <div class="d-block text-center">
                    <button type="button" class="btn btn-link"><a href="admin.php">Go Back</a></button>
                </div>
            </div>
<?php
        }
    }
} else {
    echo "ERROR: " .$conn->error;
}
?>