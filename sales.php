<?php
    require "connect.php";

    if (isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
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
            if (isset($_GET['type']) && $_GET['type'] == 'create') {
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/create.php" enctype="multipart/form-data">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="Generate By System" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Pelanggan</label>
                                <select name="customer" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Pelanggan</option>
                                    <?php
                                        $dropdownCust = mysqli_query($con, "SELECT * FROM tbl_customer");
                                        while ($listCust = mysqli_fetch_array($dropdownCust)) {
                                            echo "<option value=\"".$listCust['code']."\">".$listCust['code']." - ".$listCust['name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Mulai Tanggal</label>
                                <input type="date" name="start_periode" required>
                            </div>
                            <div class="form-input">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_periode" required>
                            </div>
                            <div class="form-input">
                                <label>Total Harga</label>
                                <input type="text" name="total_price" placeholder="Total Harga" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Total Qty/Box</label>
                                <input type="text" name="total_qty_box" placeholder="Total Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="create-sales"><i class='bx bx-save'></i> Create</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'update' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qSales = mysqli_query($con, "SELECT * FROM vw_sales WHERE id = $id LIMIT 1");
                $dataSales = mysqli_fetch_array($qSales);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/update.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataSales['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Pelanggan</label>
                                <select name="customer" class="searchable" required>
                                    <option value="" disabled selected style="display:none;">Pelanggan</option>
                                    <?php
                                        $dropdownCust = mysqli_query($con, "SELECT * FROM tbl_customer");
                                        while ($listCust = mysqli_fetch_array($dropdownCust)) {
                                            $selected = ($listCust['code'] == $dataSales['customer_code']) ? 'selected' : '';
                                            echo "<option value=\"".$listCust['code']."\" $selected>".$listCust['code']." - ".$listCust['name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>Mulai Tanggal</label>
                                <input type="date" name="start_periode" value="<?=$dataSales['start_periode']?>" required>
                            </div>
                            <div class="form-input">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_periode" value="<?=$dataSales['end_periode']?>" required>
                            </div>
                            <div class="form-input">
                                <label>Total Harga</label>
                                <input type="text" name="total_price" value="<?=$dataSales['total_price']?>" placeholder="Total Harga" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Total Qty/Box</label>
                                <input type="text" name="total_qty_box" value="<?=$dataSales['total_qty_box']?>" placeholder="Total Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="update-sales"><i class='bx bx-edit'></i> Update</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qSales = mysqli_query($con, "SELECT * FROM vw_sales WHERE id = $id LIMIT 1");
                $dataSales = mysqli_fetch_array($qSales);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/delete.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataSales['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Pelanggan</label>
                                <input type="text" name="customer" maxlength="100" value="<?=$dataSales['customer_code']?> - <?=$dataSales['customer_name']?>" placeholder="Pelanggan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Mulai Tanggal</label>
                                <input type="date" name="start_periode" value="<?=$dataSales['start_periode']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_periode" value="<?=$dataSales['end_periode']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Total Harga</label>
                                <input type="text" name="total_price" value="<?=$dataSales['total_price']?>" placeholder="Total Harga" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Total Qty/Box</label>
                                <input type="text" name="total_qty_box" value="<?=$dataSales['total_qty_box']?>" placeholder="Total Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="delete-sales"><i class='bx bx-trash'></i> Delete</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'read' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qSales = mysqli_query($con, "SELECT * FROM vw_sales WHERE id = $id LIMIT 1");
                $dataSales = mysqli_fetch_array($qSales);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataSales['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Pelanggan</label>
                                <input type="text" name="customer" maxlength="100" value="<?=$dataSales['customer_code']?> - <?=$dataSales['customer_name']?>" placeholder="Pelanggan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9()- ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Mulai Tanggal</label>
                                <input type="date" name="start_periode" value="<?=$dataSales['start_periode']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_periode" value="<?=$dataSales['end_periode']?>" disabled>
                            </div>
                            <div class="form-input">
                                <label>Total Harga</label>
                                <input type="text" name="total_price" value="<?=$dataSales['total_price']?>" placeholder="Total Harga" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Total Qty/Box</label>
                                <input type="text" name="total_qty_box" value="<?=$dataSales['total_qty_box']?>" placeholder="Total Qty/Box" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else {
        ?>
                <main>
                    <section class="container section section__height">
                        <h2 class="section__title">Penjualan</h2>
                        <a class="add-button" href="<?=base_url()?>/sales.php?type=create"><i class='bx bx-plus-circle'></i> Tambah</a>
                        <table id="record" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Waktu Transaksi</th>
                                    <th>Mulai Tanggal</th>
                                    <th>Sampai Tanggal</th>
                                    <th>Total Harga</th>
                                    <th>Total Qty/Box</th>
                                    <th>Sisa Qty/Box</th>
                                    <th>Aksi</th>
                                </tr>
                                <tr>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Kode"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Kode Pelanggan"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Nama Pelanggan"></th>
                                    <th><input type="text" style="width: 130px; height: 18px;" class="search-field" placeholder="Cari Waktu Transaksi"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Mulai"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Sampai"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Total Harga"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Total Qty/Box"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari Sisa Qty/Box"></th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                        <script>
                            $(window).on('load', function() {
                                new DataTable('#record', {
                                    ajax: 'fetch.php?table=vw_sales',
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
                                            targets: 9,
                                            render: function(data, type, row) {
                                                let btn = '<center><a href=\'sales.php?type=update&id='+data+'\' class=\'button-action\'><i class=\'bx bx-edit\'></i></a> <a href=\'sales.php?type=delete&id='+data+'\' class=\'button-action\'><i class=\'bx bx-trash\'></i></a> <a href=\'sales.php?type=read&id='+data+'\' class=\'button-action\'><i class=\'bx bx-bullseye\'></i></a> <a href=\'process/print.php?type=sls&id='+data+'\' class=\'button-action\'><i class=\'bx bxs-file-blank\'></i></a></center>';
                                                return btn;
                                            }
                                        },
                                        {
                                            targets: '_all',
                                            orderable: false
                                        }
                                    ],
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
                    </section>
                </main>
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