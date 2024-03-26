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
    $sql = "SELECT * FROM contacts WHERE user_id = $id";
    $result = mysqli_query($conn, $sql);
    
    // If the user has no contact details, redirect to the contact details page
    if (mysqli_num_rows($result) == 0) {
        header("Location: details_contact.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['products'])) {
    $products = $_POST['products'];

    $_SESSION['cart_products'] = $products;
   
    } else {
        echo "os dados ja foram enviados";
    }
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];

        $sql_users = "SELECT * FROM users WHERE id = $id";
        $data_users = mysqli_query($conn, $sql_users);
        $row_users = mysqli_fetch_assoc($data_users);

        $sql = "SELECT * FROM contacts WHERE user_id =$id";
        $data = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($data);
    }

    $cartInfo = calculateCart();
    $totalCart = $cartInfo['totalPrice'];
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @keyframes blink {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .blinking {
            animation: blink 1s infinite;
        }
    </style>
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
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header>
        <!-- Welcome  -->
        <div class="container-fluid d-flex justify-content-between align-items-center bgspace">
            <div>
                <?php
                if (isset($_SESSION['user_name'])) {
                    echo "<span class='text-user'>Welcome, </span> <span class='usernametx'>" . $_SESSION['user_name'] . "</span>";
                } else {
                    echo "<span class='text-user'>Welcome</span>, <span class='usernametx'>Visitor</span>";
                }
                ?>
            </div>
            <div>
                <?php
                if (isset($_SESSION['user_name'])) {
                    echo '<a href="logout.php" class="btn btn-danger btn-sm" role="tab" aria-selected="false">Sair</a>';
                } else {
                    echo '<button class="btn btn-sm btn-primary" href="login.php" data-bs-toggle="modal" data-bs-target="#loginmodal" type="button" role="tab" aria-controls="loginmodal" aria-selected="false">Login/Register</button>';
                }
                ?>
            </div>

        </div>
    </header>

    <main>
        <div class="container text-white">
            <div class="container mt-5 bg-secondary rounded-2 border border-2 border-dark">
                <h1 class="mb-4 text-center">Shipping Data</h1>
                <form action="payment_successfully.php" method="post" id="shippingForm" onsubmit="return validateForm()">
                    <h2 class="text-center bg-success">Customer Information</h2>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="firstname" class="form-label">First Name:</label>
                            <span class="form-control">
                                <?php echo $row_users['firstname'] ?>
                            </span>
                            <input type="hidden" name="firstname" value="<?php echo $row_users['firstname']; ?>">
                        </div>
                        <div class="col">
                            <label for="lastname" class="form-label">Last Name:</label>
                            <span class="form-control">
                                <?php echo $row_users['lastname'] ?>
                            </span>
                            <input type="hidden" name="lastname" value="<?php echo $row_users['lastname']; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="email" class="form-label">Email:</label>
                            <span class="form-control">
                                <?php echo $row_users['email'] ?>
                            </span>
                            <input type="hidden" name="email" value="<?php echo $row_users['email']; ?>">
                        </div>
                        <div class="col">
                            <label for="nif" class="form-label">Nif:</label>
                            <span class="form-control">
                                <?php echo $row['nif'] ?>
                            </span>
                            <input type="hidden" name="nif" value="<?php echo $row['nif']; ?>">
                        </div>
                    </div>
                    <hr>
                    <h2 class="text-center bg-success">Address</h2>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="billingAddress" class="form-label">Address:</label>
                            <span class="form-control">
                                <?php echo $row['address'] ?>
                            </span>
                            <input type="hidden" name="address" value="<?php echo $row['address']; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="door_floor" class="form-label">Door/Floor:</label>
                            <span class="form-control">
                                <?php echo $row['door_floor'] ?>
                            </span>
                            <input type="hidden" name="door_floor" value="<?php echo $row['door_floor']; ?>">
                        </div>
                        <div class="col">
                            <label for="city" class="form-label">City:</label>
                            <span class="form-control">
                                <?php echo $row['city'] ?>
                            </span>
                            <input type="hidden" name="city" value="<?php echo $row['city']; ?>">
                        </div>
                        <div class="col">
                            <label for="zipcode" class="form-label">Zip-Code:</label>
                            <span class="form-control">
                                <?php echo $row['zipcode'] ?>
                            </span>
                            <input type="hidden" name="zipcode" value="<?php echo $row['zipcode']; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="billingCountry" class="form-label">Country</label>
                            <select name="country" class="form-select" id="billingCountry" required>
                                <option selected disabled>Select Country</option>
                            </select>
                        </div>
                        <div id="countryError" class="text-danger text-center fs-1 bg-dark" style="display: none;">Please. Select Country.
                        </div>
                    </div>
                    <hr>
                    <h2 class="text-center bg-success mb-3">Payment Method</h2>
                    <div class="container row mb-3">
                        <div class="col bg-white">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="paymentMethod1" class="form-label"></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod1" value="atm" required>
                                        <label class="form-check-label" for="paymentMethod1">
                                            <img class="img-fluid" src="images/multibanco1.png" alt="" style="max-width: 100%; width: 100%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="paymentMethod2" class="form-label"></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod2" value="paypal" required>
                                        <label class="form-check-label" for="paymentMethod2">
                                            <img class="img-fluid" src="images/paypal.png" alt="" style="max-width: 100%; width: 100%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="paymentMethod3" class="form-label"></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod3" value="mbway" required>
                                        <label class="form-check-label" for="paymentMethod3">
                                            <img class="img-fluid" src="images/mbway1.png" alt="" style="max-width: 100%; width: 100%;">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="text-center mb-4">
                        <input type="hidden" name="finalPrice" value="<?php echo $totalCart; ?>">
                        <button type="submit" class="btn btn-primary text-center">Proceed to Payment <?php echo $totalCart; ?> €</button>
                    </div>
                </form>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        // Make a GET request to the REST Countries API to get all countries
        fetch('https://restcountries.com/v3.1/all')
            .then(response => response.json())
            .then(data => {
                const selectElement = document.getElementById('billingCountry');
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));
                data.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.name.common;
                    option.text = country.name.common;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error search country:', error);
            });
        function validateForm() {
            var countrySelect = document.getElementById("billingCountry");
            var countryError = document.getElementById("countryError");

            if (countrySelect.value === "Select Country") {
                countryError.style.display = "block";
                countryError.classList.add("blinking");
                return false;
            } else {
                countryError.style.display = "none";
                countryError.classList.remove("blinking"); 
                return true; 
            }
        }
    </script>
</body>

</html>