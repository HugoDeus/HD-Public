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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $mensagem = $_POST["mensagem"];
    
    // Enviar email para o administrador
    $to = "testes.devhugo@gmail.com"; // Troque pelo endereço de email do administrador
    $subject = "Nova mensagem de contato de $nome";
    $message = "Nome: $nome\nEmail: $email\nMensagem:\n$mensagem";
    mail($to, $subject, $message);

    // Redirecionar de volta para a página de contato com uma mensagem de sucesso
    header("Location: contact.php?enviado=1");
    exit();
}
$cartInfo = calculateCart();
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

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
                        <a class="nav-link text-white active" href="about.php">About Us</a>
                    </li>
                </ul>
            </div>
            <div class="welcome">
                <!-- Cart -->
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



    <header>
        <!-- Welcome navbar -->
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
        <div class="container d-flex flex-column mt-4 alert-secondary text-center rounded">
            <div>
                <h2 class="text-dark fw-bolder"><u>About Us...</u></h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt sequi eius, quidem dicta alias itaque nostrum? Distinctio, deserunt magni nostrum aperiam repellat explicabo totam perspiciatis qui aliquid unde deleniti sequi?
                Repellat nostrum sint eaque eos pariatur corrupti amet assumenda sunt similique ratione numquam voluptatum facilis, accusamus excepturi rerum dolores quod inventore cumque obcaecati. Non tempore sunt labore praesentium eligendi deserunt.
                In, debitis? Porro, corrupti natus! Eaque, rerum. Amet perspiciatis odio vero quaerat fuga, nulla qui ducimus nisi commodi asperiores quo minus corrupti voluptate. Magnam laudantium culpa inventore dolore eius ut!</p>
            </div>
            <hr style="height: 20px; border-radius: 50%;">
            <div>
                <h2 class="text-dark fw-bolder"><u>Our Goals</u></h2>
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Magnam expedita voluptas odio! Unde cupiditate omnis sequi consectetur iusto numquam odit maxime nihil dolorem nam modi quibusdam, placeat assumenda, reprehenderit aliquam.
                Reprehenderit distinctio neque saepe doloremque soluta ut iusto quidem qui mollitia voluptatem cumque doloribus architecto, consequuntur ad vero quasi aliquid laboriosam, quis aspernatur! Molestiae vitae voluptate hic cumque ut vel?</p>
            </div>
            <hr style="height: 20px; border-radius: 50%;">
            <div>
                <h2 class="text-dark fw-bolder"><u>Our Mission</u></h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim accusantium voluptates, numquam harum eius illo provident expedita recusandae suscipit nulla unde natus temporibus iusto tempore sequi animi quibusdam nemo iste!
                Laudantium sequi alias numquam dicta temporibus, qui reiciendis in ut ex nesciunt facere provident vitae corrupti eligendi necessitatibus dignissimos delectus consectetur quibusdam, vel perferendis. Veniam, iure aperiam! Tempora, at natus.
                Blanditiis sit beatae quod, voluptas sapiente quas aperiam natus quo nam, ratione et. Suscipit facere reprehenderit blanditiis debitis hic quod quibusdam in excepturi placeat sunt quasi nulla, ipsam ab maiores.</p>
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