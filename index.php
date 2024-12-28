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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Thêm Font Awesome -->
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
  <h3>Status</h3>
  <button onclick="loadGoodsSummary()">Refresh</button>
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
  <h3>Registered Goods</h3>
</div>
  <div class="search-section">
    <form method="GET" action="">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" placeholder="Enter search keyword">
        
        <label for="filter">Filter by:</label>
        <select id="filter" name="filter">
            <option value="serialnumber">Serial Number</option>
            <option value="device_dep">Device</option>
        </select>
        
        <label for="fragile_status">Fragile Status:</label>
        <select id="fragile_status" name="fragile_status">
            <option value="">Any</option>
            <option value="yes">Fragile</option>
            <option value="no">Not Fragile</option>
        </select>
        
        <label for="sort">Sort by Date:</label>
        <select id="sort" name="sort">
            <option value="asc">Descending</option>
            <option value="desc">Ascending</option>
        </select>
        
        <button type="submit">Search</button>
    </form>
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
      
        // Lấy dữ liệu từ form
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc'; // Mặc định là giảm dần
        $fragile_status = isset($_GET['fragile_status']) ? $_GET['fragile_status'] : ''; // Get fragile status filter

        // Start building the SQL query
        $sql = "SELECT * FROM goods WHERE add_card=1"; 
        $bind_params = []; // Array to hold parameters for binding
        $types = ''; // String to define the types of parameters (e.g., "s" for string)

        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search) && !empty($filter)) {
            $sql .= " AND $filter LIKE ?"; 
            $bind_params[] = "%$search%";  // Add search term to bind parameters
            $types .= 's'; // Add 's' to parameter type for string
        }

        // Add fragile status filter if selected
        if ($fragile_status !== '') {
            $sql .= " AND fragile = ?";
            $bind_params[] = $fragile_status;  // Add fragile_status to bind parameters
            $types .= 's'; // Add 's' to parameter type for string
        }

        // Add sorting condition
        $sql .= " ORDER BY good_date $sort";
        
        // Prepare statement
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo '<p class="error">SQL Error</p>';
        } else {
            // Bind parameters if needed
            if (!empty($bind_params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$bind_params);  // Bind all parameters dynamically
            }

            // Execute query
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if rows are returned
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; echo " | "; echo $row['good']; ?></td>
                        <td><?php echo $row['serialnumber']; ?></td>
                        <td><?php echo $row['fragile']; ?></td>
                        <td><?php echo $row['card_uid']; ?></td>
                        <td><?php echo $row['good_date']; ?></td>
                        <td><?php echo $row['device_dep']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6'>No results found</td></tr>";
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
