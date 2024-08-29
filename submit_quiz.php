<?php
session_start();
include 'inc/db.php';

$score = 0;
$totalQuestions = 20; // Set total number of questions
$correctAnswers = 0;

$userName = $_SESSION['fullname'];

// Fetch all questions
$sql = "SELECT * FROM questions";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $questionId = $row['id'];
    $correctOption = $row['correct_option'];
    if (isset($_POST['question' . $questionId]) && $_POST['question' . $questionId] == $correctOption) {
        $correctAnswers++;
    }
}

$percentage = ($correctAnswers / $totalQuestions) * 100;

// Store result in database
$userId = 1; // Replace with actual user ID if available
$stmt = $conn->prepare("INSERT INTO results (user_id, correct_answers, total_questions, percentage, fullname) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiids", $userId, $correctAnswers, $totalQuestions, $percentage, $userName);
$stmt->execute();

// Set session variables for displaying results
$_SESSION['correctAnswers'] = $correctAnswers;
$_SESSION['totalQuestions'] = $totalQuestions;
$_SESSION['percentage'] = $percentage;
$_SESSION['createdAt'] = date('Y-m-d H:i:s'); // Store current timestamp

// Redirect to result.php
header("Location: result.php");
exit();
?>
