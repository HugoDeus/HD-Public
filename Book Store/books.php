<?php
include('connection.php');
include("update_cart.php");

session_start();

function getUserName()
{
    if (isset($_SESSION['user_name'])) {
        return $_SESSION['user_name'];
    } elseif (isset($_SESSION['permissions'])) {
        switch ($_SESSION['permissions']) {
            case 'Admin':
                return 'Admin';
            case 'User':
                return 'User';
            default:
                return 'Guest';
        }
    } else {
        return 'Guest';
    }
}
$userName = getUserName();

$pagination = 10;

if (!isset($_GET['page']) || $_GET['page'] <= 0) {
    $page = 1;
} else {
    $page = $_GET['page'];
}
$total_results = 0;

if (isset($_GET['subcategory'])) {
    $subcategory = mysqli_real_escape_string($conn, $_GET['subcategory']);

    $sql_subcategory_count = "SELECT COUNT(*) AS total FROM books WHERE subcategory = '$subcategory'";
    $result_subcategory_count = $conn->query($sql_subcategory_count);
    $total_results = $result_subcategory_count->fetch_assoc()['total'];
} elseif (isset($_GET['query'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['query']);
    $sql_search_count = "SELECT COUNT(*) AS total FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
    $result_search_count = $conn->query($sql_search_count);
    $total_results = $result_search_count->fetch_assoc()['total'];
} else {
    $sql_count_books = "SELECT COUNT(*) AS total FROM books";
    $result_count = $conn->query($sql_count_books);
    $total_results = $result_count->fetch_assoc()['total'];
}

$total_pages = ceil($total_results / $pagination);

$start = max(1, $page - 2);
$end = min($total_pages, $start + 4);

$sql_list_books = "SELECT * FROM books";

if (isset($_GET['order'])) {
    switch ($_GET['order']) {
        case 'title_asc':
            $sql_list_books .= " ORDER BY title ASC";
            break;
        case 'title_desc':
            $sql_list_books .= " ORDER BY title DESC";
            break;
        case 'author_asc':
            $sql_list_books .= " ORDER BY author ASC";
            break;
        case 'author_desc':
            $sql_list_books .= " ORDER BY author DESC";
            break;
        case 'price_asc':
            $sql_list_books .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $sql_list_books .= " ORDER BY price DESC";
            break;
        default:
            break;
    }
}

$offset = ($page - 1) * $pagination;
$sql_list_books .= " LIMIT $offset, $pagination";

$result = $conn->query($sql_list_books);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    addToCart($productName, $productPrice);

    header("Location: books.php?" . http_build_query($_POST));
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_from_cart'])) {
    removeFromCart($_POST['product_name']);

    header("Location: books.php?" . http_build_query($_POST));
    exit();
}

