<?php
session_start();

// ตรวจสอบว่ามีการส่งชื่อ - สกุล ผ่าน POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fullname'])) {
    // เก็บชื่อ - สกุลใน session
    $_SESSION['fullname'] = $_POST['fullname'];

    // ดึงชื่อ - สกุล จาก session
    $userName = $_SESSION['fullname'];
} else {
    // ถ้าไม่มีการส่งชื่อ - สกุล ให้กลับไปที่หน้าแรก
    header("Location: home");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Knowledge Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require_once 'inc/templete/header.php'; ?>
<div class="row mx-3 mt-5">
<div class="col-md-6 mx-auto mt-5">
    <h2 class="text-primary">Digital Knowledge Quiz</h2>
    <br>
    <p class="fw-bold">ยินดีต้อนรับ : <?=$userName?></p>
    <br>
    <form id="quizForm" method="post" action="submit_quiz.php">
        <?php
        
        include 'inc/db.php';
        $sql = "SELECT * FROM questions";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo '<div class="mb-3">';
            echo '<label class="form-label fw-bold"> ข้อ ' .$row['id'].' . '.$row['question_text'] . '</label>';
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="question' . $row['id'] . '" value="A" required>';
            echo '<label class="form-check-label">' . $row['option_a'] . '</label>';
            echo '</div>';
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="question' . $row['id'] . '" value="B">';
            echo '<label class="form-check-label">' . $row['option_b'] . '</label>';
            echo '</div>';
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="question' . $row['id'] . '" value="C">';
            echo '<label class="form-check-label">' . $row['option_c'] . '</label>';
            echo '</div>';
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="radio" name="question' . $row['id'] . '" value="D">';
            echo '<label class="form-check-label">' . $row['option_d'] . '</label>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div id="result" class="mt-4"></div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
