<?php
session_start();

// Set the timeout period in seconds
$timeout_duration = 1800; // 30 minutes

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != '1') {
    header("Location: ../home"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session timed out
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session
    header("Location: ../home"); // Redirect to login page
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดข้อสอบ</title>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
<?php require_once '../inc/templete/navbar.php'; ?>
    <br><br>
    <div class="container mt-5">
        <h3 class="text-primary">Dashboard</h3>
        <div class="row">
            <div class="col-md-6">
                <div id="averageScoreChart" class="my-4"></div>
            </div>

            <div class="col-md-6">
                <div id="scoreDistributionChart" class="my-4"></div>
            </div>
            <div class="col-md-12">
                <div id="questionsChart" class="my-4"></div>
            </div>
        </div>

        <h4>รายละเอียดข้อคำตอบ</h4>
        <table id="questionsTable" class="display">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ผู้ใช้</th>
                    <th>คะแนนที่ถูกต้อง</th>
                    <th>จำนวนคำถามทั้งหมด</th>
                    <th>เปอร์เซ็นต์</th>
                    <th>#</th>
                </tr>
            </thead>
        </table>
    </div>
    <br><br><br><br>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.1/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Fetch data for the dashboard
            $.ajax({
                url: '../class/Dashboard.php?action=fetchSummary',
                method: 'GET',
                success: function(response) {
                    try {
                        var data = JSON.parse(response);

                        // Create Average Score Chart
                        var optionsAverageScore = {
                            chart: {
                                type: 'pie',
                                width: '100%',
                                height: '300px',
                                events: {
                                    dataPointSelection: function(event, chartContext, config) {
                                        var index = config.dataPointIndex;
                                        var selectedItem = data.average[index];
                                        Swal.fire({
                                            title: 'รายละเอียด',
                                            html: `<p>ชื่อ: ${selectedItem.name}</p>
                                                   <p>คะแนน: ${selectedItem.y}</p>`,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonText: 'ดูรายละเอียด',
                                            cancelButtonText: 'ปิด'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = `show.php?name=${encodeURIComponent(selectedItem.name)}&percentage=${selectedItem.y}`;
                                            }
                                        });
                                    }
                                }
                            },
                            series: data.average.map(item => item.y),
                            labels: data.average.map(item => item.name),
                            title: {
                                text: 'ค่าเฉลี่ยการตอบถูกและตอบผิด'
                            }
                        };
                        var chartAverageScore = new ApexCharts(document.querySelector("#averageScoreChart"), optionsAverageScore);
                        chartAverageScore.render();

                        // Create Overall Score Chart
                        var optionsOverallScore = {
                            chart: {
                                type: 'pie',
                                width: '100%',
                                height: '300px',
                                events: {
                                    dataPointSelection: function(event, chartContext, config) {
                                        var index = config.dataPointIndex;
                                        var selectedItem = data.overall[index];
                                        Swal.fire({
                                            title: 'รายละเอียดคะแนนภาพรวม',
                                            html: `<p>ชื่อ: ${selectedItem.name}</p>
                                                   <p>คะแนน: ${selectedItem.y}</p>`,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonText: 'ดูรายละเอียด',
                                            cancelButtonText: 'ปิด'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = `show.php?name=${encodeURIComponent(selectedItem.name)}&percentage=${selectedItem.y}`;
                                            }
                                        });
                                    }
                                }
                            },
                            series: data.overall.map(item => item.y),
                            labels: data.overall.map(item => item.name),
                            title: {
                                text: 'คะแนนภาพรวม'
                            },
                            plotOptions: {
                                pie: {
                                    dataLabels: {
                                        style: {
                                            fontSize: '18px'
                                        }
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                fontSize: '18px'
                            }
                        };
                        var chartOverallScore = new ApexCharts(document.querySelector("#overallScoreChart"), optionsOverallScore);
                        chartOverallScore.render();

                        // Create Questions Chart
                        var optionsQuestionsChart = {
                            chart: {
                                type: 'bar',
                                height: 350,
                                events: {
                                    dataPointSelection: function(event, chartContext, config) {
                                        var index = config.dataPointIndex;
                                        var selectedItem = data.questions[index];
                                        Swal.fire({
                                            title: 'รายละเอียดคำตอบ',
                                            html: `<p>ชื่อผู้ใช้: ${selectedItem.fullname}</p>
                                                   <p>เปอร์เซ็นต์: ${selectedItem.percentage}%</p>`,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonText: 'ดูรายละเอียด',
                                            cancelButtonText: 'ปิด'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = `show.php?name=${encodeURIComponent(selectedItem.fullname)}&percentage=${selectedItem.percentage}`;
                                            }
                                        });
                                    }
                                }
                            },
                            series: [{
                                name: 'เปอร์เซ็นต์',
                                data: data.questions.map(q => q.percentage)
                            }],
                            xaxis: {
                                categories: data.questions.map(q => q.fullname),
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
                                text: 'คะแนนตามคำตอบ'
                            }
                        };
                        var chartQuestionsChart = new ApexCharts(document.querySelector("#questionsChart"), optionsQuestionsChart);
                        chartQuestionsChart.render();


                        // Chart score and event click to list.php 
                        var optionsScoreDistribution = {
                            chart: {
                                type: 'pie',
                                width: '100%',
                                height: '300px',
                                events: {
                                    dataPointSelection: function(event, chartContext, config) {
                                        var index = config.dataPointIndex;
                                        var category = index === 0 ? 'above50' : 'belowOrEqual50';
                                        var selectedItem = index === 0 ? data.above50 : data.belowOrEqual50;
                                        var names = index === 0 ? data.above50Names : data.belowOrEqual50Names;

                                        // Create a table for DataTables
                                        /* var tableHtml = `
                        <table  class="table table-sm table-striped " style="width:100%">
                            <thead>
                                <tr>
                                    <th>ชื่อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${names.map(name => `<tr><td>${name}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    `; */

                                        Swal.fire({
                                            title: index === 0 ? 'รายละเอียดคะแนน > 50%' : 'รายละเอียดคะแนน ≤ 50%',
                                            html: `<p>จำนวนคน: ${selectedItem.count}</p>
                             
                                 `,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonText: 'ดูรายละเอียด',
                                            cancelButtonText: 'ปิด',
                                            reverseButtons: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = `list.php?category=${category}`;
                                            }
                                        }).then(() => {
                                            // Initialize DataTables after the modal has been shown
                                            $('#namesTable').DataTable();
                                        });
                                    }
                                }
                            },
                            series: [
                                data.above50.count,
                                data.belowOrEqual50.count
                            ],
                            labels: [
                                '> 50%',
                                '≤ 50%'
                            ],
                            title: {
                                text: 'คะแนนโดยรวม (> 50% และ ≤ 50%)'
                            }
                        };

                        var chartScoreDistribution = new ApexCharts(document.querySelector("#scoreDistributionChart"), optionsScoreDistribution);
                        chartScoreDistribution.render();


                        // Initialize DataTable
                        $('#questionsTable').DataTable({
                            data: data.details,
                            columns: [{
                                    data: 'id'
                                },
                                {
                                    data: 'fullname'
                                },
                                {
                                    data: 'correct_answers'
                                },
                                {
                                    data: 'total_questions'
                                },
                                {
                                    data: 'percentage',
                                    render: (data, type, row) => `<b>${row.percentage}%</b>`
                                },
                                {
                                    data: null,
                                     render: (data, type, row) => `
                                    <a class="btn btn-sm btn-outline-info" href="dataone.php?name=${encodeURIComponent(row.fullname)}">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" onclick="showDetails('${row.fullname}', ${row.percentage}, '${row.dated}')">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                `
                                }
                            ]
                        });

                        window.showDetails = (name, percentage, dated) => {
                            Swal.fire({
                                title: 'รายละเอียด '+name+' ',
                                html: `
                                <div id="pieChart" style="width: 100%; height: 250px;"></div>
                                    <p>ชื่อผู้ใช้: ${name}</p>
                                    <p>เปอร์เซ็นต์ถูกต้อง: ${percentage}%</p>
                                    <p>เปอร์เซ็นต์ผิด: ${100 - percentage}%</p>
                                    <p>วันที่: ${dated}</p>       
                                `,
                                showCloseButton: true,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-width'
                                },
                                didOpen: () => {
                                    // Create Pie Chart inside SweetAlert
                                    const optionsPieChart = {
                                        chart: {
                                            type: 'pie',
                                            height: '250px'
                                        },
                                        series: [percentage, 100 - percentage],
                                        labels: ['ถูกต้อง', 'ผิดพลาด'],
                                        colors: ['#4CAF50', '#F44336'],
                                        legend: {
                                            position: 'bottom'
                                        },
                                        tooltip: {
                                            y: {
                                                formatter: (val) => `${val}%`
                                            }
                                        }
                                    };
                                    const chartPieChart = new ApexCharts(document.querySelector("#pieChart"), optionsPieChart);
                                    chartPieChart.render();
                                }
                            });
                        };

                    } catch (e) {
                        console.error('Parsing error:', e);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                }
            });


        });
    </script>
</body>

</html>