$cartInfo = calculateCart();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
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
                        <a class="nav-link text-white active" href="books.php">Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="about.php">About Us</a>
                    </li>
                </ul>
            </div>
            <div class="welcome">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header>
        <!-- SeacrhBar -->
        <div class="container-fluid p-2 searchbar">
            <div class="container">
                <div class="row align-items-around">
                    <div class="col-md-2 col-4 d-none d-sm-block text-center">
                        <img src="images/logo.png" alt="logo" class="img-fluid" style="max-height: 50px; max-width: 50px;">
                    </div>
                    <div class="container col-md-10 col-8">
                        <form class="form-inline" action="books.php" method="get">
                            <div class="d-flex">
                                <input class="form-control rounded-3 me-2 flex-grow-1" type="search" name="query" placeholder="Search Book or Author" aria-label="Search" required>
                                <button class="btn btn-outline-success text-nowrap" type="submit">&#128269; Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome  -->
        <div class="container-fluid mx-0 row justify-content-between align-items-center bgspace">
            <div class="col-sm-6 col-4 text-start welcome">
                <span class="text-user my-1">Welcome, <?php echo $userName ?? 'Guest'; ?></span>
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="btn btn-sm btn-success text-nowrap my-1" href="user_data.php?id=<?php echo $id; ?>">My Profile</a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 col-4 text-end">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <?php if ($_SESSION['permissions'] === 'Admin') : ?>
                        <a href="message.php" class="btn btn-sm btn-success my-1">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm btn-danger text-nowrap my-1">Log out</a>
                <?php else : ?>
                    <a href="login.php" class="btn btn-info btn-sm my-1" data-bs-toggle="modal" data-bs-target="#loginmodal" type="button" role="tab" aria-controls="loginmodal" aria-selected="false">Login/Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="container d-flex justify-content-center">
            <form action="books.php" method="GET">
                <div class="row d-flex justify-content-center align-items-center mt-3">
                    <label class="form-label col text-nowrap text-secondary fs-5 fw-bolder" for="order">Order by:</label>
                    <select class="form-select col w-auto" aria-label="Small select example" name="order" id="order">
                        <option class="form-option" value="title_asc" <?php echo ($_GET['order'] ?? '') === 'title_asc' ? 'selected' : ''; ?>>Title (A-Z)</option>
                        <option value="title_desc" <?php echo ($_GET['order'] ?? '') === 'title_desc' ? 'selected' : ''; ?>>Title (Z-A)</option>
                        <option value="author_asc" <?php echo ($_GET['order'] ?? '') === 'author_asc' ? 'selected' : ''; ?>>Author (A-Z)</option>
                        <option value="author_desc" <?php echo ($_GET['order'] ?? '') === 'author_desc' ? 'selected' : ''; ?>>Author (Z-A)</option>
                        <option value="price_asc" <?php echo ($_GET['order'] ?? '') === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="price_desc" <?php echo ($_GET['order'] ?? '') === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                    </select>
                    <button class="btn-sm btn-success ms-2 col" type="submit">Confirm</button>
                </div>
            </form>
        </div>
        <div class="container" style="max-width: 850px;">
            <?php
            if (isset($_GET['subcategory'])) {
                $subcategory = mysqli_real_escape_string($conn, $_GET['subcategory']);

                $sql_query_index = "SELECT *
                FROM books
                WHERE subcategory = '$subcategory'";

                $offset = ($page - 1) * $pagination;
                $sql_query_index .= " LIMIT $offset, $pagination";

                $result_queryp = mysqli_query($conn, $sql_query_index);
                if ($result_queryp->num_rows > 0) {
            ?>
                    <div class="table-responsive">
                        <table class='table mt-3 table-hover table-striped bg-white'>
                            <tr class='bg-secondary text-white'>
                                <th class='col-auto'>Image</th>
                                <th class='col-auto'>Title</th>
                                <th class='col-auto'>Author</th>
                                <th class='col-auto'>Price</th>
                                <th class='col-auto'>Actions</th>
                            </tr>
                            <?php
                            while ($row = $result_queryp->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><img src=<?php echo $row['image'] ?> style='max-width: 40px;'></td>
                                    <td><?php echo $row['title'] ?></td>
                                    <td><?php echo $row['author'] ?></td>
                                    <td><?php echo $row['price'] ?></td>
                                    <td>
                                        <form method='post' action='books.php'>
                                            <input type="hidden" name="page" value="<?php echo $page; ?>">
                                            <?php if (isset($_GET['subcategory'])) : ?>
                                                <input type="hidden" name="subcategory" value="<?php echo htmlspecialchars($_GET['subcategory']); ?>">
                                            <?php endif; ?>
                                            <?php if (isset($_GET['query'])) : ?>
                                                <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                                            <?php endif; ?>
                                            <?php if (isset($_GET['order'])) : ?>
                                                <input type="hidden" name="order" value="<?php echo htmlspecialchars($_GET['order']); ?>">
                                            <?php endif; ?>
                                            <?php if (!empty($row['title']) && !empty($row['price'])) : ?>
                                                <input type='hidden' name='product_name' value="<?php echo htmlspecialchars($row['title']); ?>">
                                                <input type='hidden' name='product_price' value="<?php echo htmlspecialchars($row['price']); ?>">
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-center">
                                                <button type='submit' name='add_to_cart' class='btn btn-success bi bi-plus'></button>
                                                <button type='submit' name='remove_from_cart' class='btn btn-danger bi bi-dash'></button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                }
            } else {
                if (isset($_GET['query'])) {
                    $search_query = mysqli_real_escape_string($conn, $_GET['query']);
                    $sql_query = "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";

                    $offset = ($page - 1) * $pagination;
                    $sql_query .= " LIMIT $offset, $pagination";

                    $result_query = mysqli_query($conn, $sql_query);

                    if ($result_query->num_rows > 0) {
                    ?>
                        <div class="table-responsive">
                            <table class='table mt-3 table-hover table-striped bg-white'>
                                <tr class='bg-secondary text-white'>
                                    <th class='col-auto'>Image</th>
                                    <th class='col-auto'>Title</th>
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
                                            <form method='post' action='books.php'>
                                                <input type="hidden" name="page" value="<?php echo $page; ?>">
                                                <?php if (isset($_GET['subcategory'])) : ?>
                                                    <input type="hidden" name="subcategory" value="<?php echo htmlspecialchars($_GET['subcategory']); ?>">
                                                <?php endif; ?>
                                                <?php if (isset($_GET['query'])) : ?>
                                                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                                                <?php endif; ?>
                                                <?php if (isset($_GET['order'])) : ?>
                                                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($_GET['order']); ?>">
                                                <?php endif; ?>
                                                <?php if (!empty($row['title']) && !empty($row['price'])) : ?>
                                                    <input type='hidden' name='product_name' value="<?php echo htmlspecialchars($row['title']); ?>">
                                                    <input type='hidden' name='product_price' value="<?php echo htmlspecialchars($row['price']); ?>">
                                                <?php endif; ?>
                                                <div class="d-flex justify-content-center">
                                                    <button type='submit' name='add_to_cart' class='btn btn-success bi bi-plus'></button>
                                                    <button type='submit' name='remove_from_cart' class='btn btn-danger bi bi-dash'></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div>
                            <h2 class='text-center mt-3 text-success bg-light'>No Books Found</h2>
                        </div><?php
                            }
                        } else {
                            if (isset($result) && $result->num_rows > 0) {
                                ?>
                        <div class="table-responsive">
                            <table class='table mt-3 table-hover table-striped bg-white'>
                                <tr class='bg-secondary text-white'>
                                    <th class='col-auto'>Image</th>
                                    <th class='col-auto'>Title</th>
                                    <th class='col-auto'>Author</th>
                                    <th class='col-auto'>Price</th>
                                    <th class='col-auto'>Actions</th>
                                </tr>
                                <?php
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><img src=<?php echo $row['image'] ?> style='max-width: 40px;'></td>
                                        <td><?php echo $row['title'] ?></td>
                                        <td><?php echo $row['author'] ?></td>
                                        <td><?php echo $row['price'] ?></td>
                                        <td>
                                            <form method='post' action='books.php'>
                                                <input type="hidden" name="page" value="<?php echo $page; ?>">
                                                <?php if (isset($_GET['subcategory'])) : ?>
                                                    <input type="hidden" name="subcategory" value="<?php echo htmlspecialchars($_GET['subcategory']); ?>">
                                                <?php endif; ?>
                                                <?php if (isset($_GET['query'])) : ?>
                                                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                                                <?php endif; ?>
                                                <?php if (isset($_GET['order'])) : ?>
                                                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($_GET['order']); ?>">
                                                <?php endif; ?>
                                                <?php if (!empty($row['title']) && !empty($row['price'])) : ?>
                                                    <input type='hidden' name='product_name' value="<?php echo htmlspecialchars($row['title']); ?>">
                                                    <input type='hidden' name='product_price' value="<?php echo htmlspecialchars($row['price']); ?>">
                                                <?php endif; ?>
                                                <div class="d-flex justify-content-center">
                                                    <button type='submit' name='add_to_cart' class='btn btn-success bi bi-plus'></button>
                                                    <button type='submit' name='remove_from_cart' class='btn btn-danger bi bi-dash'></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </table>
                        </div>
                    <?php
                            } else {
                    ?>
                        <div>
                            <h2 class='text-center mt-3 text-success bg-light'>No Books Found</h2>
                        </div><?php
                            }
                        }
                    }
                                ?>
            <?php
            $pagination_url = "books.php?";
            if (isset($_GET['query'])) {
                $pagination_url .= "query=" . urlencode($_GET['query']) . "&";
            }
            if (isset($_GET['order'])) {
                $pagination_url .= "order=" . urlencode($_GET['order']) . "&";
            }
            if (isset($_GET['subcategory'])) {
                $pagination_url .= "subcategory=" . urlencode($_GET['subcategory']) . "&";
            }
            if (isset($_GET['page'])) {
                $pagination_url .= "page=" . urlencode($_GET['page']) . "&";
            }
            ?>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1) : ?>
                    <li class="page-item"><a class="page-link" href="<?php echo $pagination_url; ?>page=1">First</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo $pagination_url; ?>page=<?php echo ($page - 1); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++) : ?>
                    <li class="page-item <?php echo ($i == $page ? 'active' : ''); ?>"><a class="page-link" href="<?php echo $pagination_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages) : ?>
                    <li class="page-item"><a class="page-link" href="<?php echo $pagination_url; ?>page=<?php echo ($page + 1); ?>">Next</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo $pagination_url; ?>page=<?php echo $total_pages; ?>">Last</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </main>

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

    <!-- Modal Login -->
    <div class="modal fade" id="loginmodal" tabindex="-1" aria-labelledby="loginmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login / Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="container">
                            <div class="text-center d-flex justify-content-evenly">
                                <button type="submit" class="btn btn-primary">Login</button>
                                <a href="register.php" class="nav-link btn btn-mod text-mod" id="login-tab" data-bs-toggle="modal" data-bs-target="#registermodal" type="button" role="tab" aria-controls="registermodal" aria-selected="false">Register</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Register-->
        <div class="modal fade" id="registermodal" tabindex="-1" aria-labelledby="registermodalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="nome1" class="form-label">Primeiro Nome</label>
                                <input type="text" class="form-control" name="nome1" required>
                            </div>
                            <div class="mb-3">
                                <label for="nome2" class="form-label">Ultimo Nome</label>
                                <input type="text" class="form-control" name="nome2">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="container">
                                <div class="text-center d-flex justify-content-evenly">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                    <a href="register.php" class="nav-link btn btn-mod text-mod" id="login-tab" data-bs-toggle="modal" data-bs-target="#registermodal" type="button" role="tab" aria-controls="registermodal" aria-selected="false">Login</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>