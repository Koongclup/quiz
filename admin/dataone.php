<?php
session_start();
include '../inc/db.php';

// Fetch result from session


// Clear session variables after fetching
/*
unset($_SESSION['correctAnswers']);
unset($_SESSION['totalQuestions']);
unset($_SESSION['percentage']);
unset($_SESSION['createdAt']); */

// Fetch the results for the user
$Name = $_GET['name'];
if (!empty($Name) ) {
    $sql = "SELECT * FROM results WHERE fullname = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $Name);
    $stmt->execute();
    $result = $stmt->get_result();
    $latestResult = $result->fetch_assoc();
    $correctAnswers = $latestResult['correct_answers'];
    $totalQuestions = $latestResult['total_questions'];
    $percentage = $latestResult['percentage'];
    $createdAt = $latestResult['created_at'];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลคะแนน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.min.js"></script>
</head>

<body>
    <?php require_once '../inc/templete/navbar.php'; ?>
    <br><br>
    <div class="container mt-5">
        <h4>คุณทำคะแนนได้ <?php echo htmlspecialchars($correctAnswers); ?> จาก <?php echo htmlspecialchars($totalQuestions); ?> ข้อ คะแนนของคุณคือ <?php echo htmlspecialchars($percentage); ?>%</h4>

        <!-- Fetch and display the results summary -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Correct Answers</th>
                    <th>Total Questions</th>
                    <th>Percentage</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($latestResult): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($latestResult['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($latestResult['correct_answers']); ?></td>
                        <td><?php echo htmlspecialchars($latestResult['total_questions']); ?></td>
                        <td><?php echo htmlspecialchars($latestResult['percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($latestResult['created_at']); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-md-6">
                <div id="resultBarChart" class="mt-4"></div>
            </div>
            <div class="col-md-6">
                <div id="resultPieChart" class="mt-4"></div>
            </div>
        </div>
        <!-- Generate a bar chart using ApexCharts -->


        <!-- Generate a pie chart using ApexCharts -->


        <script>
            // Bar Chart
            var barOptions = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Score',
                    data: [<?php echo $correctAnswers; ?>, <?php echo $totalQuestions - $correctAnswers; ?>]
                }],
                xaxis: {
                    categories: ['ตอบถูก', 'ตอบผิด']
                },
                colors: ['#00E396', '#FF4560'], // Customize colors
                title: {
                    text: 'Quiz Results (Bar Chart)',
                    align: 'left'
                }
            };

            var barChart = new ApexCharts(document.querySelector("#resultBarChart"), barOptions);
            barChart.render();

            // Pie Chart
            var pieOptions = {
                chart: {
                    type: 'pie',
                    height: 350
                },
                series: [<?php echo $correctAnswers; ?>, <?php echo $totalQuestions - $correctAnswers; ?>],
                labels: ['ตอบถูก', 'ตอบผิด'],
                colors: ['#00E396', '#FF4560'], // Customize colors
                title: {
                    text: 'Quiz Results (Pie Chart)',
                    align: 'left'
                }
            };

            var pieChart = new ApexCharts(document.querySelector("#resultPieChart"), pieOptions);
            pieChart.render();
        </script>
    </div>
</body>

</html>