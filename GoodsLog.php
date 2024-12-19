<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}

// Kết nối cơ sở dữ liệu
require 'connectDB.php';

// Câu lệnh SQL để đếm số lượng hàng hóa trong bảng goods_logs
$sql_count = "SELECT COUNT(DISTINCT good) AS total_goods FROM goods_logs WHERE card_out = 1";  // Chỉ đếm các mặt hàng không bị trùng
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_goods = $row_count['total_goods'];

// Câu lệnh SQL để đếm số lượng hàng hóa trùng lặp theo tên, nếu không trùng thì hiển thị 1
$sql_duplicates = "SELECT good, 
                          IF(COUNT(*) > 1, COUNT(*), 1) AS count_duplicates 
                   FROM goods_logs 
                   WHERE card_out = 1 
                   GROUP BY good";
$result_duplicates = mysqli_query($conn, $sql_duplicates);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Goods Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/goodslog.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>   
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/good_log.js"></script>
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
    <script>
    $(document).ready(function () {
      let intervalID;

      // Hàm gửi yêu cầu AJAX để cập nhật bảng user_log
      function fetchGoodLogs(selectDate) {
        $.ajax({
          url: "good_log_up.php",
          type: 'POST',
          data: { 'select_date': selectDate },
          success: function (data) {
            $('#goodslog').html(data);
          },
          error: function () {
            console.error("Failed to fetch good logs.");
          },
        });
      }

      // Cập nhật bảng user_log ngay khi trang được tải
      fetchGoodLogs(1);

      // Hàm cập nhật bảng user_log nếu Live Update bật
      function updateTable() {
        if ($('#liveToggle').is(':checked')) {
          fetchGoodLogs(0);
        }
      }

      // Thiết lập cập nhật tự động mỗi 5 giây nếu Live Update bật
      intervalID = setInterval(updateTable, 1000);

      // Bắt sự kiện thay đổi trạng thái của nút Live (toggle)
      $('#liveToggle').on('change', function () {
        if ($(this).is(':checked')) {
          // Bật Live Update
          intervalID = setInterval(updateTable, 1000);
        } else {
          // Tắt Live Update
          clearInterval(intervalID);
        }
      });
    });
    </script>
</head>
<body>
<?php include 'header.php'; ?> 
<section class="container py-lg-5">
  <!--User table-->
  <h1 class="slideInDown animated">Here are the Goods daily logs</h1>
  <p>Total Number of Goods: <?php echo $total_goods; ?></p> <!-- Hiển thị tổng số hàng hóa -->
  
  <!-- Hiển thị số lượng hàng hóa trùng lặp -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Product Name</th>
        <th>Count of Product</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result_duplicates)) { ?>
        <tr>
          <td><?php echo $row['good']; ?></td>
          <td><?php echo $row['count_duplicates']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  
  <div class="form-style-5">
    <button type="button" data-toggle="modal" data-target="#Filter-export">Log Filter/ Export to Excel</button>
    <input type="checkbox" id="liveToggle" checked> Live Update
  </div>
  
  <!-- Log filter -->
  <div class="modal fade bd-example-modal-lg" id="Filter-export" tabindex="-1" role="dialog" aria-labelledby="Filter/Export" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg animate" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLongTitle">Filter Your Goods Log:</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" action="Export_Excel.php" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-6 col-sm-6">
                  <div class="panel panel-primary">
                    <div class="panel-heading">Filter By Date:</div>
                    <div class="panel-body">
                      <label for="Start-Date"><b>Select from this Date:</b></label>
                      <input type="date" name="date_sel_start" id="date_sel_start">
                      <label for="End -Date"><b>To End of this Date:</b></label>
                      <input type="date" name="date_sel_end" id="date_sel_end">
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                  <div class="panel panel-primary">
                    <div class="panel-heading">Filter By:</div>
                    <div class="panel-body">
                      <label for="Start-Time"><b>Select from this Time:</b></label>
                      <input type="time" name="time_sel_start" id="time_sel_start">
                      <label for="End -Time"><b>To End of this Time:</b></label>
                      <input type="time" name="time_sel_end" id="time_sel_end">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success">Filter</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- //Log filter -->
  <div class="slideInRight animated">
    <div id="goodslog"></div>
  </div>
</section>
</body>
</html>
