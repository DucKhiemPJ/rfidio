<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Goods</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Goods.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
    </script>
</head>
<body>
<?php include 'header.php'; ?> 
<main>
<section>
  <h1 class="slideInDown animated">HERE ARE ALL THE GOODS</h1>

  <!-- Tổng hợp số lượng hàng hóa -->
  <div class="summary-section slideInRight animated">
    <h3>Summary of Goods</h3>
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th>Goods</th>
                <th>Import</th>
                <th>Export</th>
                <th>Remaining</th>
            </tr>
        </thead>
        <tbody id="goods-summary" class="table-secondary">
            <!-- Dữ liệu sẽ được nạp qua AJAX -->
        </tbody>
    </table>
    <script>
    function loadGoodsSummary() {
        $.ajax({
            url: 'fetch_goods_summary.php', // File xử lý backend
            type: 'GET',
            success: function (data) {
                $('#goods-summary').html(data); // Nạp dữ liệu vào tbody
            },
            error: function () {
                $('#goods-summary').html('<tr><td colspan="4">Error loading data</td></tr>');
            }
        });
    }

    // Gọi hàm loadGoodsSummary ngay khi trang tải xong
    $(document).ready(function () {
        loadGoodsSummary();

        // Cập nhật tự động sau mỗi 5 giây (5000ms)
        setInterval(loadGoodsSummary, 5000);
    });
    </script>
  </div>

  <!-- Bảng hàng hóa -->
  <div class="table-responsive slideInRight animated" style="max-height: 400px;"> 
    <table class="table">
      <thead class="table-primary">
        <tr>
          <th>ID | Goods</th>
          <th>Serial Number</th>
          <th>Fragile</th>
          <th>Card UID</th>
          <th>Date</th>
          <th>Device</th>
        </tr>
      </thead>
      <tbody class="table-secondary">
        <?php
          // Kết nối CSDL
          require 'connectDB.php';

          // Truy vấn hàng hóa
          $sql = "SELECT * FROM goods WHERE add_card=1 ORDER BY id DESC";
          $result = mysqli_stmt_init($conn);
          if (!mysqli_stmt_prepare($result, $sql)) {
              echo '<p class="error">SQL Error</p>';
          } else {
              mysqli_stmt_execute($result);
              $resultl = mysqli_stmt_get_result($result);
              if (mysqli_num_rows($resultl) > 0) {
                  while ($row = mysqli_fetch_assoc($resultl)) {
        ?>
                      <TR>
                      <TD><?php echo $row['id']; echo " | "; echo $row['good'];?></TD>
                      <TD><?php echo $row['serialnumber'];?></TD>
                      <TD><?php echo $row['fragile'];?></TD>
                      <TD><?php echo $row['card_uid'];?></TD>
                      <TD><?php echo $row['good_date'];?></TD>
                      <TD><?php echo $row['device_dep'];?></TD>
                      </TR>
        <?php
                  }
              }
          }
        ?>
      </tbody>
    </table>
  </div>
</section>
</main>
</body>
</html>
