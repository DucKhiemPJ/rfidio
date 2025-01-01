<?php
require 'connectDB.php';

// Đặt múi giờ thành Việt Nam (GMT+7)
date_default_timezone_set("Asia/Ho_Chi_Minh");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_id'])) {
    $log_id = intval($_POST['log_id']);
    $timeout = date("H:i:s");

    // Cập nhật trạng thái 'check out' trong bảng goods_logs
    $update_logs_sql = "UPDATE goods_logs 
                        SET timeout=?, card_out=1, status='check out' 
                        WHERE id=? AND card_out=0 AND status='check in'";
    $stmt_logs = mysqli_prepare($conn, $update_logs_sql);

    if ($stmt_logs) {
        mysqli_stmt_bind_param($stmt_logs, "si", $timeout, $log_id);

        if (mysqli_stmt_execute($stmt_logs)) {
            // Đảm bảo việc đóng statement logs trước khi thực hiện câu lệnh khác
            mysqli_stmt_close($stmt_logs);

            // Bước 2: Lấy card_uid từ goods_logs sau khi cập nhật
            $card_uid_sql = "SELECT card_uid FROM goods_logs WHERE id=?";
            $stmt_card_uid = mysqli_prepare($conn, $card_uid_sql);

            if ($stmt_card_uid) {
                mysqli_stmt_bind_param($stmt_card_uid, "i", $log_id);
                mysqli_stmt_execute($stmt_card_uid);
                mysqli_stmt_bind_result($stmt_card_uid, $card_uid);

                if (mysqli_stmt_fetch($stmt_card_uid)) {
                    // Đảm bảo việc đóng statement card_uid trước khi thực hiện câu lệnh khác
                    mysqli_stmt_close($stmt_card_uid);

                    // Bước 3: Cập nhật trạng thái trong bảng goods thành 'sold out' thông qua card_uid
                    $update_goods_sql = "UPDATE goods SET status='sold out' WHERE card_uid=?";
                    $stmt_goods = mysqli_prepare($conn, $update_goods_sql);

                    if ($stmt_goods) {
                        mysqli_stmt_bind_param($stmt_goods, "s", $card_uid);

                        if (mysqli_stmt_execute($stmt_goods)) {
                            echo "Goods status updated to 'sold out' for card_uid: $card_uid";
                        } else {
                            echo "Failed to update goods status for card_uid: $card_uid";
                        }
                        // Đảm bảo đóng statement goods sau khi thực hiện
                        mysqli_stmt_close($stmt_goods);
                    } else {
                        echo "Error preparing update query for goods status.";
                    }
                } else {
                    echo "Failed to fetch card_uid from goods_logs for log ID: $log_id";
                }
            } else {
                echo "Error fetching card_uid from goods_logs.";
            }
        } else {
            echo "Failed to update goods_logs for log ID: $log_id";
        }
    } else {
        echo "SQL Error while preparing goods_logs update query.";
    }
} else {
    echo "Invalid request or log_id not set.";
}
?>
