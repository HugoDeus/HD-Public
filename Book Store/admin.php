<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();


if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo "<div id='error_message' class='alert alert-danger text-center' role='alert'>There are associated books with this subcategory. You cannot delete it.</div>";
}
// Vefify if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove'])) {
    $category_id = $_POST['category_id'];

    $sql_check_subcategories = "SELECT COUNT(*) AS count FROM subcategories WHERE category_id = $category_id";
    $result_check_subcategories = $conn->query($sql_check_subcategories);
    $row_check_subcategories = $result_check_subcategories->fetch_assoc();
    $count_subcategories = $row_check_subcategories['count'];

    if ($count_subcategories == 0) {
        $sql_remove_category = "DELETE FROM categories WHERE id = $category_id";
        if ($conn->query($sql_remove_category) === TRUE) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error removing category: " . $conn->error;
        }
    } else {
        echo "<div id='error_message' class='alert alert-danger text-center' role='alert'>There are associated subcategories with this category. You cannot delete it.</div>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_subcategory'])) {
    $subcategory_id = $_POST['subcategory_id'];

    $sql_check_books = "SELECT COUNT(*) AS count FROM books WHERE subcategory_id = $subcategory_id";
    $result_check_books = $conn->query($sql_check_books);
    $row_check_books = $result_check_books->fetch_assoc();
    $count_books = $row_check_books['count'];

    if ($count_books > 0) {
        header("Location: admin.php?error=1");
        exit();
    } else {
        $sql_delete_books = "DELETE FROM books WHERE subcategory_id = $subcategory_id";
        if ($conn->query($sql_delete_books) === TRUE) {

            $sql_remove_subcategory = "DELETE FROM subcategories WHERE id = $subcategory_id";
            if ($conn->query($sql_remove_subcategory) === TRUE) {
                header("Location: admin.php");
                exit();
            } else {
                echo "Error removing subcategory: " . $conn->error;
            }
        } else {
            echo "Error removing associated books: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
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
        </div>
    </nav>

    <div class="container-fluid">
        <div class="text-center my-4">
            <h2 class="fw-bolder text-success">Admin Page</h2>
        </div>
    </div>

    <div class="container mb-2 bg-secondary">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link nav-admin" aria-current="page" href="users.php">Users</a>
            </li>
            <li class="nav-item nav-admin">
                <a class="nav-link nav-admin" href="orders.php">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="message.php">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="adminbooks.php">Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link activeadmin" href="admin.php">Add Books</a>
            </li>
        </ul>
    </div>

    <div class="container-fluid text-center mt-4">
        <h3 class="fw-bolder">Select Category To Add Book</h3>
    </div>

    <div class="container bg-light p-2">
        <form action="addbooks.php" method="post">
            <div class="input-group mb-3">
                <label class="input-group-text" for="category">Category</label>
                <select class="form-select" name="category" required>
                    <option selected>Choose...</option>
                    <?php
                    $sql_category = "SELECT id, category FROM categories";
                    $result_category = $conn->query($sql_category);

                    if ($result_category->num_rows > 0) {
                        while ($row = $result_category->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['category'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="row text-center">
                <div class="col-12">
                    <button type="button" onclick="submitbook()" class="btn btn-primary">Next</button>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <div class="container d-flex justify-content-around">
        <div>
            <h3 class="fw-bolder">Add Category</h3>
            <form action="add_category.php" method="post">
                <div class="mb-3">
                    <label for="addCategory" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="category_name" name="addCategory" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
        <div>
            <h3 class="fw-bolder">Add Subcategory</h3>
            <form action="add_subcategory.php" method="post">
                <div class="mb-3">
                    <label for="subcategory_name" class="form-label">Subcategory Name</label>
                    <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" required>
                </div>
                <div class="mb-3">
                    <label for="category_select" class="form-label">Category</label>
                    <select class="form-select" id="category_select" name="category_id" required>
                        <option value="" selected>Choose category</option>
                        <?php
                        $sql_categories = "SELECT id, category FROM categories";
                        $result_categories = $conn->query($sql_categories);
                        if ($result_categories->num_rows > 0) {
                            while ($row = $result_categories->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Subcategory</button>
            </form>
        </div>
    </div>

    <hr>

    <div>
        <?php
        $sql_categories = "SELECT id, category FROM categories";
        $result_categories = $conn->query($sql_categories);

        if ($result_categories->num_rows > 0) {
            while ($category_row = $result_categories->fetch_assoc()) {
                $category_id = $category_row['id'];
                $category_name = $category_row['category'];

                $sql_subcategories = "SELECT id, subcategory FROM subcategories WHERE category_id = $category_id";
                $result_subcategories = $conn->query($sql_subcategories);
        ?>
                <div class="container">
                    <table class="table table-light table-column">
                        <thead>
                            <tr>
                                <th class="d-flex justify-content-between bg-dark text-white"><?php echo $category_name ?><div>
                                        <div class="d-flex justify-content-around">
                                            <form action="edit_category.php" method="post">
                                                <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                            </form>
                                            <form action="admin.php" method="post">
                                                <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                                                <button type="submit" name="remove" class="btn btn-danger btn-sm ms-1">Delete</button>
                                            </form>
                                        </div>

                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_subcategories->num_rows > 0) {
                                while ($subcategory_row = $result_subcategories->fetch_assoc()) {
                                    $subcategory_id = $subcategory_row['id'];
                                    $subcategory_name = $subcategory_row['subcategory'];
                            ?>
                                    <tr>
                                        <td class="d-flex justify-content-between">
                                            <?php echo $subcategory_name; ?>
                                            <div class="d-flex justify-content-around">
                                                <form action="edit_subcategory.php" method="post">
                                                    <input type="hidden" name="subcategory_id" value="<?php echo $subcategory_id; ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                                </form>
                                                <form action="admin.php" method="post">
                                                    <input type="hidden" name="subcategory_id" value="<?php echo $subcategory_id; ?>">
                                                    <button type="submit" name="remove_subcategory" class="btn btn-danger btn-sm ms-1">Delete</button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td class='text-center fw-bolder text-danger'>No subcategories found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
        <?php
            }
        } else {
            echo "<p class='text-center fw-bolder text-danger'>No categories found.</p>";
        }
        ?>
    </div>
    <div class="container">
        <p class="text-center mt-4"><a href="index.php"><button class="btn btn-danger">Exit -> Back to Homepage</button></a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function submitbook() {
            let categoryId = document.querySelector('select[name="category"]').value;
            // Send first form found
            document.forms[0].submit();
        }

        setTimeout(function() {
            var errorMessage = document.getElementById('error_message');
            if (errorMessage) {
                errorMessage.remove();
            }
        }, 5000);
    </script>

</body>

</html>