<?php
    require "../connect.php";
    require "../components/notify.php";

    if (isset($_SESSION['user'])) {
        if (isset($_POST['delete-customer'])) {
            $id = (int)$_POST['id'];

            $qDelete = mysqli_query($con, "DELETE FROM tbl_customer WHERE id = $id");

            if ($qDelete) {
                alert('success', 'Success', 'Berhasil Menghapus Data', base_url().'/customer.php');
            } else {
                alert('error', 'Error', 'Gagal Menghapus Data', base_url().'/customer.php');
            }
        } else if (isset($_POST['delete-delivery'])) {
            $id = (int)$_POST['id'];

            $qDelete = mysqli_query($con, "DELETE FROM tbl_delivery WHERE id = $id");

            if ($qDelete) {
                alert('success', 'Success', 'Berhasil Menghapus Data', base_url().'/delivery.php');
            } else {
                alert('error', 'Error', 'Gagal Menghapus Data', base_url().'/delivery.php');
            }
        } else if (isset($_POST['delete-sales'])) {
            $id = (int)$_POST['id'];

            $qDelete = mysqli_query($con, "DELETE FROM tbl_sales WHERE id = $id");

            if ($qDelete) {
                alert('success', 'Success', 'Berhasil Menghapus Data', base_url().'/sales.php');
            } else {
                alert('error', 'Error', 'Gagal Menghapus Data', base_url().'/sales.php');
            }
        } else if (isset($_POST['delete-user'])) {
            $id = (int)$_POST['id'];

            $qDelete = mysqli_query($con, "DELETE FROM tbl_user WHERE id = $id");

            if ($qDelete) {
                alert('success', 'Success', 'Berhasil Menghapus Data', base_url().'/users.php');
            } else {
                alert('error', 'Error', 'Gagal Menghapus Data', base_url().'/users.php');
            }
        } else {
            echo "<script>window.location='".base_url()."/login.php';</script>";
        }
    } else {
        echo "<script>window.location='".base_url()."/login.php';</script>";
    }
?>