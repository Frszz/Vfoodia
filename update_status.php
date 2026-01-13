<?php
    require "connect.php";

    if (isset($_POST['updates'])) {
        $updates = json_decode($_POST['updates'], true);

        foreach ($updates as $item) {
            $code = mysqli_real_escape_string($con, $item['code']);
            $status = mysqli_real_escape_string($con, $item['status']);
            $update_at = date("Y-m-d H:i:s");
            $now = date("Y-m-d H:i:s");

            if ($status == 'WAIT') {
                $message = 'Menunggu kurir ⌚';
            } else if ($status == 'WA') {
                $message = 'Kurir dalam perjalanan 🛵';
            } else if ($status == 'DONE') {
                $message = 'Kurir selesai mengantar ✅';
            } else if ($status == 'CANCEL') {
                $message = 'Pengantaran dibatalkan ❌';
            }

            $query = "UPDATE tbl_delivery SET status = '$status', update_at = '$update_at', update_by = '{$_SESSION['user']}'";

            if ($status === 'WAIT') {
                $query .= ", departure_time = null, arrival_time = null";
            } else if ($status === 'WA') {
                $query .= ", departure_time = '$now'";
            } else if ($status === 'DONE') {
                $query .= ", arrival_time = '$now'";
            } else if ($status === 'CANCEL') {
                $query .= ", departure_time = null, arrival_time = null";
            }

            $query .= " WHERE code = '$code'";

            $qStatus = mysqli_query($con, $query);
            if ($qStatus) {
                if (in_array($status, ['WA', 'DONE', 'CANCEL'])) {
                    $qDLV = mysqli_query($con, "SELECT * FROM vw_delivery WHERE code = '$code' LIMIT 1");
                    $dataDLV = mysqli_fetch_array($qDLV);
                    sendWA (
                        $dataDLV['wa_token'],
                        [
                            $dataDLV['customer_nohp']
                        ],
                        waTemplate (
                            "*No. Pengantaran* : ".$dataDLV['code']."\n\n*Kurir* : ".$dataDLV['kurir_name']."\n*No. Plat* : ".$dataDLV['no_vehicle']."\n\n*Pelanggan* : ".$dataDLV['customer_name']."\n*Alamat Pelanggan* : ".$dataDLV['customer_address']."\n\n".$message.""
                        )
                    );
                }
            }
        }
    }
?>