<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != '1') {
    header("Location: ../home"); // Redirect to index.php if not logged in or not an admin
    exit();
}

$category = isset($_GET['category']) ? $_GET['category'] : 'above50';

// Connect to database
include '../inc/db.php';

// Fetch the relevant data
if ($category === 'above50') {
    $query = "SELECT fullname, ROUND((correct_answers / total_questions), 2) * 100 AS percentage, created_at
              FROM results 
              WHERE ROUND((correct_answers / total_questions), 2) * 100 > 50";
} else {
    $query = "SELECT fullname, ROUND((correct_answers / total_questions), 2) * 100 AS percentage, created_at
              FROM results 
              WHERE ROUND((correct_answers / total_questions), 2) * 100 <= 50";
}
$result = $conn->query($query);
$details = array();
while ($row = $result->fetch_assoc()) {
    $details[] = $row;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคะแนน</title>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
<?php require_once '../inc/templete/navbar.php'; ?>
    <br><br>
    <div class="container mt-5">
        <h3 class="text-primary">รายละเอียดคะแนน <?php echo $category === 'above50' ? '> 50%' : '≤ 50%'; ?></h3>
        <div class="row">
            <div class="col-md-12">
                <div id="areaChart" class="my-4"></div>
            </div>
        </div>

        <h4>รายละเอียดผู้ใช้</h4>
        <table id="detailsTable" class="display">
            <thead>
                <tr>
                    <th>ผู้ใช้</th>
                    <th>เปอร์เซ็นต์</th>
                    <th>วันที่</th>
                    <th>ดู</th>
                </tr>
            </thead>
        </table>
    </div>
    <br><br><br><br>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.min.js"></script>
    <style>
         .custom-swal-width {
        width: 600px; /* Adjust the width as needed */
        max-width: none;
    }
    </style>
    <script>
        $(document).ready(function() {
            var details = <?php echo json_encode($details); ?>;

            // Initialize DataTable
            $('#detailsTable').DataTable({
                data: details,
                columns: [{
                        data: 'fullname'
                    },
                    {
                        data: 'percentage'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                        <a href="dataone.php?name=${encodeURIComponent(row.fullname)}&percentage=${row.percentage}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-primary" onclick="showDetails('${row.fullname}', ${row.percentage}, '${row.created_at}')">
                            <i class="bi bi-eye"></i>
                        </a>
                    `;
                        }
                    }
                ]
            });

            // Function to show SweetAlert with details and a pie chart
            window.showDetails = function(name, percentage, date) {
                Swal.fire({
                    title: 'รายละเอียด',
                    html: `
                    <div class="row">
                    <div class="col-6">
                        <div id="pieChart" style="width: 100%; height: 220px;"></div>
                    </div>
                    <div class="col-6">
                        <div class="card-body mt-3">
                        <p class="text-left ">ชื่อผู้ใช้: ${name}</p>
                        <p class="text-left" >เปอร์เซ็นต์ถูกต้อง: ${percentage}%</p>
                        <p class="text-left" >เปอร์เซ็นต์ผิด: ${100 - percentage}%</p>
                        <p class="text-left" >วันที่: ${date}</p>
                    </div>
                    </div>
                                    
                </div>
            `,
                   
                    confirmButtonText: 'ปิด',
                    customClass: {
                        popup: 'custom-swal-width'
                    },
                    didOpen: () => {
                        // Create Pie Chart inside SweetAlert
                        var optionsPieChart = {
                            chart: {
                                type: 'pie',
                                height: '250px'
                            },
                            series: [percentage, 100 - percentage],
                            labels: ['ตอบถูก', 'ตอบผิด'],
                            colors: ['#6495ED', '#F44336'],
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + "%";
                                    }
                                }
                            }
                        };
                        var chartPieChart = new ApexCharts(document.querySelector("#pieChart"), optionsPieChart);
                        chartPieChart.render();
                    }
                });
            };

            // Create Area Chart
            var optionsAreaChart = {
                chart: {
                    type: 'area',
                    height: 350
                },
                series: [{
                    name: 'เปอร์เซ็นต์',
                    data: details.map(d => d.percentage)
                }],
                xaxis: {
                    categories: details.map(d => d.fullname),
                    title: {
                        text: 'ผู้ใช้'
                    }
                },
                yaxis: {
                    title: {
                        text: 'เปอร์เซ็นต์'
                    }
                },
                title: {
                    text: 'เปอร์เซ็นต์คะแนนตามผู้ใช้'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                }
            };

            var chartAreaChart = new ApexCharts(document.querySelector("#areaChart"), optionsAreaChart);
            chartAreaChart.render();
        });
    </script>
</body>

</html>