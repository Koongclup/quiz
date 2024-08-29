<?php
include '../inc/db.php'; // Adjust path as needed
include '../class/UserManager.php'; // Adjust path as needed

$user = new User($conn);

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'getUserCounts':
        echo json_encode($user->getUserCounts());
        break;
    
    case 'getUsers':
        echo json_encode($user->getUsers());
        break;
    
    case 'createUser':
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];
        echo json_encode($user->createUser($username, $password, $role_id));
        break;
    
    case 'updateUser':
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];
        echo json_encode($user->updateUser($id, $username, $password, $role_id));
        break;
    
    case 'deleteUser':
        $id = $_POST['id'];
        echo json_encode($user->deleteUser($id));
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

?>
