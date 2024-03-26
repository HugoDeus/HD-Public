<?php

include("connection.php");

session_start();
include('restricted.php');
checkPermissions();

if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    $sql = "SELECT * FROM messages WHERE id = $message_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $message_id = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $date = $row['date'];
        $message = $row['message'];
        $read_status = $row['read_status'];

        $change_read = "UPDATE messages SET read_status = 1 WHERE id = $message_id";
        $conn->query($change_read);
    } else {
        header("Location: message.php");
        exit();
    }
} else {
    header("Location: message.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="text-center mt-4">
            <h2 class="text-success">Message Details</h2>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text"><span class="fs-5 text-success">Send for:</span> <span class="text-secondary"><?php echo $name; ?></span></p>
                        <p class="card-text mb-2"><span class="fs-5 text-success">Email: </span><span class="text-secondary"><?php echo $email; ?></span></p>
                        <p class="card-text"><span class="fs-5 text-success">Message Date: </span><span class="text-secondary"><?php echo $date; ?></span></p>
                        <p class="card-text text-center"><span class="fs-5 text-success">Message: </span></p>
                        <p class="text-center alert-secondary rounded p-1"><?php echo $message; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <p class="text-center"><a href="message.php" class="btn btn-primary">Go back</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>