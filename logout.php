<?php
session_start();

// Destroy the session and clear session data
session_unset();
session_destroy();

// Redirect to the login page or home page
header("Location: home");
exit();
?>
