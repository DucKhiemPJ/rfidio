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
        $device_mode = $device_info['device_mode']; // 1: Offline, 0: Online
        $device_dep = $device_info['device_dep'];

        if ($device_mode == 0) { // Trạng thái Online
            $good_info = get_good_info($conn, $card_uid);
            if ($good_info) {
                // Nếu thẻ đã tồn tại → Kiểm tra đăng nhập hoặc đăng xuất
                handle_good_login_logout($conn, $good_info, $card_uid, $device_uid, $device_dep, $d, $t);
            } else {
                // Nếu thẻ mới → Đăng ký và chỉ thông báo đăng ký thành công
                handle_new_card($conn, $card_uid, $device_uid, $device_dep);
            }
        } elseif ($device_mode == 1) { // Trạng thái Offline
            // Không cho phép đăng xuất, gửi phản hồi về trạng thái offline
            echo json_encode([
                "status" => "offline",
                "message" => "Device is in offline mode. RFID scanner is disabled."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid Device!"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid Input!"
    ]);
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

            // Kiểm tra xem thẻ đã đăng nhập chưa (có bản ghi trong goods_logs với card_out = 0)
            $sql = "SELECT * FROM goods_logs WHERE card_uid = ? AND checkindate = ? AND card_out = 0 LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $card_uid, $d);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);

                if ($row) {
                    // Thẻ đã đăng nhập → Thực hiện đăng xuất
                    update_good_log_logout($conn, $card_uid, $d, $t);
                    echo "Logout successful for $Gname (Serial Number: $Number)";
                } else {
                    // Thẻ chưa đăng nhập → Thực hiện đăng nhập
                    insert_good_log($conn, $Gname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, "00:00:00");
                    echo "Login successful for $Gname (Serial Number: $Number)";
                }
            } else {
                error_log("SQL Error: Failed to check login status");
            }
        } else {
            echo "Access Denied!";
        }
    } else {
        echo "Please register the card first!";
    }
}


// Cập nhật bản ghi đăng xuất và gán checkoutdate là ngày có timeout
function update_good_log_logout($conn, $card_uid, $d, $t) {
    // Thực hiện cập nhật thời gian timeout và checkoutdate
    $sql = "UPDATE goods_logs 
            SET timeout = ?, checkoutdate = ?, card_out = 1 
            WHERE card_uid = ? AND checkindate = ? AND card_out = 0 LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Liên kết tham số với câu lệnh SQL
        mysqli_stmt_bind_param($stmt, "ssss", $t, $d, $card_uid, $d);
        
        // Thực thi câu lệnh SQL
        if (!mysqli_stmt_execute($stmt)) {
            error_log("SQL Error: Failed to update logout log for card UID $card_uid");
        }
    } else {
        error_log("SQL Error: Failed to prepare update for logout");
    }
}


// Thêm bản ghi đăng nhập
function insert_good_log($conn, $Gname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, $timeout) {
    $sql = "INSERT INTO goods_logs (good, serialnumber, card_uid, device_uid, device_dep, checkindate, timein, timeout, card_out) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
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
