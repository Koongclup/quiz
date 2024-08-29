<?php
include '../inc/db.php';
include '../class/QuestionManager.php';

$action = $_GET['action'];
$data = $_POST;
$manager = new QuestionManager($conn);

echo $manager->processRequest($action, $data);
?>
