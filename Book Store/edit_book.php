<?php

include("connection.php");
include("html_pages.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    $sql_select_book = "SELECT * FROM books WHERE id = $book_id";
    $result_book = $conn->query($sql_select_book);
    if ($result_book->num_rows > 0) {
        $book_data = $result_book->fetch_assoc();
?>
        <div class="container mt-5 alert-secondary rounded-3">
            <h3 class="text-center text-success fw-bolder mt-3">Edit Book</h3>
            <form method="post" action="update_book.php" enctype="multipart/form-data">
                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                <div class="mt-3">
                    <label class="form-label" for="title">Book Title:</label>
                    <input class="form-control" type="text" name="title" id="title" value="<?php echo $book_data['title']; ?>">
                </div>
                <div class="mt-3">
                    <label class="form-label" for="author">Author:</label>
                    <input class="form-control" type="text" name="author" id="author" value="<?php echo $book_data['author']; ?>">
                </div>
                <div class="mt-3">
                    <label class="form-label" for="price">Price:</label>
                    <input class="form-control" type="text" name="price" id="price" pattern="^\d+(\.\d{1,2})?$" title="Enter a valid price (ex: 20.90)" value="<?php echo $book_data['price']; ?>">
                </div>
                <div class="text-center">
                    <div class="mt-3">
                        <label class="form-label" for="image">Image:</label>
                        <input class="form-control" type="file" name="image" id="image"><br>
                        <img class="img-fluid mb-3" src="<?php echo $book_data['image']; ?>" alt="Book Image" style="height: 100px;">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary mb-3">Update</button>
                </div>
            </form>
        </div>
        <div class="text-center mt-4">
            <a href="javascript:history.go(-1)" class="btn btn-danger">&#8592; Back</a>
        </div>
<?php
    } else {
        echo "<div class='container bg-dark my-auto p-2'>";
        echo "<h2 class='alert-danger text-center my-auto'>No Books Found.</h2>";
        echo "<p class='alert-danger text-center'>Go to admin books page in 5 seconds.</p>";
        header("refresh:5;url=books.php");
    }
} else {
    echo "<div class='container bg-dark my-auto p-2'>";
    echo "<h2 class='alert-danger text-center my-auto'>Invalid Request.</h2>";
    echo "<p class='alert-danger text-center'>Go to admin books page in 5 seconds.</p>";
    header("refresh:5;url=books.php");
}
?>