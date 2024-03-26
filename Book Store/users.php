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

$offset = ($page - 1) * $pagination;

$sql_count_users = "SELECT COUNT(*) AS total FROM users";
$result_count = $conn->query($sql_count_users);
$total_results = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_results / $pagination);

$selected_order = isset($_SESSION['selected_order']) ? $_SESSION['selected_order'] : 'id_asc';
$order_options = array(
    "id_asc" => "id ASC",
    "id_desc" => "id DESC",
    "fname_asc" => "firstname ASC",
    "fname_desc" => "firstname DESC",
    "lname_asc" => "lastname ASC",
    "lname_desc" => "lastname DESC",
    "email_asc" => "email ASC",
    "email_desc" => "email DESC",
);
if (isset($_POST['order']) && isset($order_options[$_POST['order']])) {
    $selected_order = $_POST['order'];
}
$_SESSION['selected_order'] = $selected_order;
$order_select = array(
    "id_asc" => "ID (Low-High)",
    "id_desc" => "ID (High-Low)",
    "fname_asc" => "First Name (A-Z)",
    "fname_desc" => "First Name (Z-A)",
    "lname_asc" => "Last Name (A-Z)",
    "lname_desc" => "Last Name (Z-A)",
    "email_asc" => "Email (A-Z)",
    "email_desc" => "Email (Z-A)",
);
$_SESSION['order_select'] = $order_select;

$sql_list_users = "SELECT id, firstname, lastname, email, user FROM users ORDER BY " . $order_options[$selected_order] . " LIMIT $offset, $pagination";
$result = $conn->query($sql_list_users);

if (isset($_POST['delete']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $sql_delete_user = "DELETE FROM users WHERE id= $user_id";
    $query_delete = $conn->query($sql_delete_user);
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

    header("Location: users.php?page=$current_page");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
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
            <h2 class="text-success fw-bolder">Admin Page</h2>
        </div>
    </div>

    <div class="container mb-2 bg-secondary">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link activeadmin" href="users.php">Users</a>
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
                <a class="nav-link nav-admin" href="admin.php">Add Books</a>
            </li>
        </ul>
    </div>

    <div class="container bg-light text-center mx-auto">
        <?php
        if ($result->num_rows == 0) {
            echo "<h3><strong>No registered users...</strong></h3>";
        } else {
        ?>
            <div class="text-center">
                <form action="users.php" method="post">
                    <label for="form-label" class="form-label">Choose...</label>
                    <select class="select-label" name="order">
                        <?php
                        foreach ($order_select as $key => $value) {
                            $selected = ($key == $_SESSION['selected_order']) ? 'selected' : '';
                            echo "<option value='$key' $selected>$value</option>";
                        }
                        ?>
                    </select>
                    <button class="btn btn-success btn-sm ms-2 col" type="submit">Confirm</button>
                </form>
            </div>
        <?php
            echo "<div class='table-responsive'>";
            echo "<table class='table mt-3 table-striped table-hover mx-auto'>";
            echo "<tr>";
            echo "<th class='text-nowrap'>Fisrt Name</th>";
            echo "<th class='text-nowrap'>Last Name</th>";
            echo "<th>Email</th>";
            echo "<th>Action</th>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['firstname'] . "</td>";
                echo "<td>" . $row['lastname'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td class='container d-flex justify-content-center'>
                        <form method='post' action='' onsubmit='return confirmDelete()'>
                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                            <button class='btn-sm btn-danger' name='delete'>Delete</button>
                        </form>
                        <a href='view_profile.php?id=" . $row['id'] . "' class='btn-sm btn-info ms-1'><i class='bi bi-eye'></i></a>
                        </td>";
                echo "</tr>";
            }
            echo "</table>";
            echo '</div>';

            echo "<ul class='pagination justify-content-center'>";
            if ($page > 1) {
                $selected_order = isset($_POST['order']) ? $_POST['order'] : 'id_asc';
                echo "<li class='page-item'><a class='page-link' href='users.php?page=1&order=" . urlencode($selected_order) . "'>First</a></li>";
                echo "<li class='page-item'><a class='page-link' href='users.php?page=" . ($page - 1) . "&order=" . urlencode($selected_order) . "'>Previous</a></li>";
            }
            $start = max(1, $page - 2);
            $end = min($total_pages, $start + 4);

            for ($i = $start; $i <= $end; $i++) {
                $selected_order = isset($_POST['order']) ? $_POST['order'] : (isset($_SESSION['selected_order']) ? $_SESSION['selected_order'] : 'id_asc');
                echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='users.php?page=$i&order=" . urlencode($selected_order) . "'>$i</a></li>";
            }
            if ($page < $total_pages) {
                echo "<li class='page-item'><a class='page-link' href='users.php?page=" . ($page + 1) . "&order=" . urlencode($selected_order) . "'>Next</a></li>";
                echo "<li class='page-item'><a class='page-link' href='users.php?page=$total_pages&order=" . urlencode($selected_order) . "'>Last</a></li>";
            }
            echo "</ul>";
        }
        ?>
    </div>

    <div class="container">
        <p class="text-center mt-4"><a href="index.php"><button class="btn btn-danger">Exit -> Back to Homepage</button></a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this user?");
        }
    </script>

</body>

</html>