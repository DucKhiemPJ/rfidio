<?php
require 'connectDB.php';

// Đặt múi giờ thành Việt Nam (GMT+7)
date_default_timezone_set("Asia/Ho_Chi_Minh");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_id'])) {
    $log_id = intval($_POST['log_id']);
    $timeout = date("H:i:s");

    $sql = "UPDATE goods_logs SET timeout=?, card_out=1 WHERE id=? AND card_out=0";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $timeout, $log_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "Logout successful for log ID: $log_id";
        } else {
            echo "Logout failed for log ID: $log_id";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "SQL Error";
    }
}
?>
