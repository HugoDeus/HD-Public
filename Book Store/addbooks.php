<?php
include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];

    if ($category == "Choose...") {
        header("Location: admin.php");
    } else {
        $sql_category_name = "SELECT category FROM categories WHERE id='$category'";
        $result_category_name = $conn->query($sql_category_name);

        if ($result_category_name->num_rows > 0) {
            $row_category = $result_category_name->fetch_assoc();
            $category_name = $row_category['category'];
        }
?>
        <div class="container-fluid">
            <div class="text-center my-4">
                <h2 class="fw-bolder">Admin Page</h2>
            </div>
        </div>

        <div class="container mb-2 bg-secondary">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link nav-admin" aria-current="page" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-admin" href="message.php">Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-admin" href="adminbooks.php">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-admin" href="admin.php">Add Books</a>
                </li>
            </ul>
        </div>
        <!-- Form add books to database -->
        <div class="container bg-light mt-5">
            <h3 class="text-center mt-3 fw-bolder">Add Books</h3>
            <form action="process_book.php" method="post" name="addbooks" id="bookform" enctype="multipart/form-data">
                <div>
                    <label class="form-label" for="nomelivro">Book name:</label>
                    <input class="form-control" type="text" name="namebook" required>
                </div>
                <div>
                    <label class="form-label" for="author">Author:</label>
                    <input class="form-control" type="text" name="author" required>
                </div>
                <div>
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" type="text" name="description" maxlength="5000" style="resize: none;" required></textarea>
                </div>
                <div>
                    <label class="form-label" for="price">Price</label>
                    <input class="form-control" type="text" name="price" pattern="^\d+(\.\d{1,2})?$" title="Enter a valid price (ex: 20.90)" required>
                </div>
                <div class="input-group mb-3 mt-4">
                    <input type="file" class="form-control" name="imagebook" required>
                    <label class="input-group-text" for="imagebook">Upload Image</label>
                </div>
                <div class="text-center">
                    <label class="form-label text-success fw-bolder" for="description">Selected Category</label>
                    <p class="form-control alert-secondary fw-bolder" name="selcategory"><?php echo "$category_name"; ?></p>
                    <input type="hidden" name="category_name" value="<?php echo $category_name; ?>">
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="subcategory">Subcategory</label>
                    <select class="form-select container" name="subcategory" id="category" required>
                        <option selected>Choose...</option>
                        <?php
                        $sql_subcategory = "SELECT * FROM subcategories WHERE category_id='$category'";
                        $result_subcategory = $conn->query($sql_subcategory);

                        if ($result_subcategory->num_rows > 0) {
                            while ($row = $result_subcategory->fetch_assoc()) {
                                echo '<option value="' . $row['id'] . '">' . $row['subcategory'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="row">
                    <button type="submit" name="addbooks" class="btn btn-primary mb-2 col" onclick="sendbook()">Send Book</button>
                </div>
            </form>
        </div>
        <div class="container">
            <p class="text-center mt-4"><a href="admin.php"><button class="btn btn-danger">Exit -> Go Back</button></a></p>

        </div>
<?php
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function sendbook() {
            // Obter o valor da categoria selecionada
            let categoryId = document.querySelector('option[name="category_name"]').value;
            // Enviar o formulário
            document.forms[0].submit();
        }
    </script>

</body>

</html>