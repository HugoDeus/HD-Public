<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $sql_get_category = "SELECT id, category FROM categories WHERE id = $category_id";
    $result_get_category = $conn->query($sql_get_category);
    if ($result_get_category->num_rows > 0) {
        $row_get_category = $result_get_category->fetch_assoc();
        $category_name = $row_get_category['category'];
    }
} else {
    echo "<p class='text-center bg-dark text-danger my-auto fw-bolder fs-2'>No category ID provided</p>";
    header("refresh:3; url=admin.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['category_name']) && isset($_POST['category_id'])) {
        $category_name = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['category_name']))));
        $category_id = $_POST['category_id'];

        $sql_book_categoryID = "SELECT category_id FROM books WHERE category_id = $category_id";
        $result_book_categoryID = $conn->query($sql_book_categoryID);

        if ($result_book_categoryID->num_rows > 0) {
            $row_book_categoryID = $result_book_categoryID->fetch_assoc();
            $categoryId_book = $row_book_categoryID['category_id'];
        }

        $sql_update_category = "UPDATE categories SET category = '$category_name' WHERE id = $category_id";
        if ($conn->query($sql_update_category) === TRUE) {
            $sql_update_books_category = "UPDATE books SET category = '$category_name' WHERE category_id = $category_id";
            if ($conn->query($sql_update_books_category) === TRUE) {
                header("Location: admin.php");
                exit();
            } else {
                echo "<p class='text-center bg-dark text-danger'>Error updating books category: </p>" . $conn->error;

                echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            }
        } else {
            echo "<p class='text-center bg-dark text-danger my-auto'>Error updating category: </p>" . $conn->error;
            header("refresh:3; url=admin.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php
    if (isset($_POST['category_id'])) {
    ?>
        <div class="my-auto">
            <div class="container mt-4 alert-success rounded-3">
                <h3 class="text-success text-center">Edit Category</h3>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" value="<?php echo isset($category_name) ? $category_name : ''; ?>">
                    </div>
                    <div class="text-center">
                        <input type="hidden" name="category_id" value="<?php echo isset($category_id) ? $category_id : ''; ?>">
                        <button type="submit" class="btn btn-primary mb-2">Save Changes</button>
                    </div>
                </form>
            </div>
            <div class="text-center mt-3">
        <a href="javascript:history.go(-1)" class="btn btn-danger">&#8592; Back</a>
    </div>
        </div>
    <?php
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>