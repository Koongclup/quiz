<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != '1') {
    header("Location: index.php");
    exit();
}

// Include database connection file
require_once 'inc/db.php';

// Get the parameters from the URL
$name = isset($_GET['name']) ? $_GET['name'] : '';
$percentage = isset($_GET['percentage']) ? $_GET['percentage'] : '';

// Prepare the SQL query using MySQLi
$stmt = $conn->prepare("SELECT * FROM results WHERE fullname = ? AND percentage = ?");
$stmt->bind_param("ss", $name, $percentage);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

// Check if user details are found
if (!$userDetails) {
    echo "<p class='text-danger'>No details found for the specified user.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการสอบ - <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once 'templete/navbar.php'; ?>
    <br><br>
    <div class="container mt-5">
        <h3 class="text-primary">รายละเอียดการสอบของ <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h3>
        <div class="card mt-3">
            <div class="card-body">
                <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($userDetails['fullname'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>คะแนนที่ถูกต้อง:</strong> <?php echo htmlspecialchars($userDetails['correct_answers'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>จำนวนคำถามทั้งหมด:</strong> <?php echo htmlspecialchars($userDetails['total_questions'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>เปอร์เซ็นต์:</strong> <?php echo htmlspecialchars($userDetails['percentage'], ENT_QUOTES, 'UTF-8'); ?>%</p>
                <p><strong>วันที่สอบ:</strong> <?php echo htmlspecialchars($userDetails['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
        <a href="dashboard.php" class="btn btn-primary mt-4">กลับไปที่แดชบอร์ด</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
