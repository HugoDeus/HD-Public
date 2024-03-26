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

$sql_count_messages = "SELECT COUNT(*) AS total FROM messages";
$result_count = $conn->query($sql_count_messages);
$total_results = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_results / $pagination);

$start = max(1, $page - 2);
$end = min($total_pages, $start + 4);

$offset = ($page - 1) * $pagination;

$sql_list_messages = "SELECT id, name, email, read_status, date FROM messages ORDER BY date DESC LIMIT $offset, $pagination";
$result = $conn->query($sql_list_messages);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
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
                <a class="nav-link nav-admin" href="users.php">Users</a>
            </li>
            <li class="nav-item nav-admin">
                <a class="nav-link nav-admin" href="orders.php">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link activeadmin" href="message.php">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="adminbooks.php">Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-admin" href="admin.php">Add Books</a>
            </li>
        </ul>
    </div>

    <div class="container">
        <?php if ($result->num_rows == 0) : ?>
            <div>
                <h2 class="text-center mt-3 text-danger bg-light">No Messages Received</h2>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class='table mt-3 table-hover table-striped bg-white'>
                    <tr class='bg-secondary text-white'>
                        <th class='col-auto'>Name</th>
                        <th class='col-auto'>Email</th>
                        <th class='col-auto'>Date</th>
                        <th class='col-auto'>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr <?php echo $row['read_status'] ? "class='table-success'" : "class='text-danger'"; ?>>
                            <td> <?php echo $row['name']; ?> </td>
                            <td> <?php echo $row['email']; ?> </td>
                            <td> <?php echo $row['date']; ?> </td>
                            <td>
                                <a href="view_message.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1) : ?>
                <li class="page-item"><a class="page-link" href="message.php?page=1">First</a></li>
                <li class="page-item"><a class="page-link" href="message.php?page=<?php echo ($page - 1); ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++) : ?>
                <li class="page-item <?php echo ($i == $page ? 'active' : ''); ?>"><a class="page-link" href="message.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>

            <?php if ($page < $total_pages) : ?>
                <li class="page-item"><a class="page-link" href="message.php?page=<?php echo ($page + 1); ?>">Next</a></li>
                <li class="page-item"><a class="page-link" href="message.php?page=<?php echo $total_pages; ?>">Last</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="container">
        <p class="text-center mt-4"><a href="index.php"><button class="btn btn-danger">Exit -> Back to Homepage</button></a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>