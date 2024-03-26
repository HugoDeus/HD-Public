<?php

include("connection.php");
include("html_pages.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_POST['update'])) {
    $book_id = $_POST['book_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $price = floatval($price);

    if (!empty($title) && !empty($author) && !empty($price)) {
        if ($_FILES['image']['name']) {
            $upload_dir = "upload/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $image_name = $_FILES['image']['name'];
            $image_temp = $_FILES['image']['tmp_name'];
            $image_path = $upload_dir . $image_name;

            $sql_select_image = "SELECT image FROM books WHERE id = $book_id";
            $result_image = $conn->query($sql_select_image);
            if ($result_image->num_rows > 0) {
                $row = $result_image->fetch_assoc();
                $previous_image = $row['image'];
                if (file_exists($previous_image)) {
                    unlink($previous_image);
                }
            }

            if (move_uploaded_file($image_temp, $image_path)) {
                $sql_update_book = "UPDATE books SET title = '$title', author = '$author', price = '$price', image = '$image_path' WHERE id = $book_id";

                if ($conn->query($sql_update_book) === TRUE) {
?>
                    <div class="container-fluid mt-5">
                        <div class="row text-center">
                            <h3 class="text-success">Book updated successfully</h3>
                        </div>
                        <div class="d-block text-center">
                            <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="container-fluid mt-5">
                        <div class="row text-center">
                            <h3 class="text-danger">Error updating book in the database.</h3>
                        </div>
                        <div class="d-block text-center">
                            <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
                        </div>
                    </div>
                <?php $conn->error;
                }
            } else {
                ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-success">Error uploading image.</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
                    </div>
                </div>
            <?php
            }
        } else {
            $sql_update_book = "UPDATE books SET title = '$title', author = '$author', price = '$price' WHERE id = $book_id";

            if ($conn->query($sql_update_book) === TRUE) {
            ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-success">Book updated successfully</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="container-fluid mt-5">
                    <div class="row text-center">
                        <h3 class="text-danger">Error updating book in database.</h3>
                    </div>
                    <div class="d-block text-center">
                        <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
                    </div>
                </div>
        <?php $conn->error;
            }
        }
    } else {
        ?>
        <div class="container-fluid mt-5">
            <div class="row text-center">
                <h3 class="text-danger">Title, Author and Price are required fields.</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="adminbooks.php">Go Back</a></button>
            </div>
        </div>
<?php
    }
}
?>