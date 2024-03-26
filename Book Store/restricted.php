<?php
function checkPermissions() {
    // Define permissions
    $permissions = array(
        'add_category.php' => 'Admin',
        'add_subcategory.php' => 'Admin',
        'addbooks.php' => 'Admin',
        'admin_order_details.php' => 'Admin',
        'admin.php' => 'Admin',
        'adminbooks.php' => 'Admin',
        'edit_category.php' => 'Admin',
        'edit_subcategory.php' => 'Admin',
        'edit_book.php' => 'Admin',
        'edit_profile.php' => 'Admin',
        'message.php' => 'Admin',
        'process_book.php' => 'Admin',
        'update_book.php' => 'Admin',
        'users.php' => 'Admin',
        'view_message.php' => 'Admin',
        'view_profile.php' => 'Admin',
    );

    $current_page = basename($_SERVER['SCRIPT_FILENAME']);

    // Check logged and permissions
    if (!isset($_SESSION['user_name']) || 
        !isset($permissions[$current_page]) ||
        $_SESSION['permissions'] !== $permissions[$current_page]) {
            include 'html_pages.php';
        echo "<div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;' class='container-fluid alert alert-danger text-center'><p><h2><span class='text-danger bg-dark'>WARNING RESTRICTED AREA.</span></h2></p><hr><p><em>Go to Homepage after 5 seconds</em></p></div>";
        header('refresh:5; url=index.php');
        exit();
    }
}
checkPermissions();
?>
