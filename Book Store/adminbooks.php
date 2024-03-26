<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

$pagination = 10;

if (!isset($_GET['page']) || $_GET['page'] <= 0) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$sql_count_books = "SELECT COUNT(*) AS total FROM books";
$result_count = $conn->query($sql_count_books);
$total_results = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_results / $pagination);

$start = max(1, $page - 2);
$end = min($total_pages, $start + 4);

$offset = ($page - 1) * $pagination;

$sql_list_books = "SELECT * FROM books ORDER BY title LIMIT $offset, $pagination";
$result = $conn->query($sql_list_books);

if (isset($_POST['delete']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    $sql_select_image = "SELECT image FROM books WHERE id = $book_id";
    $result_image = $conn->query($sql_select_image);
    if ($result_image->num_rows > 0) {
        $row = $result_image->fetch_assoc();
        $image_path = $row['image'];

        $sql_delete_book = "DELETE FROM books WHERE id = $book_id";
        $query_delete = $conn->query($sql_delete_book);

        if ($query_delete) {
            if (file_exists($image_path)) {
                unlink($image_path);
                header("Location: adminbooks.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Books</title>
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

    <section>
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
                    <a class="nav-link activeadmin" href="adminbooks.php">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-admin" href="admin.php">Add Books</a>
                </li>
            </ul>
        </div>
        <!-- Menu de pesquisa -->
        <div class="container-fluid p-2 searchbar mb-3">
            <div class="container">
                <div class="row align-items-around">
                    <div class="container col-md-10 col-8">
                        <form class="form-inline" action="adminbooks.php" method="get">
                            <div class="d-flex">
                                <input class="form-control rounded-3 me-2 flex-grow-1" type="search" name="query" placeholder="Search Book or Author" aria-label="Search" required>
                                <button class="btn btn-outline-success text-nowrap" type="submit">&#128269; Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" style="max-width: 850px;">
            <?php
            // Verify if the search query is set
            if (isset($_GET['query'])) {
                $search_query = mysqli_real_escape_string($conn, $_GET['query']);
                $sql_query = "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
                $result_query = mysqli_query($conn, $sql_query);

                if ($result_query->num_rows > 0) {
            ?>
                    <div class="table-responsive">
                        <table class='table mt-3 table-hover table-striped bg-white'>
                            <tr class='bg-secondary text-white'>
                                <th class='col-auto'>Book</th>
                                <th class='col-auto'>Name Book</th>
                                <th class='col-auto'>Author</th>
                                <th class='col-auto'>Price</th>
                                <th class='col-auto'>Actions</th>
                            </tr>
                            <?php
                            while ($row = $result_query->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><img src=<?php echo $row['image'] ?> style='max-width: 40px;'></td>
                                    <td><?php echo $row['title'] ?></td>
                                    <td><?php echo $row['author'] ?></td>
                                    <td><?php echo $row['price'] ?></td>
                                    <td>
                                        <div class='d-flex justify-content-around text-center'>
                                            <form method='post' action='edit_book.php'>
                                                <input type='hidden' name='book_id' value="<?php echo $row['id']; ?>">
                                                <button class='me-2 btn btn-info' name='edit'>Edit</button>
                                            </form>
                                            <form method='post' action='' onsubmit="return confirmDelete()">
                                                <input type='hidden' name='book_id' value="<?php echo $row['id']; ?>">
                                                <button class='btn btn-danger' name='delete'>Delete</button>
                                            </form>
                                        </div>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>

                <?php
                }
            } else {
                if ($result->num_rows == 0) : ?>
                    <div>
                        <h2 class="text-center mt-3 text-danger bg-light">No Books Found</h2>
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class='table mt-3 table-hover table-striped bg-white'>
                            <tr class='bg-secondary text-white'>
                                <th class='col-auto'>Book</th>
                                <th class='col-auto'>Name Book</th>
                                <th class='col-auto'>Author</th>
                                <th class='col-auto'>Price</th>
                                <th class="col-auto">Actions</th>
                            </tr>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><img src="<?php echo $row['image']; ?>" style="max-width: 40px;"> </td>
                                    <td> <?php echo $row['title']; ?> </td>
                                    <td> <?php echo $row['author']; ?> </td>
                                    <td> <?php echo $row['price']; ?> </td>
                                    <td>
                                        <div class='d-flex justify-content-around text-center'>
                                            <form method='post' action='edit_book.php'>
                                                <input type='hidden' name='book_id' value="<?php echo $row['id']; ?>">
                                                <button class='me-2 btn btn-info' name='edit'>Edit</button>
                                            </form>
                                            <form method='post' action='' onsubmit="return confirmDelete()">
                                                <input type='hidden' name='book_id' value="<?php echo $row['id']; ?>">
                                                <button class='btn btn-danger' name='delete'>Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                <?php
                endif; ?>

                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item"><a class="page-link" href="adminbooks.php?page=1">First</a></li>
                        <li class="page-item"><a class="page-link" href="adminbooks.php?page=<?php echo ($page - 1); ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++) : ?>
                        <li class="page-item <?php echo ($i == $page ? 'active' : ''); ?>"><a class="page-link" href="adminbooks.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item"><a class="page-link" href="adminbooks.php?page=<?php echo ($page + 1); ?>">Next</a></li>
                        <li class="page-item"><a class="page-link" href="adminbooks.php?page=<?php echo $total_pages; ?>">Last</a></li>
                    <?php endif; ?>
                </ul>
            <?php
            }
            ?>
        </div>
        <div class="container">
            <p class="text-center mt-4"><a href="index.php"><button class="btn btn-danger">Exit -> Back to Homepage</button></a></p>
        </div>
    </section>

    <footer class="card-footert bg-dark text-white position-relative w-100 bottom-0">
        <div class="container-fluid">
            <div class="d-flex justify-content-between row">
                <div class="col-sm-6 col-12">
                    Made By <a href="https://github.com/HugoDeus?tab=repositories" target="_blank"><strong>@HugoDeus</strong></a>
                </div>
                <div class="col-sm-6 col-12 d-inline-block text-end">
                    <a href="https://github.com/HugoDeus?tab=repositories" target="_blank" class="btn-sm btn-primary bi bi-github col-xs-12 text-nowrap"> Github</a>
                    <a href="https://www.instagram.com/hugodsdeus/" target="_blank" class="btn-sm btn-primary bi bi-instagram d-inline-flex col-xs-12 text-nowrap"> Instagram</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this user?");
        }
    </script>

</body>

</html>