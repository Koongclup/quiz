
<?php
session_start();

// Set the timeout period in seconds
$timeout_duration = 1800; // 30 minutes

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != '1') {
    header("Location: home"); // Redirect to login page if not logged in or not an admin
    exit();
}

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session timed out
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session
    header("Location: home"); // Redirect to login page
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
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>

<body>
    
<?php require_once '../inc/templete/navbar.php'; ?>
    <!-- User Management -->
    <div class="container mt-5 pt-5">
        <h3 class="text-primary">User Management</h3>
    <br>
        
        <!-- Card Points -->
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text" id="totalUsers">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Quiz Takers</h5>
                        <p class="card-text" id="quizTakers">0</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Question</h5>
                        <p class="card-text" id="quiz">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Button -->
        <div class="text-start mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" id="createUserBtn">
                <i class="bi bi-person-plus"></i> Create User
            </button>
        </div>

        <!-- DataTable for Users -->
        <table id="userTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here by AJAX -->
            </tbody>
        </table>

        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Create User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            <input type="hidden" id="userId" name="id">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userName" name="username" placeholder="Username" required>
                                <label for="userName">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="userPassword" name="password" placeholder="Password" required>
                                <label for="userPassword">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="userRole" name="role_id" required>
                                    <option value="" selected>Select Role</option>
                                    <option value="1">Admin</option>
                                    <option value="2">User</option>
                                </select>
                                <label for="userRole">Role</label>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="saveUser">
                                    <i class="bi bi-save"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 and Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
           toastr.options = {
        "closeButton": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right mt-5",
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

    $(document).ready(function () {
        // Load User Counts
        $.ajax({
            url: '../action/users.php?action=getUserCounts',
            method: 'GET',
            success: function (data) {
                var counts = JSON.parse(data);
                $('#totalUsers').text(counts.total_users);
                $('#quizTakers').text(counts.quiz_takers);
                $('#quiz').text(counts.quiz);
            }
        });

        // Load Users into DataTable
        var table = $('#userTable').DataTable({
            ajax: {
                url: '../action/users.php?action=getUsers',
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'username' },
                { data: 'role_id' },
                {
                    data: null,
                    className: 'text-center',
                    defaultContent: `
                        <button class="btn btn-info btn-sm edit-btn"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn"><i class="bi bi-trash"></i> Delete</button>
                    `
                }
            ]
        });

        // Show Modal for Editing
        $('#userTable').on('click', '.edit-btn', function () {
            var data = table.row($(this).parents('tr')).data();
            $('#userId').val(data.id);
            $('#userName').val(data.username);
            $('#userRole').val(data.role_id);
            $('#userModalLabel').text('Edit User');
            $('#userModal').modal('show');
        });

        // Show Modal for Creating User
        $('#createUserBtn').click(function () {
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#userModalLabel').text('Create User');
            $('#userModal').modal('show');
        });

        // Save User
        $('#userForm').submit(function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var url = $('#userId').val() ? '../action/users.php?action=updateUser' : '../action/users.php?action=createUser';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        toastr.success('User saved successfully');
                        $('#userModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        toastr.error(result.message);
                    }
                }
            });
        });

        // Delete User
        $('#userTable').on('click', '.delete-btn', function () {
            var data = table.row($(this).parents('tr')).data();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../action/users.php?action=deleteUser',
                        method: 'POST',
                        data: { id: data.id },
                        success: function (response) {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                toastr.success('User deleted successfully');
                                table.ajax.reload();
                            } else {
                                toastr.error(result.message);
                            }
                        }
                    });
                }
            });
        });
    });
    </script>
</body>

</html>
