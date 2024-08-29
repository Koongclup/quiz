


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เริ่มทำข้อสอบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    @media only screen and (max-width: 600px) {
  .card {
    border: 0px ;
  }
}
</style>

<body>
<?php require_once 'inc/templete/header.php'; ?>
<br>
    <div class="row mx-auto mt-5">
        <div class="col-md-10 mx-auto">
        <!-- ฟอร์มสำหรับกรอกชื่อและเลือกข้อสอบ -->
        <div class="mt-4 p-5 bg-light  rounded ">
        <h2 class="text-center">กรุณากรอกชื่อเพื่อทำข้อสอบ</h2>
        <br>
            <div class="container">
                <form method="post" action="quiz">
                    <div class="mb-3">
                        <label for="userName" class="form-label">ชื่อ - สกุล:</label>
                        <input type="text" id="userName" name="fullname" class="form-control" placeholder="กรุณากรอกชื่อ - สกุล" required>
                    </div>
                    <button type="submit" class="btn btn-primary">เริ่มทำข้อสอบ</button>
                </form>
            </div>
        </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>