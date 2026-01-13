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
                            <div class="upload">
                                <img class="preview" src="<?=base_url().'/assets/img/no-pp.webp'?>">
                                <div class="round">
                                    <input type="file" accept=".png, .jpg, .jpeg" name="photo">
                                    <input type="hidden" name="base64_photo" value="">
                                    <i class='bx bx-camera'></i>
                                </div>
                            </div>
                            <center><small><i>*Ukuran Maksimal File : 100kb</i></small></center>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="Generate By System" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Nama</label>
                                <input type="text" name="name" maxlength="100" placeholder="Nama" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" placeholder="No. Hp" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Alamat</label>
                                <textarea name="address" placeholder="Alamat" required></textarea>
                            </div>
                            <div class="form-input">
                                <label>Latitude</label>
                                <input type="text" name="latitude" maxlength="100" placeholder="Latitude" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Longitude</label>
                                <input type="text" name="longitude" maxlength="100" placeholder="Longitude" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Altitude</label>
                                <input type="text" name="altitude" maxlength="100" placeholder="Altitude" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="create-customer"><i class='bx bx-save'></i> Create</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'update' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qCustomer = mysqli_query($con, "SELECT * FROM tbl_customer WHERE id = $id LIMIT 1");
                $dataCustomer = mysqli_fetch_array($qCustomer);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/update.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataCustomer['photo']) ? $dataCustomer['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                                <div class="round">
                                    <input type="file" accept=".png, .jpg, .jpeg" name="photo">
                                    <input type="hidden" name="base64_photo" value="<?=$dataCustomer['photo']?>">
                                    <i class='bx bx-camera'></i>
                                </div>
                            </div>
                            <center><small><i>*Ukuran Maksimal File : 100kb</i></small></center>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataCustomer['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Nama</label>
                                <input type="text" name="name" maxlength="100" placeholder="Nama" value="<?=$dataCustomer['name']?>" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" placeholder="No. Hp" value="<?=$dataCustomer['nohp']?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" placeholder="Email" value="<?=$dataCustomer['email']?>" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Alamat</label>
                                <textarea name="address" placeholder="Alamat" required><?=$dataCustomer['address']?></textarea>
                            </div>
                            <div class="form-input">
                                <label>Latitude</label>
                                <input type="text" name="latitude" maxlength="100" placeholder="Latitude" value="<?=$dataCustomer['latitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Longitude</label>
                                <input type="text" name="longitude" maxlength="100" placeholder="Longitude" value="<?=$dataCustomer['longitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Altitude</label>
                                <input type="text" name="altitude" maxlength="100" placeholder="Altitude" value="<?=$dataCustomer['altitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="update-customer"><i class='bx bx-edit'></i> Update</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qCustomer = mysqli_query($con, "SELECT * FROM tbl_customer WHERE id = $id LIMIT 1");
                $dataCustomer = mysqli_fetch_array($qCustomer);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/delete.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataCustomer['photo']) ? $dataCustomer['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                            </div>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataCustomer['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Nama</label>
                                <input type="text" name="name" maxlength="100" placeholder="Nama" value="<?=$dataCustomer['name']?>" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" placeholder="No. Hp" value="<?=$dataCustomer['nohp']?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" placeholder="Email" value="<?=$dataCustomer['email']?>" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Alamat</label>
                                <textarea name="address" placeholder="Alamat" disabled><?=$dataCustomer['address']?></textarea>
                            </div>
                            <div class="form-input">
                                <label>Latitude</label>
                                <input type="text" name="latitude" maxlength="100" placeholder="Latitude" value="<?=$dataCustomer['latitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Longitude</label>
                                <input type="text" name="longitude" maxlength="100" placeholder="Longitude" value="<?=$dataCustomer['longitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Altitude</label>
                                <input type="text" name="altitude" maxlength="100" placeholder="Altitude" value="<?=$dataCustomer['altitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="delete-customer"><i class='bx bx-trash'></i> Delete</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'read' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qCustomer = mysqli_query($con, "SELECT * FROM tbl_customer WHERE id = $id LIMIT 1");
                $dataCustomer = mysqli_fetch_array($qCustomer);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataCustomer['photo']) ? $dataCustomer['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                            </div>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataCustomer['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Nama</label>
                                <input type="text" name="name" maxlength="100" placeholder="Nama" value="<?=$dataCustomer['name']?>" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" placeholder="No. Hp" value="<?=$dataCustomer['nohp']?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" placeholder="Email" value="<?=$dataCustomer['email']?>" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Alamat</label>
                                <textarea name="address" placeholder="Alamat" disabled><?=$dataCustomer['address']?></textarea>
                            </div>
                            <div class="form-input">
                                <label>Latitude</label>
                                <input type="text" name="latitude" maxlength="100" placeholder="Latitude" value="<?=$dataCustomer['latitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Longitude</label>
                                <input type="text" name="longitude" maxlength="100" placeholder="Longitude" value="<?=$dataCustomer['longitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Altitude</label>
                                <input type="text" name="altitude" maxlength="100" placeholder="Altitude" value="<?=$dataCustomer['altitude']?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" disabled>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else {
        ?>
                <main>
                    <section class="container section section__height">
                        <h2 class="section__title">Pelanggan</h2>
                        <a class="add-button" href="<?=base_url()?>/customer.php?type=create"><i class='bx bx-plus-circle'></i> Tambah</a>
                        <table id="record" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. Hp</th>
                                    <th>Sisa Qty/Box</th>
                                    <th>Alamat</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Altitude</th>
                                    <th>Aksi</th>
                                </tr>
                                <tr>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Kode"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Nama"></th>
                                    <th><input type="text" style="width: 130px; height: 18px;" class="search-field" placeholder="Cari Email"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari No. Hp"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Sisa"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Alamat"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Latitude"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Longitude"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Altitude"></th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                        <script>
                            $(window).on('load', function() {
                                new DataTable('#record', {
                                    ajax: 'fetch.php?table=vw_customer',
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
                                                let btn = '<center><a href=\'customer.php?type=update&id='+data+'\' class=\'button-action\'><i class=\'bx bx-edit\'></i></a> <a href=\'customer.php?type=delete&id='+data+'\' class=\'button-action\'><i class=\'bx bx-trash\'></i></a> <a href=\'customer.php?type=read&id='+data+'\' class=\'button-action\'><i class=\'bx bx-bullseye\'></i></a></center>';
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