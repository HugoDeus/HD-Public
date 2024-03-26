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

$message_client = "";

if (isset($_SESSION['user_name'])) {
    $username = $_SESSION['user_name'];
    $sql = "SELECT id, firstname, lastname, email, user FROM users WHERE user='$username'";
    $query = $conn->query($sql);
    if ($query) {
        if ($query->num_rows > 0) {
            $result = $query->fetch_assoc();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(mysqli_real_escape_string($conn, $_POST["name"]));
    $email = isset($result['email']) ? $result['email'] : trim(mysqli_real_escape_string($conn, $_POST["email"]));
    $message = trim(mysqli_real_escape_string($conn, $_POST["message"]));
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql_message = "INSERT INTO messages (name, email, message, read_status,date) VALUES ('$name', '$email', '$message', FALSE, NOW())";
        $message_query = $conn->query($sql_message);

        if ($message_query) {
            $message_client = "<h3 class='bg-success text-white text-center'>Message sent successfully</h3>";
        } else {
            $message_client = "<h3 class='alert-danger text-dark text-center'>ERROR sending message.</h3>";
        }
    } else {
        $message_client = "<h3 class='alert-danger text-dark text-center'>Please fill all the fields</h3>";
    }
}
$cartInfo = calculateCart();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
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
                        <a class="nav-link text-white active" href="contact.php">Contact</a>
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
    
    <div class="container my-3">
        <div class="row">
            <div class="col-md-6 mb-3 my-auto">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d12462.870485328147!2d-9.027550038916013!3d38.65537274056115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1spt-PT!2spt!4v1696332790485!5m2!1spt-PT!2spt" width="auto" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="container" id="message">
                <h5 class="text-center"><?php echo $message_client; ?></h5>
            </div>
            <div class="col-md-6 alert-secondary mb-3">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo isset($result['firstname']) ? $result['firstname'] : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo isset($result['email']) ? $result['email'] : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="textarea" class="form-label">Message To Admin</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" name="message" rows="3" placeholder="Write your message here..."></textarea>
                    </div>

                    <div class="mb-3 text-center">
                        <input type="submit" value="Send..." class="btn btn-primary mx-auto">
                    </div>
                </form>

            </div>
        </div>
        <div class="row">
            <div class="container text-center alert-success">
                <h2>Speak With us...</h2>
                <div class="speakus">
                    <p>You can send us a message through the form above.</p>
                    <p>Call our office during business hours.</p>
                    <p>Email us with your questions or comments.</p>
                </div>
                <h2 class="my-3">Contact Information</h2>
                <div class="text-start">
                    <p><strong>Adress: </strong> Rua Artur Paiva - Nrº 3</p>
                    <p><strong>Tel: </strong> +351 911 110 110</p>
                    <p><strong>Email: </strong>testes.devhugo@gmail.com</p>
                    <p><strong>Opening Hours: </strong>Monday to Friday from 9am to 6pm</p>
                </div>
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