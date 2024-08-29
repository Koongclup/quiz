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
    <title>Admin: จัดการข้อสอบ</title>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../inc/templete/navbar.php'; ?>
    <br><br>
    <div class="container mt-5">
        <h3 class="text-primary"> จัดการข้อสอบ</h3>
        <button class="btn btn-success my-3" id="addQuestionBtn"> <i class="bi bi-plus fe-bold"></i> เพิ่มข้อสอบ</button>
        <table class="table table-sm display " id="questionsTable">
            <thead>
                <tr>
                   
                    <th>คำถาม</th>
                    <th>ตัวเลือก A</th>
                    <th>ตัวเลือก B</th>
                    <th>ตัวเลือก C</th>
                    <th>ตัวเลือก D</th>
                    <th>คำตอบที่ถูกต้อง</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>

    <!-- Modal สำหรับเพิ่ม/แก้ไขข้อสอบ -->
    <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"> เพิ่มข้อสอบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="questionForm">
                    <div class="modal-body">
                        <input type="hidden" id="questionId" name="id">
                        <div class="mb-3">
                            <label for="questionText" class="form-label">คำถาม</label>
                            <textarea class="form-control" id="questionText" name="questionText" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="optionA" class="form-label">ตัวเลือก A</label>
                            <input type="text" class="form-control" id="optionA" name="optionA" required>
                        </div>
                        <div class="mb-3">
                            <label for="optionB" class="form-label">ตัวเลือก B</label>
                            <input type="text" class="form-control" id="optionB" name="optionB" required>
                        </div>
                        <div class="mb-3">
                            <label for="optionC" class="form-label">ตัวเลือก C</label>
                            <input type="text" class="form-control" id="optionC" name="optionC" required>
                        </div>
                        <div class="mb-3">
                            <label for="optionD" class="form-label">ตัวเลือก D</label>
                            <input type="text" class="form-control" id="optionD" name="optionD" required>
                        </div>
                        <div class="mb-3">
                            <label for="correctOption" class="form-label">คำตอบที่ถูกต้อง</label>
                            <select class="form-control" id="correctOption" name="correctOption" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
    toastr.options = {
        "closeButton": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right mb-5",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

        $(document).ready(function() {
            var table = $('#questionsTable').DataTable({
                ajax: '../action/process.php?action=fetch',
                responsive: true,
                columns: [
                    {
                        data: 'question_text'
                    },
                    {
                        data: 'option_a'
                    },
                    {
                        data: 'option_b'
                    },
                    {
                        data: 'option_c'
                    },
                    {
                        data: 'option_d'
                    },
                    {
                        data: 'correct_option'
                    },
                    {
                        data: null,
                        className: "center",
                        defaultContent: '<button class="btn btn-sm btn-outline-warning  editBtn"><i class="bi bi-pencil"></i></button> ' +
                            '<button class="btn btn-sm btn-outline-danger deleteBtn"><i class="bi bi-trash"></i></button>'
                    }
                ]
            });

            $('#addQuestionBtn').on('click', function() {
                $('#modalTitle').text('เพิ่มข้อสอบ');
                $('#questionForm')[0].reset();
                $('#questionId').val('');
                $('#questionModal').modal('show');
            });

            $('#questionsTable tbody').on('click', '.editBtn', function() {
                var data = table.row($(this).parents('tr')).data();
                $('#modalTitle').text('แก้ไขข้อสอบ');
                $('#questionId').val(data.id);
                $('#questionText').val(data.question_text);
                $('#optionA').val(data.option_a);
                $('#optionB').val(data.option_b);
                $('#optionC').val(data.option_c);
                $('#optionD').val(data.option_d);
                $('#correctOption').val(data.correct_option);
                $('#questionModal').modal('show');
            });

            $('#questionsTable tbody').on('click', '.deleteBtn', function() {
                var data = table.row($(this).parents('tr')).data();
                swal({
                    title: "คุณแน่ใจหรือไม่?",
                    text: "ข้อสอบนี้จะถูกลบออก",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "ใช่, ลบเลย!",
                    cancelButtonText: "ยกเลิก",
                    closeOnConfirm: false
                }, function() {
                    $.ajax({
                        url: '../action/process.php?action=delete',
                        method: 'POST',
                        data: {
                            id: data.id
                        },
                        success: function(response) {
                            table.ajax.reload();
                            swal("ลบสำเร็จ!", "ข้อสอบถูกลบออกแล้ว.", "success");
                        },
                        error: function() {
                            swal("ข้อผิดพลาด!", "เกิดข้อผิดพลาดในการลบข้อสอบ.", "error");
                        }
                    });
                });
            });

            $('#questionForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var action = $('#questionId').val() ? 'save' : 'save';
                $.ajax({
                    url: 'action/process.php?action=' + action,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#questionModal').modal('hide');
                        table.ajax.reload();
                        toastr.success('บันทึกข้อมูลเรียบร้อย','Success', { timeOut: 2500 });
                    },
                    error: function() {
                        toastr.error('เกิดข้อผิดพลาดในการบันทึก');
                    }
                });
            });
        });
    </script>
</body>

</html>