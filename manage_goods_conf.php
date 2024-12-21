

<?php  
// Kết nối đến cơ sở dữ liệu
require 'connectDB.php';

// Thêm người dùng
if (isset($_POST['Add'])) {
    $Good_id = $_POST['good_id'];
    $Gname = $_POST['good'];
    $Number = $_POST['number'];
    $Origin = $_POST['origin'];
    $dev_uid = $_POST['dev_uid'];
    $Fragile = $_POST['fragile'];

    // Kiểm tra xem có người dùng nào được chọn hay không
    $sql = "SELECT add_card FROM goods WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $Good_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                if (!empty($Gname) && !empty($Number) && !empty($Origin)) {
                    // Kiểm tra nếu đã có người dùng nào có Serial Number giống
                    $sql = "SELECT serialnumber FROM goods WHERE serialnumber=? AND id NOT LIKE ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error: " . mysqli_error($conn);
                        exit();
                    } else {
                        mysqli_stmt_bind_param($result, "di", $Number, $Good_id);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {
                            $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error: " . mysqli_error($conn);
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $dev_uid);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $dev_name = $row['device_dep'];
                                } else {
                                    $dev_name = "All";
                                }
                            }
                            $sql = "UPDATE goods SET good=?, serialnumber=?, fragile=?, origin=?, good_date=CURDATE(), device_uid=?, device_dep=?, add_card=1 WHERE id=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_select_Fingerprint: " . mysqli_error($conn);
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "sdssssi", $Gname, $Number, $Fragile, $Origin, $dev_uid, $dev_name, $Good_id);
                                mysqli_stmt_execute($result);
                                echo 1;
                                exit();
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

if (isset($_POST['Update'])) {
    $Good_id = $_POST['good_id'];
    $Gname = $_POST['good'];
    $Number = $_POST['number'];
    $Origin = $_POST['origin'];
    $dev_uid = $_POST['dev_uid'];
    $Fragile = $_POST['fragile'];

    // Kiểm tra nếu người dùng đã được chọn
    $sql = "SELECT add_card FROM goods WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($result, "i", $Good_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)) {
            if ($row['add_card'] == 0) {
                echo "First, You need to add the Good!";
                exit();
            } else {
                if (empty($Gname) && empty($Number) && empty($Origin)) {
                    echo "Empty Fields";
                    exit();
                } else {
                    // Kiểm tra nếu đã có người dùng nào có Serial Number giống
                    $sql = "SELECT serialnumber FROM goods WHERE serialnumber=? AND id NOT LIKE ?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error: " . mysqli_error($conn);
                        exit();
                    } else {
                        mysqli_stmt_bind_param($result, "di", $Number, $Good_id);
                        mysqli_stmt_execute($result);
                        $resultl = mysqli_stmt_get_result($result);
                        if (!$row = mysqli_fetch_assoc($resultl)) {
                            // Kiểm tra xem tên thiết bị có tồn tại không
                            $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error: " . mysqli_error($conn);
                                exit();
                            } else {
                                mysqli_stmt_bind_param($result, "s", $dev_uid);
                                mysqli_stmt_execute($result);
                                $resultl = mysqli_stmt_get_result($result);
                                if ($row = mysqli_fetch_assoc($resultl)) {
                                    $dev_name = $row['device_dep'];
                                } else {
                                    $dev_name = "All";
                                }
                            }

                            // Kiểm tra tất cả các trường đầu vào
                            if (!empty($Gname) && !empty($Origin) && !empty($Number)) {
                                $sql = "UPDATE goods SET good=?, serialnumber=?, fragile=?, origin=?, good_date=CURDATE(), device_uid=?, device_dep=?, add_card=1 WHERE id=?";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_select_Card: " . mysqli_error($conn);
                                    exit();
                                } else {
                                    // Bind tham số
                                    mysqli_stmt_bind_param($result, "sdssssi", $Gname, $Number, $Fragile, $Origin, $dev_uid, $dev_name, $Good_id);
                                    mysqli_stmt_execute($result);
                                    echo 1;
                                    exit();
                                }
                            } else {
                                echo "All fields are required!";
                                exit();
                            }
                        } else {
                            echo "The serial number is already taken!";
                            exit();
                        }
                    }
                }
            }
        } else {
            echo "There's no selected Good to be updated!";
            exit();
        }
    }
}


// Chọn người dùng (thẻ)
if (isset($_GET['select'])) {
    $card_uid = $_GET['card_uid'];

    $sql = "SELECT * FROM goods WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select: " . mysqli_error($conn);
        exit();
    } else {
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        //echo "Good Fingerprint selected";
        //exit();
        // Trả về dữ liệu dưới dạng JSON
        header('Content-Type: application/json');
        $data = array();
        if ($row = mysqli_fetch_assoc($resultl)) {
            foreach ($resultl as $row){
                $data[] = $row;}
        }
        $resultl->close();
        $conn->close();
        print json_encode($data);  // Trả về JSON
    }
}

// Xóa người dùng
if (isset($_POST['delete'])) {
    $Good_id = $_POST['good_id'];

    if (empty($Good_id)) {
        echo "There's no selected good to remove";
        exit();
    } else {
        $sql = "DELETE FROM goods WHERE id=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_delete: " . mysqli_error($conn);
            exit();
        } else {
            mysqli_stmt_bind_param($result, "i", $Good_id);
            mysqli_stmt_execute($result);
            echo 1;
            exit();
        }
    }
}
?>
