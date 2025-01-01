<?php
// Kết nối đến cơ sở dữ liệu
require 'connectDB.php';

// Thêm hàng hóa
if (isset($_POST['Add'])) {
    $Good_id = $_POST['good_id'];
    $Gname = $_POST['good'];
    $Number = $_POST['number'];
    $Exp_date = $_POST['exp_date'];
    $Origin = $_POST['origin'];
    $dev_uid = $_POST['dev_uid'];
    $Fragile = $_POST['fragile'];

    // Kiểm tra xem hàng hóa có tồn tại không
    $sql = "SELECT add_card FROM goods WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL_Error: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $Good_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['add_card'] == 0) {
                if (!empty($Gname) && !empty($Number) && !empty($Origin)) {
                    // Kiểm tra trùng Serial Number
                    $sql = "SELECT serialnumber FROM goods WHERE serialnumber=? AND id NOT LIKE ?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "SQL_Error: " . mysqli_error($conn);
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "si", $Number, $Good_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if (!$row = mysqli_fetch_assoc($result)) {
                            $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "SQL_Error: " . mysqli_error($conn);
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $dev_uid);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $dev_name = ($row = mysqli_fetch_assoc($result)) ? $row['device_dep'] : "All";

                                $sql = "UPDATE goods SET good=?, serialnumber=?, fragile=?, origin=?, good_date=CURDATE(), device_uid=?, device_dep=?, exp_date=?, add_card=1 WHERE id=?";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo "SQL_Error_select_Fingerprint: " . mysqli_error($conn);
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "sssssssi", $Gname, $Number, $Fragile, $Origin, $dev_uid, $dev_name, $Exp_date, $Good_id);
                                    mysqli_stmt_execute($stmt);
                                    echo 1;
                                    exit();
                                }
                            }
                        } else {
                            echo "The serial number is already taken!";
                            exit();
                        }
                    }
                } else {
                    echo "Empty Fields";
                    exit();
                }
            } else {
                echo "This Good already exists";
                exit();
            }
        } else {
            echo "There's no selected Card!";
            exit();
        }
    }
}

// Cập nhật hàng hóa
if (isset($_POST['Update'])) {
    $Good_id = $_POST['good_id'];
    $Gname = $_POST['good'];
    $Number = $_POST['number'];
    $Exp_date = $_POST['exp_date'];
    $Origin = $_POST['origin'];
    $dev_uid = $_POST['dev_uid'];
    $Fragile = $_POST['fragile'];

    $sql = "SELECT add_card FROM goods WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL_Error: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $Good_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['add_card'] == 0) {
                echo "First, You need to add the Good!";
                exit();
            } else {
                if (!empty($Gname) && !empty($Number) && !empty($Origin)) {
                    $sql = "SELECT serialnumber FROM goods WHERE serialnumber=? AND id NOT LIKE ?";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "SQL_Error: " . mysqli_error($conn);
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "si", $Number, $Good_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if (!$row = mysqli_fetch_assoc($result)) {
                            $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "SQL_Error: " . mysqli_error($conn);
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $dev_uid);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $dev_name = ($row = mysqli_fetch_assoc($result)) ? $row['device_dep'] : "All";

                                $sql = "UPDATE goods SET good=?, serialnumber=?, fragile=?, origin=?, good_date=CURDATE(), device_uid=?, device_dep=?, exp_date=? WHERE id=?";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo "SQL_Error_select_Card: " . mysqli_error($conn);
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "sssssssi", $Gname, $Number, $Fragile, $Origin, $dev_uid, $dev_name, $Exp_date, $Good_id);
                                    mysqli_stmt_execute($stmt);
                                    echo 1;
                                    exit();
                                }
                            }
                        } else {
                            echo "The serial number is already taken!";
                            exit();
                        }
                    }
                } else {
                    echo "All fields are required!";
                    exit();
                }
            }
        } else {
            echo "There's no selected Good to be updated!";
            exit();
        }
    }
}

// Chọn hàng hóa
if (isset($_GET['select'])) {
    $card_uid = $_GET['card_uid'];

    $sql = "SELECT * FROM goods WHERE card_uid=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL_Error_Select: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $card_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        header('Content-Type: application/json');
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit();
    }
}

// Xóa hàng hóa
if (isset($_POST['delete'])) {
    $Good_id = $_POST['good_id'];

    if (empty($Good_id)) {
        echo "There's no selected good to remove";
        exit();
    } else {
        $sql = "DELETE FROM goods WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "SQL_Error_delete: " . mysqli_error($conn);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $Good_id);
            mysqli_stmt_execute($stmt);
            echo 1;
            exit();
        }
    }
}
?>
