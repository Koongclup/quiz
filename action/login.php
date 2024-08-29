<?php
include '../inc/db.php'; // Adjust path as needed
include '../class/LoginManager.php'; // Adjust path as needed

$action = $_GET['action'] ?? '';
$loginManager = new LoginManager($conn);

if ($action === 'loginUser') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $loginManager->authenticate($username, $password);

    if ($user) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_id'];
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'Invalid action';
}
?>
