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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/Goods.css">
    <script>
      $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();

    // Variable to track the sorting state for each column (0: default, 1: ascending, -1: descending)
    var sortState = {
        0: 0, // Goods
        1: 0, // Import
        2: 0, // Export
        3: 0  // Remaining
    };

    // Function to sort a specific column
    function sortTable(columnIndex, isNumeric = false) {
        var table = document.querySelector("table");
        var rows = Array.from(table.rows).slice(1); // Get rows excluding the header
        var sortedRows;

        // Get the current sort state for this column
        var state = sortState[columnIndex];

        // Toggle the sorting state for the column
        if (state === 0) {
            // Default -> Ascending
            sortState[columnIndex] = 1;
        } else if (state === 1) {
            // Ascending -> Descending
            sortState[columnIndex] = -1;
        } else if (state === -1) {
            // Descending -> Default (no sorting)
            sortState[columnIndex] = 0;
            return; // Exit function without sorting if reset to default
        }

        // Perform sorting based on the current state
        if (sortState[columnIndex] === 1) {
            // Ascending Order
            if (isNumeric) {
                sortedRows = rows.sort(function (a, b) {
                    var cellA = parseFloat(a.cells[columnIndex].textContent.trim());
                    var cellB = parseFloat(b.cells[columnIndex].textContent.trim());
                    return cellA - cellB;
                });
            } else {
                sortedRows = rows.sort(function (a, b) {
                    var cellA = a.cells[columnIndex].textContent.trim().toLowerCase();
                    var cellB = b.cells[columnIndex].textContent.trim().toLowerCase();
                    return cellA.localeCompare(cellB);
                });
            }
        } else if (sortState[columnIndex] === -1) {
            // Descending Order
            if (isNumeric) {
                sortedRows = rows.sort(function (a, b) {
                    var cellA = parseFloat(a.cells[columnIndex].textContent.trim());
                    var cellB = parseFloat(b.cells[columnIndex].textContent.trim());
                    return cellB - cellA;
                });
            } else {
                sortedRows = rows.sort(function (a, b) {
                    var cellA = a.cells[columnIndex].textContent.trim().toLowerCase();
                    var cellB = b.cells[columnIndex].textContent.trim().toLowerCase();
                    return cellB.localeCompare(cellA);
                });
            }
        }

        // Reorder the rows in the table
        table.tBodies[0].append(...sortedRows);

        // Update the arrows for the sorted column
        updateArrows(columnIndex);
    }

    // Function to update the arrows for sorting state
    function updateArrows(columnIndex) {
        // Reset all arrows
        $('th').find('i').remove();

        // Add the appropriate arrow icon for the sorted column
        var arrowIcon;
        if (sortState[columnIndex] === 1) {
            arrowIcon = '<i class="fas fa-arrow-up"></i>'; // Ascending
        } else if (sortState[columnIndex] === -1) {
            arrowIcon = '<i class="fas fa-arrow-down"></i>'; // Descending
        }

        // Append the arrow to the sorted column
        $('th').eq(columnIndex).append(arrowIcon);
    }

    // Function to load goods summary on demand (when Refresh button is clicked)
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

    </script>
</head>
<body>
<?php include 'header.php'; ?> 
<main>
<section>
  <h1 class="slideInDown animated">HERE ARE ALL THE GOODS</h1>

  <!-- Tổng hợp số lượng hàng hóa -->
  <div class="summary-section slideInRight animated">
  <div class="summary-section slideInRight animated" style="text-align: center;">
  <h3>STATUS</h3>
  <button onclick="loadGoodsSummary()" class="btn"
  style = "margin-bottom: 10px;  text-decoration: none; box-shadow: 5px 5px 7px rgba(82, 82, 82, 0.3);
  background-color: #46abb9;
  color: #000;
  border: none;
  display: inline-block;
  border-radius: 0px">Refresh</button>
</div>

    
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th onclick="sortTable(0)">Goods</th>
                <th onclick="sortTable(1, true)">Import</th>
                <th onclick="sortTable(2, true)">Export</th>
                <th onclick="sortTable(3, true)">Remaining</th>
            </tr>
        </thead>
        <tbody id="goods-summary" class="table-secondary">
            <!-- Dữ liệu sẽ được nạp qua AJAX -->
        </tbody>
    </table>
  </div>

  <div class="summary-section slideInRight animated">
  <div class="summary-section slideInRight animated" style="text-align: center;">
  <h3>REGISTERED GOODS</h3>
