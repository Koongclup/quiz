<?php
session_start();

// Include page content based on the requested URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to 'home'

$allowed_pages = ['home', 'user', 'admin', 'dashboard', 'index', 'quiz'];
if (in_array($page, $allowed_pages)) {
    include "{$page}.php";
} else {
    include '404.php'; // Include 404 page if the page is not found
}
?>
