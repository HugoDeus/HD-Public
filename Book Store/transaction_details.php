<?php
include('connection.php');

include("update_cart.php");


session_start(); // Iniciar sessão para verificar login

// Função para obter nome de usuário/tipo
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

// Obter nome de usuário/tipo
$userName = getUserName();

// Verifica se o ID da transação foi passado como parâmetro na URL
if (isset($_GET['id'])) {
    // Obtém o ID da transação da URL
    $transaction_id = $_GET['id'];
}
$cartInfo = calculateCart();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-light bg-success">
        <div class="container-fluid">
            <!-- Botão de menu responsivo -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Itens do menu -->
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Links do menu -->
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
            <!-- Carrinho de compras (exibido apenas quando o usuário está logado) -->
            <div class="welcome">
                <?php if (isset($_SESSION['user_name'])) : ?>
                    <a class="nav-link text-white bi bi-cart4 mb-2" href="cart.php">Cart (<span><?php echo $cartInfo['totalItems']; ?></span> itens, €<span><?php echo number_format($cartInfo['totalPrice'], 2); ?></span>)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



    <header>

        <!-- Mensagem boas-vindas e login/logout  -->
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

    <?php
    $sql = "SELECT t.id AS transaction_id, DATE(t.transaction_date) AS transaction_date, t.total_value, ti.product_name, ti.author, ti.image, ti.quantity, ti.unit_price, ti.total, s.firstname, s.lastname, s.email, s.nif, s.address, s.door_floor, s.city, s.zipcode, s.country
            FROM transactions t 
            INNER JOIN transaction_items ti ON t.id = ti.transaction_id 
            INNER JOIN shipments s ON t.id = s.transaction_id
            WHERE t.id = $transaction_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<div class='container my-auto'>";
            echo "<div class='table-responsive'>";
            echo "<h2 class='text-center text-success fs-1'>Orders</h2>";
            echo "<table class='container table table-secondary'>";
            echo "<tr><th>Image</th><th>Title</th><th>Autho</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td><img src='{$row['image']}' alt='{$row['image']}' width='60px' height='60px'></td>";
                echo "<td>{$row['product_name']}</td>";
                echo "<td>{$row['author']}</td>";
                echo "<td>{$row['quantity']}</td>";
                echo "<td class='text-nowrap'>{$row['unit_price']} €</td>";
                echo "<td class='text-nowrap'>{$row['total']} €</td>";
                echo "</tr>";

                $total_price = $row['total_value'];

                $shipment_details = array(
                    'Name' => $row['firstname'] . ' ' . $row['lastname'],
                    'Email' => $row['email'],
                    'NIF' => $row['nif'],
                    'Address' => $row['address'],
                    'Door/Floor' => $row['door_floor'],
                    'City' => $row['city'],
                    'Zipcode' => $row['zipcode'],
                    'Country' => $row['country']
                );
            }
            echo "<tr><td colspan='5' class='text-end'><strong>Total Price:</strong></td><td class='text-nowrap'>{$total_price} €</td></tr>";
            echo "</table>";
            echo "</div>";

            echo "<div class='table-responsive'>";
            echo "<h2 class='text-center text-success fs-1'>Shipment Details</h2>";
            echo "<table class='container table table-secondary'>";
            foreach ($shipment_details as $key => $value) {
                echo "<tr><td><strong>{$key}:</strong></td><td>{$value}</td></tr>";
            }
            echo "</table>";
            echo "</div>";
            echo "</div>";
            echo "<div class='text-center'><a href='user_Data.php' class='btn btn-success'>Back to My Profile</a></div>";
        } else {
            echo "<div class='my-auto text-center'>";
            echo "<p class='text-center bg-dark text-danger'>There are no details avalable for this transaction</p>" . $conn->error;

            echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
            echo "</div>";
        }
    } else {
        echo "<div class='my-auto text-center'>";
        echo "<p class='text-center bg-dark text-danger'>An error occurred while retrieving transaction details</p>" . $conn->error;

        echo "<a href='javascript:history.go(-1)' class='btn btn-danger my-auto'>&#8592; Back</a>";
        echo "</div>";
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>