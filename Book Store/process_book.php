<?php
include("connection.php");
include("html_pages.php");

session_start();
include('restricted.php');
checkPermissions();

function alertMessage($message)
{
    echo "<div class='container-fluid mt-5'>
        <div class='row justify-content-center mt-5'>
            <div class='col-12'>
                <div id='error_message' class='alert alert-danger text-center' role='alert'><h2>$message</h2></div>
                <div class='text-center'><a class='btn btn-primary' href='admin.php'>Go Back</a></div>
            </div>
        </div>
      </div>";
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
    alertMessage($message);
}

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['subcategory']) && $_POST['subcategory'] != 'Choose...' && !empty($_POST['subcategory'])) {
    $namebook = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['namebook']))));
    $author = ucfirst(strtolower(trim(mysqli_real_escape_string($conn, $_POST['author']))));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $price = trim(mysqli_real_escape_string($conn, $_POST['price']));

    $price = floatval($price);

    $category_name = $_POST['category_name'];
    $subcategory_idpost = $_POST['subcategory'];

    $sql_category = "SELECT id FROM categories WHERE category = '$category_name'";
    $result_category = $conn->query($sql_category);
    if ($row_category = $result_category->fetch_assoc()) {
        $category_id = $row_category['id'];
    } else {
        alertMessage('erros nas categorias');
    }

    $sql_subcategory = "SELECT * FROM subcategories WHERE id = '$subcategory_idpost' AND category_id = '$category_id'";
    $result_subcategory = $conn->query($sql_subcategory);
    $row_subcategory = $result_subcategory->fetch_assoc();
    $subcategory_id = $row_subcategory['id'];
    $subcategory_name = $row_subcategory['subcategory'];


    if ($category_name && $subcategory_name && $category_id && $subcategory_id) {
        if (isset($_FILES['imagebook'])) {
            $file = $_FILES['imagebook'];

            if ($file['error'] == UPLOAD_ERR_NO_FILE) {
                alertMessage('Please Select Image');
            } elseif ($file['error']) {
                alertMessage('Error uploading file! Error: ' . $file['error']);
            } elseif ($file['size'] > 2097152) {
                alertMessage('ERROR uploading file. File too large. Please select a file up to 2MB.');
            } else {
                $path = "upload/";
                $file_name = $file['name'];
                $new_file_name = uniqid();
                $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (!in_array($extension, array('jpg', 'png', 'jpeg'))) {
                    alertMessage('Invalid File Type');
                }

                $new_path = $path . $new_file_name . '.' . $extension;
                $movefile = move_uploaded_file($file['tmp_name'], $path . $new_file_name . '.' . $extension);

                if (!$movefile) {
                    alertMessage('Failed to move file to upload folder');
                } else {
                    $sql_add_book = "INSERT INTO books (title, author, description, price, image, category, subcategory, entry_date, category_id, subcategory_id) VALUES ('$namebook', '$author', '$description', '$price', '$new_path', '$category_name','$subcategory_name', NOW(), '$category_id', '$subcategory_id')";
                    $result_add = $conn->query($sql_add_book);

                    if ($result_add) {
?>
                        <div class="container-fluid mt-5 border">
                            <div class="row text-center">
                                <h3 class="text-success">The book has been added successfully</h3>
                            </div>
                            <div class="d-block text-center">
                                <button type="button" class="btn btn-link"><a href="admin.php">Go to Admin page</a></button>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="container-fluid mt-5 border">
                            <div class="row text-center">
                                <h3 class="text-danger">[ERRO] Book not added</h3>
                            </div>
                            <div class="d-block text-center">
                                <button type="button" class="btn btn-link"><a href="admin.php">Go to Admin page</a></button>
                            </div>
                        </div>
        <?php
                        echo mysqli_error($conn);
                    }
                }
            }
        } else {
            echo "<div class='my-auto text-center'>";
            echo "<p class='text-center bg-dark text-danger'>Failled to upload image</p>" . $conn->error;

            echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            echo "</div>";
        }
    } else {
        ?>
        <div class="container-fluid mt-5 border">
            <div class="row text-center">
                <h3 class="text-danger">Please select Subcategory</h3>
            </div>
            <div class="d-block text-center">
                <button type="button" class="btn btn-link"><a href="admin.php">Go to Admin page</a></button>
            </div>
        </div>
<?php
    }
}
?>