<?php
require 'connectDB.php';

if (isset($_POST["To_Excel"])) {
    $searchQuery = "1=1"; // Mặc định lấy tất cả dữ liệu
    $output = '';

    // Start date filter
    if (!empty($_POST['date_sel_start'])) {
        $Start_date = $_POST['date_sel_start'];
        $searchQuery .= " AND checkindate >= '$Start_date'";
    }
    // End date filter
    if (!empty($_POST['date_sel_end'])) {
        $End_date = $_POST['date_sel_end'];
        $searchQuery .= " AND checkindate <= '$End_date'";
    }
    // Time filter
    if (!empty($_POST['time_sel'])) {
        $timeField = $_POST['time_sel'] == 'Time_in' ? 'timein' : 'timeout';
        if (!empty($_POST['time_sel_start'])) {
            $Start_time = $_POST['time_sel_start'];
            $searchQuery .= " AND $timeField >= '$Start_time'";
        }
        if (!empty($_POST['time_sel_end'])) {
            $End_time = $_POST['time_sel_end'];
            $searchQuery .= " AND $timeField <= '$End_time'";
        }
    }
    // Card filter
    if (!empty($_POST['card_sel']) && $_POST['card_sel'] != '0') {
        $card_sel = $_POST['card_sel'];
        $searchQuery .= " AND card_uid = '$card_sel'";
    }
    // Device filter
    if (!empty($_POST['dev_sel']) && $_POST['dev_sel'] != '0') {
        $dev_uid = $_POST['dev_sel'];
        $searchQuery .= " AND device_uid = '$dev_uid'";
    }

    $sql = "SELECT * FROM goods_logs WHERE $searchQuery ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);

    if ($result && $result->num_rows > 0) {
        $output .= '
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Goods</th>
                <th>Serial Number</th>
                <th>Card UID</th>
                <th>Device ID</th>
                <th>Device Dep</th>
                <th>Date log</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>';
        while ($row = $result->fetch_assoc()) {
            $output .= '
            <tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['good'] . '</td>
                <td>' . $row['serialnumber'] . '</td>
                <td>' . $row['card_uid'] . '</td>
                <td>' . $row['device_uid'] . '</td>
                <td>' . $row['device_dep'] . '</td>
                <td>' . $row['checkindate'] . '</td>
                <td>' . $row['timein'] . '</td>
                <td>' . $row['timeout'] . '</td>
            </tr>';
        }
        $output .= '</table>';

        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=Goods_Log_' . date('Ymd') . '.xls');
        echo $output;
        exit();
    } else {
        echo "No records found!";
    }
}
?>
