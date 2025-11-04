<?php
    require "../connect.php";
    require "../components/notify.php";

    if (isset($_SESSION['user'])) {
        if (isset($_POST['update-customer'])) {
            $id = (int)$_POST['id'];
            $photo = trim(mysqli_real_escape_string($con, $_POST['base64_photo'] ?? null));
            $name = trim(mysqli_real_escape_string($con, $_POST['name']));
            $nohp = trim(mysqli_real_escape_string($con, $_POST['nohp']));
            $email = trim(mysqli_real_escape_string($con, $_POST['email']));
            $address = trim(mysqli_real_escape_string($con, $_POST['address']));
            $latitude = trim(mysqli_real_escape_string($con, $_POST['latitude']));
            $longitude = trim(mysqli_real_escape_string($con, $_POST['longitude']));
            $altitude = trim(mysqli_real_escape_string($con, $_POST['altitude'] ?? null));
            $update_at = date("Y-m-d H:i:s");

            $qUpdate = mysqli_query($con, "UPDATE tbl_customer SET
                photo='$photo',
                name='$name',
                nohp='$nohp',
                email='$email',
                address='$address',
                latitude='$latitude',
                longitude='$longitude',
                altitude='$altitude',
                update_at='$update_at',
                update_by='{$_SESSION['user']}'
            WHERE id = $id");

            if ($qUpdate) {
                alert('success', 'Success', 'Berhasil Mengubah Data', base_url().'/customer.php');
            } else {
                alert('error', 'Error', 'Gagal Mengubah Data', base_url().'/customer.php');
            }
        } else if (isset($_POST['update-delivery'])) {
            $id = (int)$_POST['id'];
            $sales = trim(mysqli_real_escape_string($con, $_POST['sales']));
            $kurir = trim(mysqli_real_escape_string($con, $_POST['kurir']));
            $qty_box = trim(mysqli_real_escape_string($con, $_POST['qty_box']));
            $schedule_date = trim(mysqli_real_escape_string($con, $_POST['schedule_date']));
            $update_at = date("Y-m-d H:i:s");
            $now = date("Y-m-d H:i:s");

            $qUpdate = mysqli_query($con, "UPDATE tbl_delivery SET
                sales_code='$sales',
                kurir_code='$kurir',
                qty_box=$qty_box,
                schedule_date='$schedule_date',
                update_at='$update_at',
                update_by='{$_SESSION['user']}'
            WHERE id = $id");

            if ($qUpdate) {
                alert('success', 'Success', 'Berhasil Mengubah Data', base_url().'/delivery.php');
            } else {
                alert('error', 'Error', 'Gagal Mengubah Data', base_url().'/delivery.php');
            }
        } else if (isset($_POST['update-sales'])) {
            $id = (int)$_POST['id'];
            $customer = trim(mysqli_real_escape_string($con, $_POST['customer']));
            $start_periode = trim(mysqli_real_escape_string($con, $_POST['start_periode']));
            $end_periode = trim(mysqli_real_escape_string($con, $_POST['end_periode']));
            $total_qty_box = trim(mysqli_real_escape_string($con, $_POST['total_qty_box']));
            $total_price = trim(mysqli_real_escape_string($con, $_POST['total_price']));
            $update_at = date("Y-m-d H:i:s");

            $qUpdate = mysqli_query($con, "UPDATE tbl_sales SET
                customer_code='$customer',
                start_periode='$start_periode',
                end_periode='$end_periode',
                total_qty_box=$total_qty_box,
                total_price=$total_price,
                update_at='$update_at',
                update_by='{$_SESSION['user']}'
            WHERE id = $id");

            if ($qUpdate) {
                alert('success', 'Success', 'Berhasil Mengubah Data', base_url().'/sales.php');
            } else {
                alert('error', 'Error', 'Gagal Mengubah Data', base_url().'/sales.php');
            }
        } else if (isset($_POST['update-user'])) {
            $id = (int)$_POST['id'];
            $photo = trim(mysqli_real_escape_string($con, $_POST['base64_photo'] ?? null));
            $email = trim(mysqli_real_escape_string($con, $_POST['email']));
            $nohp = trim(mysqli_real_escape_string($con, $_POST['nohp']));
            $full_name = trim(mysqli_real_escape_string($con, $_POST['full_name']));
            $no_vehicle = trim(mysqli_real_escape_string($con, $_POST['no_vehicle'] ?? null));
            $wa_token = trim(mysqli_real_escape_string($con, $_POST['wa_token'] ?? null));
            $active = trim(mysqli_real_escape_string($con, $_POST['is_active']));
            $update_at = date("Y-m-d H:i:s");

            $qUpdate = mysqli_query($con, "UPDATE tbl_user SET
                photo='$photo',
                email='$email',
                nohp='$nohp',
                full_name='$full_name',
                no_vehicle='$no_vehicle',
                wa_token='$wa_token',
                is_active='$active',
                update_at='$update_at',
                update_by='{$_SESSION['user']}'
            WHERE id = $id");

            if ($qUpdate) {
                alert('success', 'Success', 'Berhasil Mengubah Data', base_url().'/users.php');
            } else {
                alert('error', 'Error', 'Gagal Mengubah Data', base_url().'/users.php');
            }
        } else {
            echo "<script>window.location='".base_url()."/login.php';</script>";
        }
    } else {
        echo "<script>window.location='".base_url()."/login.php';</script>";
    }
?>