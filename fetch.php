<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See https://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - https://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$table = '';

if (isset($_GET['table'])) {
    if ($_GET['table'] == 'tbl_user') {
        $table = 'tbl_user';
    } else if ($_GET['table'] == 'vw_customer') {
        $table = 'vw_customer';
    } else if ($_GET['table'] == 'vw_sales') {
        $table = 'vw_sales';
    } else if ($_GET['table'] == 'vw_delivery') {
        $table = 'vw_delivery';
    } else if ($_GET['table'] == 'vw_delivery_today') {
        $table = 'vw_delivery';
        $kurir = isset($_GET['kurir']) ? trim($_GET['kurir']) : '';
        $is_today = true;
    } else {
        die("Invalid table name");
    }
}

$primaryKey = 'id';
$is_today = $is_today ?? false;

switch ($table) {
    case 'tbl_user':
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'email', 'dt' => 1 ),
            array( 'db' => 'nohp', 'dt' => 2 ),
            array( 'db' => 'full_name', 'dt' => 3 ),
            array( 'db' => 'role', 'dt' => 4 ),
            array( 'db' => 'no_vehicle', 'dt' => 5 ),
            array( 'db' => 'is_active', 'dt' => 6 ),
            array( 'db' => 'id', 'dt' => 7 )
        );
        $where = null;
        break;

    case 'vw_customer':
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'name', 'dt' => 1 ),
            array( 'db' => 'email', 'dt' => 2 ),
            array( 'db' => 'nohp', 'dt' => 3 ),
            array( 'db' => 'sisa_qty_box', 'dt' => 4 ),
            array( 'db' => 'address', 'dt' => 5 ),
            array( 'db' => 'latitude', 'dt' => 6 ),
            array( 'db' => 'longitude', 'dt' => 7 ),
            array( 'db' => 'altitude', 'dt' => 8 ),
            array( 'db' => 'id', 'dt' => 9 )
        );
        $where = null;
        break;

    case 'vw_sales':
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'customer_code', 'dt' => 1 ),
            array( 'db' => 'customer_name', 'dt' => 2 ),
            array( 'db' => 'input_at', 'dt' => 3 ),
            array( 'db' => 'start_periode', 'dt' => 4 ),
            array( 'db' => 'end_periode', 'dt' => 5 ),
            array( 'db' => 'total_price', 'dt' => 6 ),
            array( 'db' => 'total_qty_box', 'dt' => 7 ),
            array( 'db' => 'sisa_qty_box', 'dt' => 8 ),
            array( 'db' => 'id', 'dt' => 9 )
        );
        $where = null;
        break;

    case 'vw_delivery':
        $columns = array(
            array( 'db' => 'code', 'dt' => 0 ),
            array( 'db' => 'sales_code', 'dt' => 1 ),
            array( 'db' => 'customer_code', 'dt' => 2 ),
            array( 'db' => 'customer_name', 'dt' => 3 ),
            array( 'db' => 'customer_nohp', 'dt' => 4 ),
            array( 'db' => 'customer_address', 'dt' => 5 ),
            array( 'db' => 'kurir_code', 'dt' => 6 ),
            array( 'db' => 'kurir_name', 'dt' => 7 ),
            array( 'db' => 'kurir_nohp', 'dt' => 8 ),
            array( 'db' => 'no_vehicle', 'dt' => 9 ),
            array( 'db' => 'schedule_date', 'dt' => 10 ),
            array( 'db' => 'departure_time', 'dt' => 11 ),
            array( 'db' => 'arrival_time', 'dt' => 12 ),
            array( 'db' => 'status', 'dt' => 13 ),
            array( 'db' => 'id', 'dt' => 14 )
        );
        $where = $is_today && !empty($kurir) ? "DATE(schedule_date) = CURDATE() AND LOWER(kurir_code) = LOWER('$kurir')" : null;
        break;

    default:
        die("Invalid table configuration");
}

if ($_ENV['APP_ENV'] == 'development') {
    if (!empty($_ENV['DB_PORT_DEV']) && $_ENV['DB_PORT_DEV'] != null) {
        $sql_details = array(
            'user' => $_ENV['DB_USER_DEV'],
            'pass' => $_ENV['DB_PASS_DEV'],
            'db'   => $_ENV['DB_NAME_DEV'],
            'host' => $_ENV['DB_HOST_DEV'],
            'port' => $_ENV['DB_PORT_DEV']
        );
    } else {
        $sql_details = array(
            'user' => $_ENV['DB_USER_DEV'],
            'pass' => $_ENV['DB_PASS_DEV'],
            'db'   => $_ENV['DB_NAME_DEV'],
            'host' => $_ENV['DB_HOST_DEV']
        );
    }
} else if ($_ENV['APP_ENV'] == 'production') {
    if (!empty($_ENV['DB_PORT_PROD']) && $_ENV['DB_PORT_PROD'] != null) {
        $sql_details = array(
            'user' => $_ENV['DB_USER_PROD'],
            'pass' => $_ENV['DB_PASS_PROD'],
            'db'   => $_ENV['DB_NAME_PROD'],
            'host' => $_ENV['DB_HOST_PROD'],
            'port' => $_ENV['DB_PORT_PROD']
        );
    } else {
        $sql_details = array(
            'user' => $_ENV['DB_USER_PROD'],
            'pass' => $_ENV['DB_PASS_PROD'],
            'db'   => $_ENV['DB_NAME_PROD'],
            'host' => $_ENV['DB_HOST_PROD']
        );
    }
}

require( 'assets/ssp.class.php' );

echo json_encode(
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where )
);