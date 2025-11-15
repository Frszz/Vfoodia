<?php
    require "connect.php";

    if (isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['role'])) {
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <?php
            include "components/beforeLoad.php";
        ?>
    </head>
    <body>
        <?php
            include "components/navigation.php";
        ?>

        <?php
            if (isset($_GET['type']) && $_GET['type'] == 'create' && $_SESSION['role'] == 'ADMIN') {
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/create.php" enctype="multipart/form-data">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="Generate By System" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Penjualan</label>
                                <select name="sales" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Penjualan</option>
                                    <?php
                                        $dropdownSales = mysqli_query($con, "SELECT * FROM vw_sales WHERE sisa_qty_box > 0");
                                        while ($listSales = mysqli_fetch_array($dropdownSales)) {
                                            echo "<option value=\"".$listSales['code']."\">".$listSales['code']." - ".$listSales['customer_code']."(".$listSales['customer_name'].")</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Kurir</label>
                                <select name="kurir" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Kurir</option>
                                    <?php
                                        $dropdownKurir = mysqli_query($con, "SELECT * FROM tbl_user WHERE role = 'KURIR' AND is_active = 'TRUE'");
                                        while ($listKurir = mysqli_fetch_array($dropdownKurir)) {
                                            echo "<option value=\"".$listKurir['code']."\">".$listKurir['code']." - ".$listKurir['full_name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Qty/Box</label>
                                <input type="text" name="qty_box" placeholder="Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Jadwal</label>
                                <input type="date" name="schedule_date" required>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="create-delivery"><i class='bx bx-save'></i> Create</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'update' && isset($_GET['id']) && $_SESSION['role'] == 'ADMIN') {
                $id = (int)$_GET['id'];
                $qDelivery = mysqli_query($con, "SELECT * FROM vw_delivery WHERE id = $id LIMIT 1");
                $dataDelivery = mysqli_fetch_array($qDelivery);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/update.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataDelivery['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Penjualan</label>
                                <select name="sales" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Penjualan</option>
                                    <?php
                                        $dropdownSales = mysqli_query($con, "SELECT * FROM vw_sales");
                                        while ($listSales = mysqli_fetch_array($dropdownSales)) {
                                            $selected = ($listSales['code'] == $dataDelivery['sales_code']) ? 'selected' : '';
                                            echo "<option value=\"".$listSales['code']."\" $selected>".$listSales['code']." - ".$listSales['customer_code']."(".$listSales['customer_name'].")</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Kurir</label>
                                <select name="kurir" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Kurir</option>
                                    <?php
                                        $dropdownKurir = mysqli_query($con, "SELECT * FROM tbl_user WHERE role = 'KURIR' AND is_active = 'TRUE'");
                                        while ($listKurir = mysqli_fetch_array($dropdownKurir)) {
                                            $selected = ($listKurir['code'] == $dataDelivery['kurir_code']) ? 'selected' : '';
                                            echo "<option value=\"".$listKurir['code']."\" $selected>".$listKurir['code']." - ".$listKurir['full_name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Qty/Box</label>
                                <input type="text" name="qty_box" value="<?=$dataDelivery['qty_box']?>" placeholder="Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Jadwal</label>
                                <input type="date" name="schedule_date" value="<?=$dataDelivery['schedule_date']?>" required>
                            </div>
                            <div class="form-input">
                                <label>Status</label>
                                <input type="text" name="status" maxlength="20" value="<?=$dataDelivery['status']?>" placeholder="Status" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="update-delivery"><i class='bx bx-edit'></i> Update</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id']) && $_SESSION['role'] == 'ADMIN') {
                $id = (int)$_GET['id'];
                $qDelivery = mysqli_query($con, "SELECT * FROM vw_delivery WHERE id = $id LIMIT 1");
                $dataDelivery = mysqli_fetch_array($qDelivery);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/delete.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataDelivery['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Penjualan</label>
                                <input type="text" name="sales" maxlength="100" value="<?=$dataDelivery['sales_code']?> - <?=$dataDelivery['customer_code']?>(<?=$dataDelivery['customer_name']?>)" placeholder="Sales" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Kurir</label>
                                <input type="text" name="kurir" maxlength="100" value="<?=$dataDelivery['kurir_code']?> - <?=$dataDelivery['kurir_name']?>" placeholder="Kurir" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Qty/Box</label>
                                <input type="text" name="qty_box" value="<?=$dataDelivery['qty_box']?>" placeholder="Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Jadwal</label>
                                <input type="date" name="schedule_date" value="<?=$dataDelivery['schedule_date']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Status</label>
                                <input type="text" name="status" maxlength="20" value="<?=$dataDelivery['status']?>" placeholder="Status" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="delete-delivery"><i class='bx bx-trash'></i> Delete</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'read' && isset($_GET['id']) && $_SESSION['role'] == 'ADMIN') {
                $id = (int)$_GET['id'];
                $qDelivery = mysqli_query($con, "SELECT * FROM vw_delivery WHERE id = $id LIMIT 1");
                $dataDelivery = mysqli_fetch_array($qDelivery);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataDelivery['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Penjualan</label>
                                <input type="text" name="sales" maxlength="100" value="<?=$dataDelivery['sales_code']?> - <?=$dataDelivery['customer_code']?>(<?=$dataDelivery['customer_name']?>)" placeholder="Sales" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Kurir</label>
                                <input type="text" name="kurir" maxlength="100" value="<?=$dataDelivery['kurir_code']?> - <?=$dataDelivery['kurir_name']?>" placeholder="Kurir" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Qty/Box</label>
                                <input type="text" name="qty_box" value="<?=$dataDelivery['qty_box']?>" placeholder="Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Jadwal</label>
                                <input type="date" name="schedule_date" value="<?=$dataDelivery['schedule_date']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Status</label>
                                <input type="text" name="status" maxlength="20" value="<?=$dataDelivery['status']?>" placeholder="Status" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')" disabled>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else {
        ?>
                <main>
                    <section class="container section section__height">
                        <h2 class="section__title">Pengantaran</h2>
                        <?php
                            $optionStatus = [];
                            $result = mysqli_query($con, "SHOW COLUMNS FROM `tbl_delivery` WHERE `field` = 'status'");
                            if ($result) {
                                while($list = mysqli_fetch_row($result)){
                                    foreach(explode("','",substr($list[1],6,-2)) as $option){
                                        $optionStatus[] = $option;
                                    }
                                }
                            }
                        ?>
                        <script>
                            const statusOptions = <?=json_encode($optionStatus)?>
                        </script>
                        <?php
                            if ($_SESSION['role'] == 'KURIR') {
                                $listLocation = [];
                                $seenKeys = [];
                                $today = date('Y-m-d');
                                $qLocation = mysqli_query($con, "SELECT * FROM vw_delivery WHERE (status = 'OTW' OR status = 'WAIT') AND kurir_code = '{$_SESSION['user']}' AND schedule_date = '$today'");
                                while ($dataLocation = mysqli_fetch_array($qLocation)) {
                                    $lat = (float)$dataLocation['latitude'];
                                    $lng = (float)$dataLocation['longitude'];
                                    $customerCode = $dataLocation['customer_code'];
                                    $kurirCode = $dataLocation['kurir_code'];

                                    $key = "{$lat}|{$lng}|{$customerCode}|{$kurirCode}";
                                    if (!isset($seenKeys[$key])) {
                                        $seenKeys[$key] = true;
                                        $listLocation[] = [
                                            'lat' => $lat,
                                            'lng' => $lng,
                                            'label' => $customerCode . ' - ' . $dataLocation['customer_name'],
                                            'kurir' => $kurirCode . ' - ' . $dataLocation['kurir_name']
                                        ];
                                    }
                                }
                        ?>
                                <button class="other-button" id="submitStatus" style="margin-bottom: 10px;">Submit Status</button>
                                <table id="record" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">Kode</th>
                                            <th style="text-align: left;">Kode Penjualan</th>
                                            <th style="text-align: left;">Kode Pelanggan</th>
                                            <th style="text-align: left;">Nama Pelanggan</th>
                                            <th style="text-align: left;">No. Hp Pelanggan</th>
                                            <th style="text-align: left;">Alamat Pelanggan</th>
                                            <th style="text-align: left;">Kode Kurir</th>
                                            <th style="text-align: left;">Nama Kurir</th>
                                            <th style="text-align: left;">No. Hp Kurir</th>
                                            <th style="text-align: left;">No. Plat Kendaraan</th>
                                            <th style="text-align: left;">Jadwal Pengantaran</th>
                                            <th style="text-align: left;">Waktu Berangkat</th>
                                            <th style="text-align: left;">Waktu Tiba</th>
                                            <th style="text-align: left;">Status</th>
                                        </tr>
                                        <tr>
                                            <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Kode"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Kode Penjualan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Kode Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Nama Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Hp Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Alamat Pelanggan"></th>
                                            <th><input type="text" style="width: 120px; height: 18px;" class="search-field" placeholder="Cari Kode Kurir"></th>
                                            <th><input type="text" style="width: 120px; height: 18px;" class="search-field" placeholder="Cari Nama Kurir"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Hp Kurir"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Plat Kendaraan"></th>
                                            <th><input type="text" style="width: 160px; height: 18px;" class="search-field" placeholder="Cari Jadwal Pengantaran"></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                                <script>
                                    $(window).on('load', function() {
                                        new DataTable('#record', {
                                            ajax: {
                                                url: 'fetch.php?table=vw_delivery_today',
                                                data: {
                                                    kurir: '<?= $_SESSION['user'] ?>'
                                                }
                                            },
                                            fixedColumns: {
                                                start: 1,
                                                end: 1
                                            },
                                            scrollX: true,
                                            scrollCollapse: true,
                                            serverSide: false,
                                            responsive: true,
                                            order: [],
                                            columnDefs: [
                                                {
                                                    targets: 13,
                                                    orderable: false,
                                                    searchable: false,
                                                    render: function(data, type, row, meta) {
                                                        let code = row[0];
                                                        let select = `<select class="status-select" data-code="${code}">`;
                                                        statusOptions.forEach(option => {
                                                            select += `<option value="${option}" ${option === data ? 'selected' : ''}>${option}</option>`;
                                                        });
                                                        select += `</select>`;
                                                        return select;
                                                    }
                                                },
                                                {
                                                    targets: '_all',
                                                    orderable: false
                                                }
                                            ],
                                            rowCallback: function (row, data, index) {
                                                const status = data[13];
                                                const colorClassMap = {
                                                    WAIT: 'status-wait',
                                                    OTW: 'status-otw',
                                                    DONE: 'status-done',
                                                    CANCEL: 'status-cancel'
                                                };

                                                $(row).removeClass('status-wait status-otw status-done status-cancel');

                                                const className = colorClassMap[status] || '';

                                                if (className) {
                                                    $(row).addClass(className);

                                                    const rowIndex = index + 1;
                                                    const fixedLeftRow = $('.DTFC_LeftWrapper .DTFC_Cloned tbody tr').eq(rowIndex);
                                                    const fixedRightRow = $('.DTFC_RightWrapper .DTFC_Cloned tbody tr').eq(rowIndex);

                                                    fixedLeftRow.removeClass('status-wait status-otw status-done status-cancel').addClass(className);
                                                    fixedRightRow.removeClass('status-wait status-otw status-done status-cancel').addClass(className);
                                                }
                                            },
                                            initComplete: function () {
                                                this.api()
                                                    .columns()
                                                    .every(function () {
                                                        let column = this;
                                                        $('input', column.header()).on('keyup change clear', function () {
                                                            if (column.search() !== this.value) {
                                                                column
                                                                    .search(this.value)
                                                                    .draw(false);
                                                                column.table().page('first').draw(false);
                                                            }
                                                        });
                                                    });
                                            }
                                        });
                                    });
                                </script>
                        <?php
                            } else if ($_SESSION['role'] == 'ADMIN') {
                                $listLocation = [];
                                $seenKeys = [];
                                $today = date('Y-m-d');
                                $qLocation = mysqli_query($con, "SELECT * FROM vw_delivery WHERE status = 'OTW' OR status = 'WAIT' AND schedule_date = '$today'");
                                while ($dataLocation = mysqli_fetch_array($qLocation)) {
                                    $lat = (float)$dataLocation['latitude'];
                                    $lng = (float)$dataLocation['longitude'];
                                    $customerCode = $dataLocation['customer_code'];
                                    $kurirCode = $dataLocation['kurir_code'];

                                    $key = "{$lat}|{$lng}|{$customerCode}|{$kurirCode}";
                                    if (!isset($seenKeys[$key])) {
                                        $seenKeys[$key] = true;
                                        $listLocation[] = [
                                            'lat' => $lat,
                                            'lng' => $lng,
                                            'label' => $customerCode . ' - ' . $dataLocation['customer_name'],
                                            'kurir' => $kurirCode . ' - ' . $dataLocation['kurir_name']
                                        ];
                                    }
                                }
                        ?>
                                <a class="add-button" href="<?=base_url()?>/delivery.php?type=create"><i class='bx bx-plus-circle'></i> Tambah</a>
                                <table id="record" class="display nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">Kode</th>
                                            <th style="text-align: left;">Kode Penjualan</th>
                                            <th style="text-align: left;">Kode Pelanggan</th>
                                            <th style="text-align: left;">Nama Pelanggan</th>
                                            <th style="text-align: left;">No. Hp Pelanggan</th>
                                            <th style="text-align: left;">Alamat Pelanggan</th>
                                            <th style="text-align: left;">Kode Kurir</th>
                                            <th style="text-align: left;">Nama Kurir</th>
                                            <th style="text-align: left;">No. Hp Kurir</th>
                                            <th style="text-align: left;">No. Plat Kendaraan</th>
                                            <th style="text-align: left;">Jadwal Pengantaran</th>
                                            <th style="text-align: left;">Waktu Berangkat</th>
                                            <th style="text-align: left;">Waktu Tiba</th>
                                            <th style="text-align: left;">Status</th>
                                            <th style="text-align: left;">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Kode"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Kode Penjualan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Kode Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Nama Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Hp Pelanggan"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Alamat Pelanggan"></th>
                                            <th><input type="text" style="width: 120px; height: 18px;" class="search-field" placeholder="Cari Kode Kurir"></th>
                                            <th><input type="text" style="width: 120px; height: 18px;" class="search-field" placeholder="Cari Nama Kurir"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Hp Kurir"></th>
                                            <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Plat Kendaraan"></th>
                                            <th><input type="text" style="width: 160px; height: 18px;" class="search-field" placeholder="Cari Jadwal Pengantaran"></th>
                                            <th></th>
                                            <th></th>
                                            <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Status"></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                                <script>
                                    $(window).on('load', function() {
                                        new DataTable('#record', {
                                            ajax: 'fetch.php?table=vw_delivery',
                                            fixedColumns: {
                                                start: 1,
                                                end: 1
                                            },
                                            scrollX: true,
                                            scrollCollapse: true,
                                            serverSide: false,
                                            responsive: true,
                                            order: [],
                                            columnDefs: [
                                                {
                                                    searchable: false,
                                                    orderable: false,
                                                    targets: 14,
                                                    render: function(data, type, row) {
                                                        let btn = '<center><a href=\'delivery.php?type=update&id='+data+'\' class=\'button-action\'><i class=\'bx bx-edit\'></i></a> <a href=\'delivery.php?type=delete&id='+data+'\' class=\'button-action\'><i class=\'bx bx-trash\'></i></a> <a href=\'delivery.php?type=read&id='+data+'\' class=\'button-action\'><i class=\'bx bx-bullseye\'></i></a></center>';
                                                        return btn;
                                                    }
                                                },
                                                {
                                                    targets: '_all',
                                                    orderable: false
                                                }
                                            ],
                                            rowCallback: function (row, data, index) {
                                                const status = data[13];
                                                const colorClassMap = {
                                                    WAIT: 'status-wait',
                                                    OTW: 'status-otw',
                                                    DONE: 'status-done',
                                                    CANCEL: 'status-cancel'
                                                };

                                                $(row).removeClass('status-wait status-otw status-done status-cancel');

                                                const className = colorClassMap[status] || '';

                                                if (className) {
                                                    $(row).addClass(className);

                                                    const rowIndex = index + 1;
                                                    const fixedLeftRow = $('.DTFC_LeftWrapper .DTFC_Cloned tbody tr').eq(rowIndex);
                                                    const fixedRightRow = $('.DTFC_RightWrapper .DTFC_Cloned tbody tr').eq(rowIndex);

                                                    fixedLeftRow.removeClass('status-wait status-otw status-done status-cancel').addClass(className);
                                                    fixedRightRow.removeClass('status-wait status-otw status-done status-cancel').addClass(className);
                                                }
                                            },
                                            initComplete: function () {
                                                this.api()
                                                    .columns()
                                                    .every(function () {
                                                        let column = this;
                                                        $('input', column.header()).on('keyup change clear', function () {
                                                            if (column.search() !== this.value) {
                                                                column
                                                                    .search(this.value)
                                                                    .draw(false);
                                                                column.table().page('first').draw(false);
                                                            }
                                                        });
                                                    });
                                            }
                                        });
                                    });
                                </script>
                        <?php
                            }
                        ?>
                        <script>
                            const listLocation = <?=json_encode($listLocation)?>
                        </script>
                        <?php
                            if (!empty($listLocation)) {
                        ?>
                                <br>
                        <?php
                                if ($_SESSION['role'] == 'KURIR') {
                        ?>
                                    <table class="detail-dlv">
                                        <tr>
                                            <th colspan="2">Keterangan Rute</th>
                                        </tr>
                                        <tr>
                                            <td class="val"><div class="color-box" style="background-color: green;"></div></td>
                                            <td class="key">Ke-1 Paling Dekat</td>
                                        </tr>
                                        <tr>
                                            <td class="val"><div class="color-box" style="background-color: yellow;"></div></td>
                                            <td class="key">Ke-2 Paling Dekat</td>
                                        </tr>
                                        <tr>
                                            <td class="val"><div class="color-box" style="background-color: red;"></div></td>
                                            <td class="key">Ke-3 Paling Dekat</td>
                                        </tr>
                                        <tr>
                                            <td class="val"><div class="color-box" style="background-color: blue;"></div></td>
                                            <td class="key">Tidak Dekat</td>
                                        </tr>
                                    </table>
                        <?php
                                }
                        ?>
                                <div id="map"></div>
                                <table class="detail-dlv">
                                    <tr>
                                        <th colspan="3">Detail Pengantaran</th>
                                    </tr>
                                    <?php
                                        foreach ($listLocation as $itemLocation) {
                                    ?>
                                            <tr>
                                                <td class="key"><?=$itemLocation['label']?></td>
                                                <td class="val" style="width: fit-content; white-space: nowrap;"><a href="https://www.google.com/maps?q=<?=$itemLocation['lat']?>,<?=$itemLocation['lng']?>" target="_blank">Google Maps</a></td>
                                                <td class="key"><?=$itemLocation['kurir']?></td>
                                            </tr>
                                    <?php
                                        }
                                    ?>
                                </table>
                        <?php
                            }
                        ?>
                    </section>
                </main>

                <?php
                    if (!empty($listLocation)) {
                        if ($sourceMAP == 'GOOGLE') {
                ?>
                            <script>
                                let map;
                                let userMarker;
                                let directionsService;
                                let directionsRenderers = [];
                                const destinations = listLocation;

                                function initMap() {
                                    map = new google.maps.Map(document.getElementById('map'), {
                                        zoom: 14,
                                        center: { lat: 3.5952, lng: 98.6722 }
                                    });

                                    directionsService = new google.maps.DirectionsService();

                                    destinations.forEach(dest => {
                                        new google.maps.Marker({
                                            position: dest,
                                            map: map,
                                            label: dest.label,
                                        });
                                    });

                                    if (navigator.geolocation) {
                                        navigator.geolocation.watchPosition(async position => {
                                            const pos = {
                                                lat: position.coords.latitude,
                                                lng: position.coords.longitude
                                            };

                                            if (!userMarker) {
                                                userMarker = new google.maps.Marker({
                                                    position: pos,
                                                    map: map,
                                                    icon: 'https://img.icons8.com/?size=25&id=oWwF0H4PdHkh&format=png&color=000000',
                                                    title: "Lokasi Saya"
                                                });
                                            } else {
                                                userMarker.setPosition(pos);
                                            }

                                            map.setCenter(pos);

                                            <?php if ($_SESSION['role'] == 'KURIR') : ?>
                                            directionsRenderers.forEach(r => r.setMap(null));
                                            directionsRenderers = [];

                                            const routesWithDistance = await Promise.all(destinations.map(dest => {
                                                return new Promise((resolve, reject) => {
                                                    directionsService.route({
                                                        origin: pos,
                                                        destination: { lat: dest.lat, lng: dest.lng },
                                                        travelMode: google.maps.TravelMode.DRIVING
                                                    }, (response, status) => {
                                                        if (status === 'OK') {
                                                            const distance = response.routes[0].legs[0].distance.value;
                                                            resolve({
                                                                dest,
                                                                response,
                                                                distance
                                                            });
                                                        } else {
                                                            console.error('Gagal ambil route:', status);
                                                            resolve(null);
                                                        }
                                                    });
                                                });
                                            }));

                                            const sortedRoutes = routesWithDistance
                                                .filter(r => r !== null)
                                                .sort((a, b) => a.distance - b.distance);

                                            const colors = ['green', 'yellow', 'red'];
                                            sortedRoutes.forEach((r, index) => {
                                                const color = index < 3 ? colors[index] : 'blue';

                                                const renderer = new google.maps.DirectionsRenderer({
                                                    map: map,
                                                    directions: r.response,
                                                    polylineOptions: {
                                                        strokeColor: color,
                                                        strokeWeight: 6,
                                                        strokeOpacity: 0.8
                                                    },
                                                    suppressMarkers: true
                                                });

                                                directionsRenderers.push(renderer);
                                            });
                                            <?php endif; ?>

                                        }, () => {
                                            alert("Gagal mendeteksi lokasi.");
                                        });
                                    } else {
                                        alert("Browser tidak mendukung geolokasi.");
                                    }
                                }
                            </script>
                <?php
                        } else if ($sourceMAP == 'OPENSTREET') {
                ?>
                            <script>
                                const Omap = L.map('map').setView([3.5952, 98.6722], 13);

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© OpenStreetMap contributors'
                                }).addTo(Omap);

                                const tujuan = listLocation;

                                tujuan.forEach((t, index) => {
                                    const marker = L.marker([t.lat, t.lng]).addTo(Omap);
                                    marker.bindPopup(`<b>${t.label}</b><br>${t.address || ''}`);
                                });

                                const apiKey = "<?=$heigitBASICkey?>";
                                let routeLines = [];
                                let OuserMarker = null;
                                let latestCoords = null;
                                let isDrawing = false;
                                let abortController = null;
                                let routeUpdateInterval = null;

                                let performanceStats = {
                                    cacheHits: 0,
                                    apiCalls: 0,
                                    totalRoutes: 0,
                                    totalTime: 0
                                };

                                async function drawRoutes(startLat, startLng) {
                                    if (isDrawing) {
                                        console.log("Already drawing routes, skipping...");
                                        return;
                                    }
                                    
                                    isDrawing = true;
                                    console.log("Starting route drawing with caching...");
                                    
                                    if (abortController) {
                                        abortController.abort();
                                    }
                                    abortController = new AbortController();

                                    routeLines.forEach(line => Omap.removeLayer(line));
                                    routeLines = [];

                                    const batchStats = {
                                        cacheHits: 0,
                                        apiCalls: 0,
                                        errors: 0,
                                        startTime: Date.now()
                                    };

                                    const tujuanWithDistance = tujuan.map((t) => {
                                        const distance = Math.sqrt(
                                            Math.pow(t.lat - startLat, 2) + 
                                            Math.pow(t.lng - startLng, 2)
                                        );
                                        return { ...t, distance };
                                    });

                                    tujuanWithDistance.sort((a, b) => a.distance - b.distance);

                                    const specialColors = ['green', 'yellow', 'blue'];
                                    const batchSize = 5;

                                    for (let i = 0; i < tujuanWithDistance.length; i += batchSize) {
                                        const batch = tujuanWithDistance.slice(i, i + batchSize);
                                        
                                        console.log(`Processing batch ${Math.floor(i/batchSize) + 1}/${Math.ceil(tujuanWithDistance.length/batchSize)}`);
                                        
                                        const promises = batch.map(async (t, batchIndex) => {
                                            const globalIndex = i + batchIndex;
                                            
                                            try {
                                                const routeStart = Date.now();
                                                const url = `utils/caching.php?startLat=${startLat}&startLng=${startLng}&endLat=${t.lat}&endLng=${t.lng}`;
                                                
                                                const response = await fetch(url, {
                                                    signal: abortController.signal
                                                });

                                                if (!response.ok) {
                                                    if (response.status === 429) {
                                                        console.warn("Rate limit hit, waiting 5 seconds...");
                                                        await new Promise(resolve => setTimeout(resolve, 5000));
                                                        batchStats.errors++;
                                                        return null;
                                                    }
                                                    throw new Error(`HTTP ${response.status}`);
                                                }

                                                const json = await response.json();
                                                const elapsed = Date.now() - routeStart;
                                                
                                                if (json.cached) {
                                                    batchStats.cacheHits++;
                                                    console.log(`Cache hit: ${t.label} (${elapsed}ms)`);
                                                } else {
                                                    batchStats.apiCalls++;
                                                    console.log(`API call: ${t.label} (${elapsed}ms)`);
                                                }

                                                const route = json.routes[0].geometry;
                                                const decodedRoute = L.Polyline.fromEncoded(route).getLatLngs();

                                                let color = globalIndex < 3 ? specialColors[globalIndex] : 'red';

                                                const polyline = L.polyline(decodedRoute, {
                                                    color: color,
                                                    weight: 5,
                                                    opacity: 0.8
                                                }).addTo(Omap);

                                                const distanceKm = (json.routes[0].summary.distance / 1000).toFixed(2);
                                                const durationMin = Math.round(json.routes[0].summary.duration / 60);
                                                
                                                polyline.bindPopup(
                                                    `<b>${t.label}</b><br>` +
                                                    `${distanceKm} km<br>` +
                                                    `${durationMin} menit`
                                                );

                                                routeLines.push(polyline);
                                                
                                                return true;
                                            } catch (error) {
                                                if (error.name === 'AbortError') {
                                                    console.log("Request aborted");
                                                } else {
                                                    console.error(`Error for ${t.label}:`, error);
                                                    batchStats.errors++;
                                                }
                                                return null;
                                            }
                                        });

                                        await Promise.all(promises);
                                        
                                        if (i + batchSize < tujuanWithDistance.length) {
                                            await new Promise(resolve => setTimeout(resolve, 1000));
                                        }
                                    }

                                    batchStats.totalTime = Date.now() - batchStats.startTime;

                                    performanceStats.cacheHits += batchStats.cacheHits;
                                    performanceStats.apiCalls += batchStats.apiCalls;
                                    performanceStats.totalRoutes += tujuanWithDistance.length;
                                    performanceStats.totalTime += batchStats.totalTime;

                                    const cacheRate = tujuanWithDistance.length > 0 
                                        ? Math.round(batchStats.cacheHits / tujuanWithDistance.length * 100) 
                                        : 0;
                                    const avgTime = tujuanWithDistance.length > 0 
                                        ? Math.round(batchStats.totalTime / tujuanWithDistance.length) 
                                        : 0;
                                    const overallCacheRate = performanceStats.totalRoutes > 0 
                                        ? Math.round(performanceStats.cacheHits / performanceStats.totalRoutes * 100) 
                                        : 0;

                                    console.log(`
Batch Performance:
   Cache Hits: ${batchStats.cacheHits}
   API Calls: ${batchStats.apiCalls}
   Errors: ${batchStats.errors}
   Total Time: ${batchStats.totalTime}ms
   Avg/Route: ${avgTime}ms
   Cache Rate: ${cacheRate}%

Overall Stats:
   Total Routes: ${performanceStats.totalRoutes}
   Total Cache Hits: ${performanceStats.cacheHits}
   Total API Calls: ${performanceStats.apiCalls}
   Avg Cache Rate: ${overallCacheRate}%
                                    `);

                                    isDrawing = false;
                                }

                                if (navigator.geolocation) {
                                    navigator.geolocation.watchPosition(
                                        pos => {
                                            const lat = pos.coords.latitude;
                                            const lng = pos.coords.longitude;

                                            latestCoords = { lat, lng };

                                            if (OuserMarker) {
                                                OuserMarker.setLatLng([lat, lng]);
                                            } else {
                                                OuserMarker = L.marker([lat, lng], {
                                                    icon: L.icon({
                                                        iconUrl: 'https://img.icons8.com/?size=25&id=oWwF0H4PdHkh&format=png&color=000000',
                                                        iconSize: [32, 32],
                                                        iconAnchor: [16, 32]
                                                    })
                                                }).addTo(Omap).bindPopup("Lokasi Saya").openPopup();
                                                
                                                <?php if ($_SESSION['role'] == 'KURIR') : ?>
                                                    drawRoutes(lat, lng);
                                                <?php endif; ?>
                                            }
                                            
                                            Omap.setView([lat, lng], 13);
                                        },
                                        error => {
                                            console.error("Geolocation error:", error);
                                            alert("Gagal mengambil lokasi. Pastikan GPS aktif dan izin diberikan.");
                                        },
                                        {
                                            enableHighAccuracy: true,
                                            timeout: 10000,
                                            maximumAge: 30000
                                        }
                                    );

                                    <?php if ($_SESSION['role'] == 'KURIR') : ?>
                                        document.addEventListener('visibilitychange', () => {
                                            if (document.hidden) {
                                                console.log("Tab hidden, pausing updates");
                                                if (routeUpdateInterval) {
                                                    clearInterval(routeUpdateInterval);
                                                    routeUpdateInterval = null;
                                                }
                                            } else {
                                                console.log("Tab visible, resuming updates");
                                                if (!routeUpdateInterval && latestCoords) {
                                                    drawRoutes(latestCoords.lat, latestCoords.lng);
                                                    
                                                    routeUpdateInterval = setInterval(() => {
                                                        if (latestCoords && !document.hidden) {
                                                            console.log("Auto-refresh routes");
                                                            drawRoutes(latestCoords.lat, latestCoords.lng);
                                                        }
                                                    }, 300000);
                                                }
                                            }
                                        });
                                        
                                        if (!document.hidden) {
                                            routeUpdateInterval = setInterval(() => {
                                                if (latestCoords && !document.hidden) {
                                                    console.log("Auto-refresh routes");
                                                    drawRoutes(latestCoords.lat, latestCoords.lng);
                                                }
                                            }, 300000);
                                        }
                                    <?php endif; ?>
                                } else {
                                    alert("Browser tidak mendukung geolokasi.");
                                }
                            </script>
                <?php
                        }
                    }
                ?>

                <script>
                    $(document).on('click', '#submitStatus', function() {
                        let updates = [];
                        $('.status-select').each(function() {
                            const code = $(this).data('code');
                            const status = $(this).val();
                            updates.push({ code, status });
                        });

                        const $btn = $('#submitStatus');
                        $btn.prop('disabled', true);
                        $btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`);

                        $.ajax({
                            url: 'update_status.php',
                            method: 'POST',
                            data: { updates: JSON.stringify(updates) },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Status Diperbarui',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location = 'delivery.php';
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Status Gagal Diperbarui',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location = 'delivery.php';
                                });
                            }
                        });
                    });
                </script>
        <?php
            }
        ?>

        <?php
            include "components/afterLoad.php";
        ?>
    </body>
</html>
<?php
    } else {
        echo "<script>window.location='".base_url()."/login.php';</script>";
    }
?>