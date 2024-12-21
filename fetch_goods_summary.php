<?php
// Kết nối CSDL
require 'connectDB.php';

// Truy vấn tổng hợp số liệu hàng hóa
$summarySql = "
    SELECT 
        good AS item,
        SUM(CASE WHEN timein IS NOT NULL OR timein != '' OR timein != '00:00:00' THEN 1 ELSE 0 END) AS import,
        SUM(CASE WHEN timeout IS NOT NULL AND timeout != '' AND timeout != '00:00:00' THEN 1 ELSE 0 END) AS export,
        SUM(CASE WHEN timein IS NOT NULL OR timein != '' OR timein != '00:00:00' THEN 1 ELSE 0 END) - SUM(CASE WHEN timeout IS NOT NULL AND timeout != '' AND timeout != '00:00:00' THEN 1 ELSE 0 END) AS remaining
    FROM goods_logs
    GROUP BY good
";
$summaryResult = mysqli_query($conn, $summarySql);

if ($summaryResult) {
    while ($row = mysqli_fetch_assoc($summaryResult)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['item']) . '</td>';
        echo '<td>' . htmlspecialchars($row['import']) . '</td>';
        echo '<td>' . htmlspecialchars($row['export']) . '</td>';
        echo '<td>' . htmlspecialchars($row['remaining']) . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No data found</td></tr>';
}
?>
