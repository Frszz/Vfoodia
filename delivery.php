<?php
    require "connect.php";
    require "utils/routing.php";

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
                            $listLocation = [];
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
                            const statusOptions = <?=json_encode($optionStatus)?>;
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
                                            'kurir' => $kurirCode . ' - ' . $dataLocation['kurir_name'],
                                            'address' => $dataLocation['customer_address']
                                        ];
                                    }
                                }
                        ?>
                                <?php if (!empty($listLocation)) : ?>

                                <?php endif; ?>
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
                                // Reset listLocation for ADMIN view
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
                            const listLocation = <?=json_encode($listLocation)?>;
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
                                            <td class="val"><div class="color-box" style="background-color: red;"></div></td>
                                            <td class="key">Rute Pengantaran</td>
                                        </tr>
                                    </table>
                        <?php
                                }
                        ?>
                                <div class="custom-route-container">
                                    <h3><i class='bx bx-map'></i> Custom Rute Pengantaran</h3>
                                    <div class="route-selection-grid">
                                        <div class="route-select-group">
                                            <label>Lokasi Awal:</label>
                                            <select id="startLocation" class="searchable">
                                                <option value="">Pilih Lokasi Awal</option>
                                                <?php foreach ($listLocation as $loc) : ?>
                                                    <option value="<?=$loc['lat']?>,<?=$loc['lng']?>" 
                                                            data-name="<?=htmlspecialchars(explode(' - ', $loc['label'])[1] ?? $loc['label'])?>">
                                                        <?=$loc['label']?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="route-select-group">
                                            <label>Lokasi Tujuan:</label>
                                            <select id="endLocation" class="searchable">
                                                <option value="">Pilih Lokasi Tujuan</option>
                                                <?php foreach ($listLocation as $loc) : ?>
                                                    <option value="<?=$loc['lat']?>,<?=$loc['lng']?>"
                                                            data-name="<?=htmlspecialchars(explode(' - ', $loc['label'])[1] ?? $loc['label'])?>">
                                                        <?=$loc['label']?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button id="btnCariRute" class="btn-route-action btn-route-cari">
                                            Cari
                                        </button>
                                        <button id="btnResetRute" class="btn-route-action btn-route-reset">
                                            Reset
                                        </button>
                                    </div>
                                    <div id="routeResult" class="route-result-box" style="display: none;">
                                        <strong>Rute Terdekat:</strong> <span id="nearestLocationText"></span>
                                    </div>
                                </div>
                                <div id="map" style="position: relative;">
                                    <div id="routeInfoOverlay" style="display: none; position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; border: 1px solid #ddd; max-width: 350px; max-height: 80vh; overflow-y: auto;">
                                        <div style="padding: 10px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; background: #f5f5f5;">
                                            <div>
                                                <h3 style="margin: 0; font-size: 14px; font-weight: bold; color: #333;">Informasi Rute</h3>
                                                <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;" id="routeInfoSubtitle">Urutan Pengantaran Hari Ini</p>
                                            </div>
                                            <button id="closeRouteInfo" style="background: #e0e0e0; border: none; color: #333; width: 24px; height: 24px; cursor: pointer; font-size: 14px;">
                                                ✕
                                            </button>
                                        </div>
                                        <div style="padding: 10px;">
                                            <div id="routeTotalDistance" style="background: #f0f0f0; padding: 8px; margin-bottom: 10px; text-align: center; font-weight: bold; color: #333; font-size: 13px;">
                                                Total: <span id="totalDistanceValue">-</span> km
                                            </div>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;" id="routeInfoTable">
                                                <thead>
                                                    <tr style="background: #f9f9f9; border-bottom: 1px solid #ddd;">
                                                        <th style="padding: 6px 4px; text-align: center; width: 30px;">No</th>
                                                        <th style="padding: 6px 8px; text-align: left;">Lokasi</th>
                                                        <th style="padding: 6px 8px; text-align: right;">Jarak</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="routeInfoTableBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <button id="toggleRouteInfo" style="display: none; position: absolute; top: 10px; right: 10px; z-index: 999; background: #4285F4; color: white; border: 1px solid #3367D6; padding: 8px 16px; cursor: pointer; font-weight: 500; font-size: 13px;">
                                        Lihat Rute
                                    </button>
                                </div>
                                <table class="detail-dlv" id="delivery-details">
                                    <tr>
                                        <th colspan="4">Detail Pengantaran</th>
                                    </tr>
                                    <tr id="route-info-row" style="display: none;">
                                        <td colspan="4" style="background-color: #e3f2fd; padding: 10px; font-weight: bold; text-align: center;" id="route-summary">
                                            Calculating optimized route...
                                        </td>
                                    </tr>
                                    <?php
                                        foreach ($listLocation as $index => $itemLocation) {
                                    ?>
                                            <tr class="delivery-row" data-lat="<?=$itemLocation['lat']?>" data-lng="<?=$itemLocation['lng']?>">
                                                <td class="sequence-col" style="width: 40px; text-align: center; font-weight: bold;">-</td>
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

                                // Haversine formula
                                function calculateDistance(lat1, lng1, lat2, lng2) {
                                    const R = 6371;
                                    const dLat = (lat2 - lat1) * Math.PI / 180;
                                    const dLng = (lng2 - lng1) * Math.PI / 180;
                                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                                            Math.sin(dLng/2) * Math.sin(dLng/2);
                                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                    return R * c;
                                }

                                function optimizeRoute(startLat, startLng, destinations) {
                                    if (destinations.length === 0) return [];
                                    if (destinations.length === 1) {
                                        destinations[0].sequence = 1;
                                        destinations[0].distanceFromPrev = calculateDistance(startLat, startLng, destinations[0].lat, destinations[0].lng);
                                        return destinations;
                                    }

                                    const visited = [];
                                    const optimized = [];
                                    let current = { lat: startLat, lng: startLng };
                                    let sequence = 1;

                                    while (visited.length < destinations.length) {
                                        let nearestIdx = -1;
                                        let minDist = Infinity;

                                        destinations.forEach((dest, idx) => {
                                            if (visited.includes(idx)) return;
                                            const dist = calculateDistance(current.lat, current.lng, dest.lat, dest.lng);
                                            if (dist < minDist) {
                                                minDist = dist;
                                                nearestIdx = idx;
                                            }
                                        });

                                        if (nearestIdx !== -1) {
                                            visited.push(nearestIdx);
                                            const destination = { ...destinations[nearestIdx] };
                                            destination.sequence = sequence;
                                            destination.distanceFromPrev = minDist;
                                            optimized.push(destination);
                                            current = { lat: destination.lat, lng: destination.lng };
                                            sequence++;
                                        }
                                    }

                                    return optimized;
                                }

                                let tujuan = listLocation;
                                const destinationMarkers = {};
                                
                                tujuan.forEach((t, index) => {
                                    const marker = L.marker([t.lat, t.lng]).addTo(Omap);
                                    marker.bindPopup(`<b>${t.label}</b><br>${t.address || 'Memuat informasi jarak...'}`);
                                    destinationMarkers[`${t.lat},${t.lng}`] = marker;
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

                                    const tujuanWithDistance = optimizeRoute(startLat, startLng, tujuan);
                                    
                                    console.log("Optimized route order:");
                                    tujuanWithDistance.forEach(dest => {
                                        console.log(`${dest.sequence}. ${dest.label} - ${dest.distanceFromPrev.toFixed(2)} km from previous`);
                                    });
                                    Object.values(destinationMarkers).forEach(marker => Omap.removeLayer(marker));
                                    
                                    tujuanWithDistance.forEach((dest) => {
                                        const customIcon = L.divIcon({
                                            className: 'custom-marker',
                                            html: `<div style="background-color: #4285F4; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">${dest.sequence}</div>`,
                                            iconSize: [32, 32],
                                            iconAnchor: [16, 16]
                                        });
                                        
                                        const marker = L.marker([dest.lat, dest.lng], { icon: customIcon }).addTo(Omap);
                                        marker.bindPopup(`
                                            <b>Stop ${dest.sequence}: ${dest.label}</b><br>
                                            Distance from previous: ${dest.distanceFromPrev.toFixed(2)} km<br>
                                            ${dest.address || ''}
                                        `);
                                        destinationMarkers[`${dest.lat},${dest.lng}`] = marker;
                                    });

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

                                                const polyline = L.polyline(decodedRoute, {
                                                    color: 'red',
                                                    weight: 5,
                                                    opacity: 0.8
                                                }).addTo(Omap);

                                                const distanceM = json.routes[0].summary.distance;
                                                const distanceKm = (distanceM / 1000).toFixed(2);
                                                const durationMin = Math.round(json.routes[0].summary.duration / 60);
                                                
                                                polyline.bindPopup(
                                                    `<b>${t.label}</b><br>` +
                                                    `<b>Jarak:</b> ${distanceM.toLocaleString('id-ID')} meter (${distanceKm} km)<br>` +
                                                    `<b>Waktu:</b> ${durationMin} menit`
                                                );

                                                const markerKey = `${t.lat},${t.lng}`;
                                                if (destinationMarkers[markerKey]) {
                                                    destinationMarkers[markerKey].bindPopup(
                                                        `<b>${t.label}</b><br>` +
                                                        `<b>Jarak:</b> ${distanceM.toLocaleString('id-ID')} meter (${distanceKm} km)<br>` +
                                                        `<b>Waktu:</b> ${durationMin} menit`
                                                    );
                                                }

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

                                    updateDeliveryTable(tujuanWithDistance);

                                    isDrawing = false;
                                }

                                function updateDeliveryTable(optimizedDestinations) {
                                    const rows = document.querySelectorAll('#delivery-details .delivery-row');
                                    
                                    const locationMap = new Map();
                                    optimizedDestinations.forEach(dest => {
                                        const key = `${dest.lat},${dest.lng}`;
                                        locationMap.set(key, dest);
                                    });

                                    rows.forEach(row => {
                                        const lat = row.getAttribute('data-lat');
                                        const lng = row.getAttribute('data-lng');
                                        const key = `${lat},${lng}`;
                                        const dest = locationMap.get(key);
                                        
                                        if (dest) {
                                            const sequenceCol = row.querySelector('.sequence-col');
                                            sequenceCol.textContent = dest.sequence;
                                            sequenceCol.style.backgroundColor = '#4285F4';
                                            sequenceCol.style.color = 'white';
                                        }
                                    });

                                    const totalDistance = optimizedDestinations.reduce((sum, dest) => sum + dest.distanceFromPrev, 0);
                                    
                                    const routeInfoRow = document.getElementById('route-info-row');
                                    if (routeInfoRow) {
                                        routeInfoRow.style.display = 'none';
                                    }

                                    updateRouteInfoOverlay(optimizedDestinations);
                                }
                                
                                function updateRouteInfoOverlay(optimizedDestinations) {
                                    const tableBody = document.getElementById('routeInfoTableBody');
                                    const totalDistanceValue = document.getElementById('totalDistanceValue');
                                    const toggleButton = document.getElementById('toggleRouteInfo');
                                    
                                    if (!tableBody) return;
                                    
                                    tableBody.innerHTML = '';
                                    
                                    let cumulativeDistance = 0;
                                    
                                    function numberToAlphabet(num) {
                                        return String.fromCharCode(64 + num);
                                    }
                                    
                                    optimizedDestinations.forEach((dest, index) => {
                                        cumulativeDistance += dest.distanceFromPrev;
                                        
                                        const row = document.createElement('tr');
                                        row.style.borderBottom = '1px solid #e0e0e0';
                                        
                                        const customerName = dest.label.split(' - ')[1] || dest.label;
                                        const customerCode = dest.label.split(' - ')[0] || '';
                                        const alphabet = numberToAlphabet(dest.sequence);
                                        
                                        row.innerHTML = `
                                            <td style="padding: 8px 4px; text-align: center; font-weight: bold; color: #333;">${alphabet}</td>
                                            <td style="padding: 8px;">
                                                <div style="font-weight: 500; font-size: 13px; color: #333;">${customerName}</div>
                                                <div style="font-size: 11px; color: #666; margin-top: 2px;">${customerCode}</div>
                                            </td>
                                            <td style="padding: 8px; text-align: right; font-weight: 500; color: #333; white-space: nowrap;">
                                                ${cumulativeDistance.toFixed(1)} km
                                            </td>
                                        `;
                                        
                                        tableBody.appendChild(row);
                                    });
                                    
                                    totalDistanceValue.textContent = cumulativeDistance.toFixed(1);
                                    
                                    if (optimizedDestinations.length > 0 && toggleButton) {
                                        toggleButton.style.display = 'block';
                                    }
                                }
                                
                                $(document).on('click', '#toggleRouteInfo', function() {
                                    $('#routeInfoOverlay').fadeIn(300);
                                    $(this).fadeOut(300);
                                });
                                
                                $(document).on('click', '#closeRouteInfo', function() {
                                    $('#routeInfoOverlay').fadeOut(300);
                                    $('#toggleRouteInfo').fadeIn(300);
                                });

                                const overlayElement = document.getElementById('routeInfoOverlay');
                                if (overlayElement) {
                                    overlayElement.addEventListener('wheel', function(e) {
                                        e.stopPropagation();
                                    }, { passive: false });
                                    
                                    overlayElement.addEventListener('touchmove', function(e) {
                                        e.stopPropagation();
                                    }, { passive: false });
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
                                                    // Only auto-draw if no custom route is active
                                                    if (!customRouteActive) {
                                                        drawRoutes(lat, lng);
                                                    }
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
                                                if (!routeUpdateInterval && latestCoords && !customRouteActive) {
                                                    drawRoutes(latestCoords.lat, latestCoords.lng);
                                                    
                                                    routeUpdateInterval = setInterval(() => {
                                                        if (latestCoords && !document.hidden && !customRouteActive) {
                                                            console.log("Auto-refresh routes");
                                                            drawRoutes(latestCoords.lat, latestCoords.lng);
                                                        }
                                                    }, 300000);
                                                }
                                            }
                                        });
                                        
                                        if (!document.hidden) {
                                            routeUpdateInterval = setInterval(() => {
                                                if (latestCoords && !document.hidden && !customRouteActive) {
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
                    let originalDestinations = typeof listLocation !== 'undefined' ? [...listLocation] : [];
                    let customRouteActive = false;

                    function filterDestinationsByRoute(startLat, startLng, endLat, endLng, allDestinations) {
                        if (!startLat || !startLng || !endLat || !endLng) {
                            return allDestinations;
                        }

                        function calculateBearing(lat1, lng1, lat2, lng2) {
                            const dLon = (lng2 - lng1) * Math.PI / 180;
                            const y = Math.sin(dLon) * Math.cos(lat2 * Math.PI / 180);
                            const x = Math.cos(lat1 * Math.PI / 180) * Math.sin(lat2 * Math.PI / 180) -
                                    Math.sin(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.cos(dLon);
                            const brng = Math.atan2(y, x);
                            return ((brng * 180 / Math.PI) + 360) % 360;
                        }

                        function distanceToLineSegment(pointLat, pointLng, startLat, startLng, endLat, endLng) {
                            const R = 6371; // Earth radius in km
                            
                            const lat1 = startLat * Math.PI / 180;
                            const lng1 = startLng * Math.PI / 180;
                            const lat2 = endLat * Math.PI / 180;
                            const lng2 = endLng * Math.PI / 180;
                            const latP = pointLat * Math.PI / 180;
                            const lngP = pointLng * Math.PI / 180;
                            
                            const distToStart = calculateDistance(pointLat, pointLng, startLat, startLng);
                            const distToEnd = calculateDistance(pointLat, pointLng, endLat, endLng);
                            const directDist = calculateDistance(startLat, startLng, endLat, endLng);
                            
                            return Math.min(distToStart, distToEnd, (distToStart + distToEnd - directDist) / 2);
                        }

                        const mainRouteDistance = calculateDistance(startLat, startLng, endLat, endLng);
                        const threshold = mainRouteDistance * 0.4; // 40% tolerance from direct route

                        const filtered = allDestinations.filter(dest => {
                            const distToLine = distanceToLineSegment(dest.lat, dest.lng, startLat, startLng, endLat, endLng);
                            const distFromStart = calculateDistance(startLat, startLng, dest.lat, dest.lng);
                            const distFromEnd = calculateDistance(dest.lat, dest.lng, endLat, endLng);
                            
                            // Destination should be:
                            // 1. within threshold distance from the direct route
                            // 2. not be the start or end point itself
                            return distToLine <= threshold && 
                                   distFromStart > 0.1 && 
                                   distFromEnd > 0.1 &&
                                   (distFromStart + distFromEnd) < (mainRouteDistance * 1.5);
                        });

                        return filtered;
                    }

                    $(document).on('click', '#btnCariRute', function() {
                        const startVal = $('#startLocation').val();
                        const endVal = $('#endLocation').val();

                        if (!startVal || !endVal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Pilih lokasi awal dan tujuan terlebih dahulu!',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        if (startVal === endVal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Lokasi awal dan tujuan tidak boleh sama!',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        const [startLat, startLng] = startVal.split(',').map(parseFloat);
                        const [endLat, endLng] = endVal.split(',').map(parseFloat);
                        const startName = $('#startLocation option:selected').data('name');
                        const endName = $('#endLocation option:selected').data('name');

                        Swal.fire({
                            title: 'Menghitung Rute...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const filteredDestinations = filterDestinationsByRoute(
                            startLat, startLng, endLat, endLng, originalDestinations
                        );

                        const destinationsWithEnd = [...filteredDestinations, {
                            lat: endLat,
                            lng: endLng,
                            label: endName + ' (Tujuan Akhir)',
                            kurir: filteredDestinations[0]?.kurir || ''
                        }];

                        if (typeof tujuan !== 'undefined') {
                            tujuan = destinationsWithEnd;
                        }
                        customRouteActive = true;

                        $('#routeResult').show();
                        $('#nearestLocationText').html(
                            `<strong>${startName}</strong> → ${filteredDestinations.length} lokasi pengantaran → <strong>${endName}</strong>`
                        );
                        
                        $('#routeInfoSubtitle').text(`Rute: ${startName} → ${endName}`);

                        if (typeof drawRoutes === 'function' && latestCoords) {
                            drawRoutes(startLat, startLng);
                        }

                        Swal.close();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Rute Ditemukan!',
                            html: `Ditemukan <strong>${filteredDestinations.length}</strong> lokasi pengantaran dalam rute ini`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });

                    $(document).on('click', '#btnResetRute', function() {
                        $('#startLocation').val('').trigger('change');
                        $('#endLocation').val('').trigger('change');
                        $('#routeResult').hide();
                        
                        if (typeof tujuan !== 'undefined') {
                            tujuan = [...originalDestinations];
                        }
                        customRouteActive = false;

                        $('#routeInfoOverlay').fadeOut(300);
                        $('#toggleRouteInfo').fadeIn(300);
                        
                        $('#routeInfoSubtitle').text('Urutan Pengantaran Hari Ini');

                        if (typeof drawRoutes === 'function' && latestCoords) {
                            drawRoutes(latestCoords.lat, latestCoords.lng);
                        }

                        Swal.fire({
                            icon: 'info',
                            title: 'Rute Direset',
                            text: 'Kembali ke rute normal',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });

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