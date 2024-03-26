<?php

include("connection.php");
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

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}

if (isset($_POST['add_to_cart'])) {
    addToCart($_POST['product_name'], $_POST['product_price']);
    header('Location: index.php');
    exit();
}
$cartInfo = calculateCart();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
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
                        <a class="nav-link text-white active" href="index.php">Homepage</a>
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
            <div class="welcome">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



    <header>
        <!-- SearchBar -->
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


    <main class="container-fluid">
        <div class="row align-items-center">
            <div class="col-auto bgmenu">
                <?php
                $sql = "SELECT * FROM categories";
                $result = $conn->query($sql);

                $categories = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $categories[] = $row;
                    }
                }
                ?>
                <div class="d-none d-sm-block">
                    <div class="col-md-4 col-12 d-flex flex-column justify-content-around">
                        <?php
                        foreach ($categories as $category) {
                            echo '<div class="btn-group dropend">';
                            echo '<button type="button" class="btn btncat dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">' . $category["category"] . '</button>';
                            echo '<ul class="dropdown-menu">';

                            $sub_sql = "SELECT * FROM subcategories WHERE category_id = " . $category["id"];
                            $sub_result = $conn->query($sub_sql);

                            if ($sub_result->num_rows > 0) {
                                while ($sub_row = $sub_result->fetch_assoc()) {
                                    echo '<form action="books.php" method="GET">';
                                    echo '<input type="hidden" name="category" value="' . $category["category"] . '">';
                                    echo '<li class="dropdown-item" style="cursor: pointer;"><button class="btn-query-category" type="submit" name="subcategory" value="' . $sub_row["subcategory"] . '">' . $sub_row["subcategory"] . '</button></li>';
                                    echo "</form>";
                                }
                            } else {
                                echo "No Subcategories here";
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="d-block d-sm-none">
                    <div class="d-inline-flex gap-1">
                        <button class="btn btn-success mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories">
                            Categories
                        </button>
                        <div class="collapse" id="collapseCategories">
                            <div class="card card-body">
                                <?php
                                foreach ($categories as $category) {
                                    echo '<div class="btn-group dropend">';
                                    echo '<button type="button" class="btn btncat dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">' . $category["category"] . '</button>';
                                    echo '<ul class="dropdown-menu">';

                                    $sub_sql = "SELECT * FROM subcategories WHERE category_id = " . $category["id"];
                                    $sub_result = $conn->query($sub_sql);

                                    if ($sub_result->num_rows > 0) {
                                        while ($sub_row = $sub_result->fetch_assoc()) {
                                            echo '<form action="books.php" method="GET">';
                                            echo '<input type="hidden" name="category" value="' . $category["category"] . '">';
                                            echo '<li class="dropdown-item" style="cursor: pointer;"><button class="btn-query-category" type="submit" name="subcategory" value="' . $sub_row["subcategory"] . '">' . $sub_row["subcategory"] . '</button></li>';
                                            echo "</form>";
                                        }
                                    } else {
                                        echo "No Subcategories here";
                                    }
                                    echo '</ul>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="container-fluid col-md-4 col-6 col-sm-4 col-12">
                <div class="container-fluid d-flex justify-content-center align-items-center">
                    <img class="img-fluid w-75 my-auto me-2" src="images/banner/package1.png" alt="" id="slider" style="height: 150px;">
                </div>
            </div>
            <div class="container-fluid col-md-4 col-6 col-sm-4 col-12">
                <div>
                    <p class="container-fluid text-uppercase d-flex justify-content-center align-items-center text-center font-ship my-auto ms-2">Free Shipping. <br>Quality assurance. <br> Next day shipping.</p>
                </div>
            </div>
        </div>
    </main>

    <aside>
        <div class="container justify-content-center center-car mt-5">
            <div class="col-9 d-flex justify-content-center">
                <div id="sliderboot" class="carousel slide" data-bs-ride="carousel" style="max-width: 209px;">
                    <ol class="carousel-indicators btn">
                        <li data-bs-target="#sliderboot" data-bs-slide-to="0" class="active" aria-current="true" aria-label="First slide"></li>
                        <li data-bs-target="#sliderboot" data-bs-slide-to="1" aria-label="Second slide"></li>
                        <li data-bs-target="#sliderboot" data-bs-slide-to="2" aria-label="Third slide"></li>
                    </ol>
                    <div class="carousel-inner" role="listbox" style="max-height: 300px;">
                        <div class="carousel-item active">
                            <img src="images/livro1slider.jpeg" class="w-100 d-block" alt="First slide" style="max-height: 300px;">
                        </div>
                        <div class="carousel-item">
                            <img src="images/livro2slider.jpeg" class="w-100 d-block" alt="Second slide" style="max-height: 300px;">
                        </div>
                        <div class="carousel-item">
                            <img src="images/livro3slider.jpeg" class="w-100 d-block" alt="Third slide" style="max-height: 300px;">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#sliderboot" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon btn" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#sliderboot" data-bs-slide="next">
                        <span class="carousel-control-next-icon btn" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <div class="bgspace">

    </div>

    <div class="container py-4">
        <h1 class="text-center text-danger fw-bold"><span id="deals">New Hot Deals</span></h1>
    </div>

    <div class="container-fluid pb-3">
        <?php
        $sql = "SELECT * FROM books ORDER BY id DESC LIMIT 5";
        $result = $conn->query($sql);
        ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 justify-content-around align-content-around p-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="card card-hotdeals col m-1">
                        <div class="d-flex mt-1 align-items-center justify-content-center" style="height: 200px;">
                            <img class="card-img-top img-fluid" src="<?php echo $row['image']; ?>" alt="<?php echo $row['author']; ?>">
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h4 class="card-title mb-1" style="max-height: 50px; overflow: hidden;"><?php echo $row['title']; ?></h4>
                            <hr>
                            <div class="text-center">
                                <form method="post" action="index.php">
                                    <input type="hidden" name="add_to_cart" value="true">
                                    <input type="hidden" name="product_name" value="<?php echo $row['title']; ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                                    <div class="text-nowrap">
                                        <button type="submit" class="btn-sm button-card btn-primary bi bi-cart-plus" onclick="addToCart('<?php echo $row['title']; ?>',<?php echo $row['price']; ?>)" class="btn-sm btn-buy"><span class='buy'> Buy </span><?php echo $row['price']; ?> €</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "No Results Found";
            }
            ?>
        </div>
    </div>



    <!--Methods Payments-->

    <div class="container-fluid">
        <div class="row row-col-2 row-col-lg-4 row-col-md-3 my-3 d-flex justify-content-between">
            <div class="col">
                <img class="img-fluid" src="images/multibanco1.png" alt="" style="max-width: 100%; width: 100%;">
            </div>
            <div class="col">
                <img class="img-fluid" src="images/paypal.png" alt="" style="max-width: 100%; width: 100%;">
            </div>
            <div class="col">
                <img class="img-fluid" src="images/mbway1.png" alt="" style="max-width: 100%; width: 100%;">
            </div>

        </div>

    </div>


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
            <script src="assets/js/script.js"></script>

</body>

</html>