</div>
  <div class="search-section">
    <form method="GET" action="">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" placeholder="Enter search keyword" style = "padding: 4px; margin-bottom: 10px; background-color: #46abb9;
  color: #000; border: none;">
        
        <label for="filter">Filter by:</label>
        <select id="filter" name="filter" style = "padding: 6px; margin-bottom: 10px; background-color: #46abb9;
  color: #000; border: none;">
            <option value="">Any</option>
            <option value="serialnumber">Serial Number</option>
            <option value="device_dep">Device</option>
        </select>
        
        <label for="fragile_status">Fragile Status:</label>
        <select id="fragile_status" name="fragile_status" style = "padding: 6px; margin-bottom: 10px; background-color: #46abb9;
  color: #000; border: none;">
            <option value="">Any</option>
            <option value="yes">Fragile</option>
            <option value="no">Not Fragile</option>
        </select>
        
        <label for="sort">Sort by Date:</label>
        <select id="sort" name="sort" style = "padding: 6px; margin-bottom: 10px; background-color: #46abb9;
  color: #000; border: none; ">
            <option value="asc" style = "padding: 10px">Descending</option>
            <option value="desc" style = "padding: 10px" >Ascending</option>
        </select>
        
        <button type="submit" class = "btn" 
        style = "margin:0 0 2px 8px;  text-decoration: none;  box-shadow: 5px 5px 7px rgba(82, 82, 82, 0.3);
        background-color: #46abb9;
        color: #000;
        border: none;
        border-radius: 0px;
        display: inline-block;">Search</button>
    </form>
</div>


  <!-- Bảng hàng hóa -->
<div class="table-responsive slideInRight animated" style="max-height: 400px;"> 
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>ID | Goods</th>
        <th>Serial Number</th>
        <th>Origin</th>
        <th>Fragile</th>
        <th>Card UID</th>
        <th>Registered_Date</th>
        <th>Exp_Date</th>
        <th>Device</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
    <?php
    require 'connectDB.php';

    // Lấy dữ liệu từ form
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc';
    $fragile_status = isset($_GET['fragile_status']) ? $_GET['fragile_status'] : '';

    // Ánh xạ giá trị từ form sang cơ sở dữ liệu
    if ($fragile_status === 'yes') {
        $fragile_status = 'fragile';
    } elseif ($fragile_status === 'no') {
        $fragile_status = 'not fragile';
    }
    $current_date = date('Y-m-d');

    
    // Bắt đầu truy vấn SQL
    $sql = "SELECT * FROM goods WHERE add_card=1 AND status = 'available'";
    $bind_params = [];
    $types = '';

    // Thêm điều kiện tìm kiếm
    if (!empty($search)) {
        if ($filter === '') { // Nếu filter là "Any", tìm kiếm trên tất cả các cột
            $sql .= " AND (id LIKE ? OR good LIKE ? OR serialnumber LIKE ? OR fragile LIKE ? OR card_uid LIKE ? OR good_date LIKE ? OR device_dep LIKE ? OR origin LIKE ? OR exp_date LIKE ?)";
            $bind_params = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
            $types = 'sssssssss';
        } else { // Nếu filter được chọn, tìm kiếm trên cột cụ thể
            $allowed_filters = ['serialnumber', 'device_dep'];
            if (in_array($filter, $allowed_filters)) {
                $sql .= " AND $filter LIKE ?";
                $bind_params[] = "%$search%";
                $types .= 's';
            } else {
                echo '<p class="error">Invalid filter column.</p>';
                exit;
            }
        }
    }

    // Thêm điều kiện fragile nếu có chọn
    if ($fragile_status !== '') {
        $sql .= " AND fragile = ?";
        $bind_params[] = $fragile_status;
        $types .= 's';
    }

        // Thêm điều kiện sắp xếp
    $sql .= " ORDER BY good_date $sort";

    // Chuẩn bị và thực thi truy vấn
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo '<p class="error">SQL Error</p>';
    } else {
        if (!empty($bind_params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$bind_params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $exp_date = $row['exp_date']; // Ngày tháng đã được lưu dưới dạng YYYY-MM-DD
                $current_date = date('Y-m-d'); // Lấy ngày hiện tại theo định dạng YYYY-MM-DD

                // Kiểm tra ngày hết hạn
                $time_difference = strtotime($exp_date) - strtotime($current_date);

                // Kiểm tra nếu sắp hết hạn trong 3 ngày
                $is_expiring_soon = $time_difference <= 3 * 24 * 60 * 60 && $time_difference >= 0;

                // Bắt đầu dòng
                echo "<tr>";

                // Thêm dấu * nếu sắp hết hạn
                $indicator = $is_expiring_soon ? '*' : '';

                // Các cột dữ liệu
                echo "<td>{$row['id']} | {$row['good']}{$indicator}</td>";
                echo "<td>{$row['serialnumber']}</td>";
                echo "<td>{$row['origin']}</td>";
                echo "<td>{$row['fragile']}</td>";
                echo "<td>{$row['card_uid']}</td>";
                echo "<td>{$row['good_date']}</td>";
                echo "<td>{$row['exp_date']}</td>";
                echo "<td>{$row['device_dep']}</td>";                                                                                                                                                                                                                                                                                                                                                                                                                                                       

                // Kết thúc dòng
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No results found</td></tr>";
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
