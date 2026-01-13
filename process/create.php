<?php
    require "../connect.php";
    require "../components/notify.php";

    if (isset($_SESSION['user'])) {
        if (isset($_POST['create-customer'])) {
            $photo = trim(mysqli_real_escape_string($con, $_POST['base64_photo'] ?? null));
            $name = trim(mysqli_real_escape_string($con, $_POST['name']));
            $nohp = trim(mysqli_real_escape_string($con, $_POST['nohp']));
            $email = trim(mysqli_real_escape_string($con, $_POST['email']));
            $address = trim(mysqli_real_escape_string($con, $_POST['address']));
            $latitude = trim(mysqli_real_escape_string($con, $_POST['latitude']));
            $longitude = trim(mysqli_real_escape_string($con, $_POST['longitude']));
            $altitude = trim(mysqli_real_escape_string($con, $_POST['altitude'] ?? null));

            $qSequence = mysqli_query($con, "SELECT * FROM tbl_sequence WHERE just_for = 'Pelanggan' LIMIT 1");
            $dataSequence = mysqli_fetch_array($qSequence);
            if ($dataSequence['type'] == 'REGULAR') {
                $the_initial = $dataSequence['code'];
                $the_val = $dataSequence['next_val'] ?? 1;
                $code = $the_initial . str_pad($the_val, 4, '0', STR_PAD_LEFT);
            } else if ($dataSequence['type'] == 'UNIX') {
                $the_initial = $dataSequence['code'];
                $the_val = round(microtime(true) * 1000);
                $code = $the_initial . $the_val;
            }

            $qCreate = mysqli_query($con, "INSERT INTO tbl_customer (
                photo,
                code,
                name,
                nohp,
                email,
                address,
                latitude,
                longitude,
                altitude,
                input_by
            ) VALUES (
                '$photo',
                '$code',
                '$name',
                '$nohp',
                '$email',
                '$address',
                '$latitude',
                '$longitude',
                '$altitude',
                '{$_SESSION['user']}'
            )");

            if ($qCreate) {
                if ($dataSequence['type'] == 'REGULAR') {
                    $pluser = ($dataSequence['next_val'] ?? 1) + 1;
                    $qUpdateSequence = mysqli_query($con, "UPDATE tbl_sequence SET next_val=$pluser WHERE just_for = '{$dataSequence['just_for']}'");
                }
                alert('success', 'Success', 'Berhasil Menambah Data', base_url().'/customer.php');
            } else {
                alert('error', 'Error', 'Gagal Menambah Data', base_url().'/customer.php');
            }
        } else if (isset($_POST['create-delivery'])) {
            $sales = trim(mysqli_real_escape_string($con, $_POST['sales']));
            $kurir = trim(mysqli_real_escape_string($con, $_POST['kurir']));
            $qty_box = trim(mysqli_real_escape_string($con, $_POST['qty_box']));
            $schedule_date = trim(mysqli_real_escape_string($con, $_POST['schedule_date']));

            $qSequence = mysqli_query($con, "SELECT * FROM tbl_sequence WHERE just_for = 'Pengantaran' LIMIT 1");
            $dataSequence = mysqli_fetch_array($qSequence);
            if ($dataSequence['type'] == 'REGULAR') {
                $the_initial = $dataSequence['code'];
                $the_val = $dataSequence['next_val'] ?? 1;
                $code = $the_initial . str_pad($the_val, 4, '0', STR_PAD_LEFT);
            } else if ($dataSequence['type'] == 'UNIX') {
                $the_initial = $dataSequence['code'];
                $the_val = round(microtime(true) * 1000);
                $code = $the_initial . $the_val;
            }

            $qCreate = mysqli_query($con, "INSERT INTO tbl_delivery (
                code,
                sales_code,
                kurir_code,
                qty_box,
                schedule_date,
                status,
                input_by
            ) VALUES (
                '$code',
                '$sales',
                '$kurir',
                $qty_box,
                '$schedule_date',
                'WAIT',
                '{$_SESSION['user']}'
            )");

            if ($qCreate) {
                if ($dataSequence['type'] == 'REGULAR') {
                    $pluser = ($dataSequence['next_val'] ?? 1) + 1;
                    $qUpdateSequence = mysqli_query($con, "UPDATE tbl_sequence SET next_val=$pluser WHERE just_for = '{$dataSequence['just_for']}'");
                }
                alert('success', 'Success', 'Berhasil Menambah Data', base_url().'/delivery.php');
            } else {
                alert('error', 'Error', 'Gagal Menambah Data', base_url().'/delivery.php');
            }
        } else if (isset($_POST['create-sales'])) {
            $customer = trim(mysqli_real_escape_string($con, $_POST['customer']));
            $start_periode = trim(mysqli_real_escape_string($con, $_POST['start_periode']));
            $end_periode = trim(mysqli_real_escape_string($con, $_POST['end_periode']));
            $total_qty_box = trim(mysqli_real_escape_string($con, $_POST['total_qty_box']));
            $total_price = trim(mysqli_real_escape_string($con, $_POST['total_price']));

            $qSequence = mysqli_query($con, "SELECT * FROM tbl_sequence WHERE just_for = 'Penjualan' LIMIT 1");
            $dataSequence = mysqli_fetch_array($qSequence);
            if ($dataSequence['type'] == 'REGULAR') {
                $the_initial = $dataSequence['code'];
                $the_val = $dataSequence['next_val'] ?? 1;
                $code = $the_initial . str_pad($the_val, 4, '0', STR_PAD_LEFT);
            } else if ($dataSequence['type'] == 'UNIX') {
                $the_initial = $dataSequence['code'];
                $the_val = round(microtime(true) * 1000);
                $code = $the_initial . $the_val;
            }

            $qCreate = mysqli_query($con, "INSERT INTO tbl_sales (
                code,
                customer_code,
                start_periode,
                end_periode,
                total_qty_box,
                total_price,
                input_by
            ) VALUES (
                '$code',
                '$customer',
                '$start_periode',
                '$end_periode',
                $total_qty_box,
                $total_price,
                '{$_SESSION['user']}'
            )");

            if ($qCreate) {
                if ($dataSequence['type'] == 'REGULAR') {
                    $pluser = ($dataSequence['next_val'] ?? 1) + 1;
                    $qUpdateSequence = mysqli_query($con, "UPDATE tbl_sequence SET next_val=$pluser WHERE just_for = '{$dataSequence['just_for']}'");
                }
                alert('success', 'Success', 'Berhasil Menambah Data', base_url().'/sales.php');
            } else {
                alert('error', 'Error', 'Gagal Menambah Data', base_url().'/sales.php');
            }
        } else if (isset($_POST['create-user'])) {
            $photo = trim(mysqli_real_escape_string($con, $_POST['base64_photo'] ?? null));
            $email = trim(mysqli_real_escape_string($con, $_POST['email']));
            $nohp = trim(mysqli_real_escape_string($con, $_POST['nohp']));
            $full_name = trim(mysqli_real_escape_string($con, $_POST['full_name']));
            $role = trim(mysqli_real_escape_string($con, $_POST['role']));
            $no_vehicle = trim(mysqli_real_escape_string($con, $_POST['no_vehicle'] ?? null));
            $wa_token = trim(mysqli_real_escape_string($con, $_POST['wa_token'] ?? null));
            $active = trim(mysqli_real_escape_string($con, $_POST['is_active']));

            if ($role == 'ADMIN') {
                $qSequence = mysqli_query($con, "SELECT * FROM tbl_sequence WHERE just_for = 'Admin' LIMIT 1");
            } else if ($role == 'KURIR') {
                $qSequence = mysqli_query($con, "SELECT * FROM tbl_sequence WHERE just_for = 'Kurir' LIMIT 1");
            }
            $dataSequence = mysqli_fetch_array($qSequence);
            if ($dataSequence['type'] == 'REGULAR') {
                $the_initial = $dataSequence['code'];
                $the_val = $dataSequence['next_val'] ?? 1;
                $code = $the_initial . str_pad($the_val, 4, '0', STR_PAD_LEFT);
            } else if ($dataSequence['type'] == 'UNIX') {
                $the_initial = $dataSequence['code'];
                $the_val = round(microtime(true) * 1000);
                $code = $the_initial . $the_val;
            }

            $randomString = generateRandomString(10);
            $hashedPassword = password_hash($randomString, PASSWORD_BCRYPT);

            $qCreate = mysqli_query($con, "INSERT INTO tbl_user (
                code,
                photo,
                email,
                nohp,
                full_name,
                pass,
                role,
                no_vehicle,
                wa_token,
                is_active,
                input_by
            ) VALUES (
                '$code',
                '$photo',
                '$email',
                '$nohp',
                '$full_name',
                '$hashedPassword',
                '$role',
                '$no_vehicle',
                '$wa_token',
                '$active',
                '{$_SESSION['user']}'
            )");

            if ($qCreate) {
                if ($dataSequence['type'] == 'REGULAR') {
                    $pluser = ($dataSequence['next_val'] ?? 1) + 1;
                    $qUpdateSequence = mysqli_query($con, "UPDATE tbl_sequence SET next_val=$pluser WHERE just_for = '{$dataSequence['just_for']}'");
                } 
                confirmation('success', 'Success', 'Berhasil Menambah Data<br><br>Password : <b id=\'thepass\'>'.$randomString.'</b>', base_url().'/users.php');
            } else {
                alert('error', 'Error', 'Gagal Menambah Data', base_url().'/users.php');
            }
        } else {
            echo "<script>window.location='".base_url()."/login.php';</script>";
        }
    } else {
        echo "<script>window.location='".base_url()."/login.php';</script>";
    }
?>