<?php  
require 'connectDB.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
$d = date("Y-m-d");
$t = date("H:i:s");

// Xử lý đầu vào
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_GET['card_uid'], $_GET['device_token'])) {
    $card_uid = sanitize_input($_GET['card_uid']);
    $device_uid = sanitize_input($_GET['device_token']);

    // Kiểm tra thông tin thiết bị
    $device_info = get_device_info($conn, $device_uid);
    if ($device_info) {
        $device_mode = $device_info['device_mode'];
        $device_dep = $device_info['device_dep'];

        if ($device_mode == 1) {
            // Chế độ đăng nhập / đăng xuất
            $good_info = get_good_info($conn, $card_uid);
            if ($good_info) {
                handle_good_login_logout($conn, $good_info, $card_uid, $device_uid, $device_dep, $d, $t);
            } else {
                echo "Card not found!";
            }
        } elseif ($device_mode == 0) {
            // Chế độ đăng ký thẻ mới
            $good_info = get_good_info($conn, $card_uid);
            if ($good_info) {
                handle_existing_card($conn, $card_uid);
            } else {
                handle_new_card($conn, $card_uid, $device_uid, $device_dep);
            }
        }
    } else {
        echo "Invalid Device!";
    }
} else {
    echo "Invalid Input!";
}

// Lấy thông tin thiết bị
function get_device_info($conn, $device_uid) {
    $sql = "SELECT * FROM devices WHERE device_uid = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $device_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    } else {
        error_log("SQL Error: Failed to fetch device info");
        return false;
    }
}

// Lấy thông tin mặt hàng
function get_good_info($conn, $card_uid) {
    $sql = "SELECT * FROM goods WHERE card_uid = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $card_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    } else {
        error_log("SQL Error: Failed to fetch good info");
        return false;
    }
}

// Xử lý đăng nhập / đăng xuất
function handle_good_login_logout($conn, $good_info, $card_uid, $device_uid, $device_dep, $d, $t) {
    if ($good_info['add_card'] == 1) {
        if ($good_info['device_uid'] == $device_uid || $good_info['device_uid'] == 0) {
            $Gname = $good_info['good'];
            $Number = $good_info['serialnumber'];
            insert_good_log($conn, $Gname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, "00:00:00");
            echo "Login successful for $Gname (Serial Number: $Number)";
        } else {
            echo "Access Denied!";
        }
    } else {
        echo "Card not registered!";
    }
}

// Thêm bản ghi đăng nhập
function insert_good_log($conn, $Gname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, $timeout) {
    $sql = "INSERT INTO goods_logs (good, serialnumber, card_uid, device_uid, device_dep, checkindate, timein, timeout) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdssssss", $Gname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, $timeout);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("SQL Error: Failed to insert log for card UID $card_uid");
        }
    } else {
        error_log("SQL Error: Failed to prepare INSERT statement");
    }
}

// Xử lý thẻ đã tồn tại
function handle_existing_card($conn, $card_uid) {
    $sql = "UPDATE goods SET card_select = 1 WHERE card_uid = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $card_uid);
        if (mysqli_stmt_execute($stmt)) {
            echo "Card updated to selected state!";
        } else {
            error_log("SQL Error: Failed to update existing card UID $card_uid");
        }
    } else {
        error_log("SQL Error: Failed to prepare update for existing card");
    }
}

// Xử lý thẻ mới
function handle_new_card($conn, $card_uid, $device_uid, $device_dep) {
    $sql = "INSERT INTO goods (card_uid, card_select, device_uid, device_dep, good_date) 
            VALUES (?, 1, ?, ?, CURDATE())";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $card_uid, $device_uid, $device_dep);
        if (mysqli_stmt_execute($stmt)) {
            echo "New card successfully registered!";
        } else {
            error_log("SQL Error: Failed to register new card UID $card_uid");
        }
    } else {
        error_log("SQL Error: Failed to prepare insert for new card");
    }
}
?>
