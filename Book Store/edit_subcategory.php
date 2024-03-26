<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_POST['subcategory_id'])) {
    $subcategory_id = $_POST['subcategory_id'];
    $sql_get_subcategory = "SELECT id, subcategory FROM subcategories WHERE id = $subcategory_id";
    $result_get_subcategory = $conn->query($sql_get_subcategory);

    if ($result_get_subcategory->num_rows > 0) {
        $row_get_subcategory = $result_get_subcategory->fetch_assoc();
        $subcategory_name = $row_get_subcategory['subcategory'];
    }
} else {
    echo "<p class='text-center bg-dark text-danger my-auto fw-bolder fs-2'>No subcategory ID provided</p>";
    header("refresh:3; url=admin.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subcategory_name'])) {
        $subcategory_name = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['subcategory_name']))));
        $subcategory_id = $_POST['subcategory_id'];

        $sql_get_subcategoryID = "SELECT id, subcategory FROM books WHERE id = $subcategory_id";
        $result_book_subcategoryID = $conn->query($sql_get_subcategoryID);
        
        if ($result_book_subcategoryID->num_rows > 0) {
            $row_get_subcategoryID = $result_book_subcategoryID->fetch_assoc();
            $subcategoryId_book = $row_book_subcategoryID['subcategory'];
        } 
        $sql_update_subcategory = "UPDATE subcategories SET subcategory = '$subcategory_name' WHERE id = $subcategory_id";
        if ($conn->query($sql_update_subcategory) === TRUE) {
            $sql_update_books_subcategory = "UPDATE books SET subcategory = '$subcategory_name' WHERE subcategory_id = $subcategory_id";
            if ($conn->query($sql_update_books_subcategory) === TRUE) {
                header("Location: admin.php");
                exit();
            } else {
                echo "<p class='text-center bg-dark text-danger'>Error updating books subcategory: </p>" . $conn->error;

                echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            }
        } else {
            echo "<p class='text-center bg-dark text-danger my-auto'>Error updating subcategory: </p>" . $conn->error;
            header("refresh:3; url=admin.php");
        }
    }
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
<?php
    if (isset($_POST['subcategory_id'])) {
    ?>
        <div class="my-auto">
            <div class="container mt-4 alert-success rounded-3">
        <h3 class="text-success text-center">Edit Subcategory</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label for="subcategory_name" class="form-label">Subcategory Name</label>
                <input type="text" name="subcategory_name" class="form-control" value="<?php echo isset($subcategory_name) ? $subcategory_name : ''; ?>">
            </div>
            <div class="text-center">
                <input type="hidden" name="subcategory_id" value="<?php echo isset($subcategory_id) ? $subcategory_id : ''; ?>">
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
</body>

</html>