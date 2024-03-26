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

$results_html = '';

if (isset($_SESSION['cart'])) {
    $productTotals = [];

    foreach ($_SESSION['cart'] as $item) {
        $productName = mysqli_real_escape_string($conn, $item['name']);

        $sql = "SELECT * FROM books WHERE title = '$productName'";
        $result = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            $productPrice = $row['price'];
            $productImage = $row['image'];
            $productAuthor = $row['author'];

            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            $total = $productPrice * $quantity;

            if (isset($productTotals[$productName])) {
                $productTotals[$productName]['quantity'] += $quantity;
                $productTotals[$productName]['total'] += $total;
            } else {
                $productTotals[$productName] = [
                    'quantity' => $quantity,
                    'total' => $total,
                    'image' => $productImage,
                    'author' => $productAuthor
                ];
            }
        }
    }

    foreach ($productTotals as $productName => $productData) {
        $productQuantity = $productData['quantity'];
        $productTotal = $productData['total'];
        $productImage = $productData['image'];
        $productAuthor = $productData['author'];
        $results_html .= "<tr>";
        $results_html .= "<td><img src='$productImage' alt='$productName' style='width: 50px;'></td>";
        $results_html .= "<td>$productName</td>";
        $results_html .= "<td>$productAuthor</td>";
        $results_html .= "<td>" . ($productTotal / $productQuantity) . " €</td>";
        $results_html .= "<td>$productQuantity</td>";
        $results_html .= "<td class='text-nowrap'>$productTotal €</td>";
        $results_html .= "<td>";
        $results_html .= "<form method='post' action=''><input type='hidden' name='productName' value='$productName'>
        <input type='hidden' name='productPrice' value='$productPrice'>                    
        <button type='submit' name='add_to_cart' class='btn btn-success me-1 bi bi-cart-plus'></button>";
        $results_html .= "<button type='submit' name='remove_from_cart' class='btn btn-danger bi bi-cart-dash'></button></form>";
        $results_html .= "</td>";
        $results_html .= "</tr>";
    }

    $totalPrice = array_sum(array_column($productTotals, 'total'));
} else {
    $results_html = "<tr><td colspan='6'>Your cart is empty.</td></tr>";
}
if (isset($_SESSION['cart'])) {
    $_SESSION['cart_order'] = $totalPrice;
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
        $productName = $_POST['productName'];
        $productPrice = $_POST['productPrice'];
        addToCart($productName, $productPrice);
        header('Location: cart.php');
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_from_cart'])) {
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];
    removeFromCart($productName, $productPrice);
    header('Location: cart.php');
    exit();
}

$cartInfo = calculateCart();
$totalCart = $cartInfo['totalPrice'];
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
            <div class="welcome">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2 active" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
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

    <div class="container my-auto">
        <h1 class="mb-4 text-center text-success mb-5">Shopping Cart</h1>
        <?php
        $teste = $cartInfo['totalPrice'];
        ?>
        <?php
        if (empty($productTotals)) {
        ?>
            <div class="container text-center">
                <h2 class="text-danger">Empty Cart</h2>
            </div>
        <?php
        } else {
        ?>
            <div class="table-responsive">
                <table class="table table-secondary table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col bg-dark text-white">Image</th>
                            <th class="col bg-dark text-white">Title</th>
                            <th class="col bg-dark text-white">Author</th>
                            <th class="col bg-dark text-white">Price</th>
                            <th class="col bg-dark text-white">Quantity</th>
                            <th class="col bg-dark text-white">Total</th>
                            <th class="col bg-dark text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $results_html; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <form action="checkout.php" method="post">
                    <h3 name="checkout_data" class="mb-4 fs-2 bg-dark text-white">Total: <?php echo number_format($totalCart, 2); ?> €</h3>
                    <?php foreach ($productTotals as $productName => $productData) : ?>
                        <input type="hidden" name="products[<?php echo $productName; ?>][author]" value="<?php echo $productData['author']; ?>">
                        <input type="hidden" name="products[<?php echo $productName; ?>][image]" value="<?php echo $productData['image']; ?>">
                        <input type="hidden" name="products[<?php echo $productName; ?>][quantity]" value="<?php echo $productData['quantity']; ?>">
                        <input type="hidden" name="products[<?php echo $productName; ?>][price]" value="<?php echo $productData['total'] / $productData['quantity']; ?>">
                        <input type="hidden" name="products[<?php echo $productName; ?>][total]" value="<?php echo $productData['total']; ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary">Complete Purchase</button>
            </div>
        <?php
        }
        ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>