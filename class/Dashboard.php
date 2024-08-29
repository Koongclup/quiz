<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != '1') {
    header("Location: index.php"); // Redirect to index.php if not logged in or not an admin
    exit();
}

include '../inc/db.php';

$action = $_GET['action'];
$manager = new DashboardManager($conn);

if ($action === 'fetchSummary') {
    echo $manager->fetchSummary();
}

class DashboardManager
{
    private $conn;

    public function __construct($dbConn)
    {
        $this->conn = $dbConn;
    }

    public function fetchSummary()
    {
        $response = array();

        // Fetch overall score data
        $query = "SELECT COUNT(*) AS total_users, 
                         SUM(correct_answers) AS total_correct_answers,
                         SUM(total_questions) AS total_questions 
                  FROM results";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();

        $total_users = $row['total_users'];
        $total_correct_answers = $row['total_correct_answers'];
        $total_questions = $row['total_questions'];
        $total_incorrect_answers = $total_questions - $total_correct_answers;

        // Data for the overall pie chart
        $response['overall'] = [
            ['name' => 'ตอบถูก', 'y' => $total_correct_answers],
            ['name' => 'ตอบผิด', 'y' => $total_incorrect_answers]
        ];

        // Average score data
        $average_correct = $total_users ? $total_correct_answers / $total_users : 0;
        $average_incorrect = $total_users ? $total_incorrect_answers / $total_users : 0;

        $response['average'] = [
            ['name' => 'ตอบถูกเฉลี่ย', 'y' => $average_correct],
            ['name' => 'ตอบผิดเฉลี่ย', 'y' => $average_incorrect]
        ];

        // Data for questions chart
        $query = "SELECT id, fullname, 
                         correct_answers, 
                         total_questions, 
                         ROUND((correct_answers / total_questions), 2) * 100 AS percentage ,date(created_at) as dated
                  FROM results";
        $result = $this->conn->query($query);
        $questions = array();
        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'fullname' => $row['fullname'],
                'percentage' => (float)$row['percentage']
            ];
        }
        $response['questions'] = $questions;

        // Data for details table
        $query = "SELECT id, fullname, 
                         correct_answers, 
                         total_questions, 
                         ROUND((correct_answers / total_questions), 2) * 100 AS percentage ,date(created_at) as dated
                  FROM results";
        $result = $this->conn->query($query);
        $details = array();
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
        $response['details'] = $details;

        // Data for above 50% and below or equal 50%
        $queryAbove50 = "SELECT fullname, 
                                ROUND((correct_answers / total_questions), 2) * 100 AS percentage 
                         FROM results 
                         WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
        $resultAbove50 = $this->conn->query($queryAbove50);
        $above50 = array();
        while ($row = $resultAbove50->fetch_assoc()) {
            $above50[] = [
                'fullname' => $row['fullname'],
                'percentage' => (float)$row['percentage']
            ];
        }
        $response['above50'] = $above50;

        $queryBelowOrEqual50 = "SELECT fullname, 
                                    ROUND((correct_answers / total_questions), 2) * 100 AS percentage 
                             FROM results 
                             WHERE ROUND((correct_answers / total_questions), 2) * 100 <= 50";
        $resultBelowOrEqual50 = $this->conn->query($queryBelowOrEqual50);
        $belowOrEqual50 = array();
        while ($row = $resultBelowOrEqual50->fetch_assoc()) {
            $belowOrEqual50[] = [
                'fullname' => $row['fullname'],
                'percentage' => (float)$row['percentage']
            ];
        }
        $response['belowOrEqual50'] = $belowOrEqual50;

        // Data for above 50% and below or equal 50%
        $queryAbove50 = "SELECT fullname, 
                                ROUND((correct_answers / total_questions), 2) * 100 AS percentage 
                         FROM results 
                         WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
        $resultAbove50 = $this->conn->query($queryAbove50);
        $above50 = array();
        while ($row = $resultAbove50->fetch_assoc()) {
            $above50[] = [
                'fullname' => $row['fullname'],
                'percentage' => (float)$row['percentage']
            ];
        }
        $response['above50'] = $above50;



        // Data for above 50% and below or equal 50%
        $queryAbove50 = "SELECT fullname, 
         ROUND((correct_answers / total_questions), 2) * 100 AS percentage 
            FROM results 
            WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
        $resultAbove50 = $this->conn->query($queryAbove50);
        $above50 = array();
        while ($row = $resultAbove50->fetch_assoc()) {
            $above50[] = [
                'fullname' => $row['fullname'],
                'percentage' => (float)$row['percentage']
            ];
        }
        $response['above50'] = $above50;

        // Data for above 50% and below or equal 50% with counts
        $queryAbove50 = "SELECT COUNT(*) AS count, 
          ROUND((SUM(correct_answers) / SUM(total_questions)), 2) * 100 AS percentage 
   FROM results 
   WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
        $resultAbove50 = $this->conn->query($queryAbove50);
        $rowAbove50 = $resultAbove50->fetch_assoc();
        $response['above50'] = [
            'count' => (int)$rowAbove50['count'],
            'percentage' => (float)$rowAbove50['percentage']
        ];

        $queryBelowOrEqual50 = "SELECT COUNT(*) AS count, 
                 ROUND((SUM(correct_answers) / SUM(total_questions)), 2) * 100 AS percentage 
         FROM results 
         WHERE ROUND((correct_answers / total_questions), 2) * 100 <= 50";
        $resultBelowOrEqual50 = $this->conn->query($queryBelowOrEqual50);
        $rowBelowOrEqual50 = $resultBelowOrEqual50->fetch_assoc();
        $response['belowOrEqual50'] = [
            'count' => (int)$rowBelowOrEqual50['count'],
            'percentage' => (float)$rowBelowOrEqual50['percentage']
        ];

        // Data for names in each group
        $queryAbove50Names = "SELECT fullname 
        FROM results 
        WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
        $resultAbove50Names = $this->conn->query($queryAbove50Names);
        $above50Names = array();
        while ($row = $resultAbove50Names->fetch_assoc()) {
            $above50Names[] = $row['fullname'];
        }
        $response['above50Names'] = $above50Names;

        $queryBelowOrEqual50Names = "SELECT fullname 
               FROM results 
               WHERE ROUND((correct_answers / total_questions), 2) * 100 <= 50";
        $resultBelowOrEqual50Names = $this->conn->query($queryBelowOrEqual50Names);
        $belowOrEqual50Names = array();
        while ($row = $resultBelowOrEqual50Names->fetch_assoc()) {
            $belowOrEqual50Names[] = $row['fullname'];
        }
        $response['belowOrEqual50Names'] = $belowOrEqual50Names;

        return json_encode($response);
    }
}
