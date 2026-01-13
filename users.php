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
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" placeholder="No. Hp (62xxx)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" maxlength="100" placeholder="Nama Lengkap" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Role</label>
                                <select name="role" class="searchdisable" required>
                                    <option value="" disabled selected style="display:none;">Role</option>
                                    <?php
                                        $dropdownRole = mysqli_query($con, "SHOW COLUMNS FROM `tbl_user` WHERE `field` = 'role'");
                                        while ($listRole = mysqli_fetch_row($dropdownRole)) {
                                            foreach (explode("','",substr($listRole[1],6,-2)) as $option) {
                                                echo "<option>$option</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <label>No. Plat Kendaraan</label>
                                <input type="text" name="no_vehicle" maxlength="20" placeholder="No. Plat Kendaraan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')">
                            </div>
                            <div class="form-input">
                                <label>WA Token</label>
                                <input type="text" name="wa_token" maxlength="50" placeholder="WA Token" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')">
                            </div>
                            <div class="form-input">
                                <label>Active</label>
                                <select name="is_active" class="searchdisable" required>
                                    <option value="" disabled selected style="display:none;">Active</option>
                                    <?php
                                        $dropdownActive = mysqli_query($con, "SHOW COLUMNS FROM `tbl_user` WHERE `field` = 'is_active'");
                                        while ($listActive = mysqli_fetch_row($dropdownActive)) {
                                            foreach (explode("','",substr($listActive[1],6,-2)) as $option) {
                                                $label = ($option === 'TRUE') ? 'YES' : (($option === 'FALSE') ? 'NO' : $option);
                                                echo "<option value=\"$option\">$label</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="create-user"><i class='bx bx-save'></i> Create</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'update' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qUser = mysqli_query($con, "SELECT * FROM tbl_user WHERE id = $id LIMIT 1");
                $dataUser = mysqli_fetch_array($qUser);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/update.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataUser['photo']) ? $dataUser['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                                <div class="round">
                                    <input type="file" accept=".png, .jpg, .jpeg" name="photo">
                                    <input type="hidden" name="base64_photo" value="<?=$dataUser['photo']?>">
                                    <i class='bx bx-camera'></i>
                                </div>
                            </div>
                            <center><small><i>*Ukuran Maksimal File : 100kb</i></small></center>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataUser['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" value="<?=$dataUser['email']?>" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" value="<?=$dataUser['nohp']?>" placeholder="No. Hp (62xxx)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" maxlength="100" value="<?=$dataUser['full_name']?>" placeholder="Nama Lengkap" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>
                            </div>
                            <div class="form-input">
                                <label>Role</label>
                                <input type="text" name="role" maxlength="20" value="<?=$dataUser['role']?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Plat Kendaraan</label>
                                <input type="text" name="no_vehicle" maxlength="20" value="<?=$dataUser['no_vehicle']?>" placeholder="No. Plat Kendaraan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')">
                            </div>
                            <div class="form-input">
                                <label>WA Token</label>
                                <input type="text" name="wa_token" maxlength="50" placeholder="WA Token" value="<?=$dataUser['wa_token']?>" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')">
                            </div>
                            <div class="form-input">
                                <label>Active</label>
                                <select name="is_active" class="searchdisable" required>
                                    <option value="" disabled selected style="display:none;">Active</option>
                                    <?php
                                        $dropdownActive = mysqli_query($con, "SHOW COLUMNS FROM `tbl_user` WHERE `field` = 'is_active'");
                                        while ($listActive = mysqli_fetch_row($dropdownActive)) {
                                            foreach (explode("','",substr($listActive[1],6,-2)) as $option) {
                                                $label = ($option === 'TRUE') ? 'YES' : (($option === 'FALSE') ? 'NO' : $option);
                                                $selected = ($option == $dataUser['is_active']) ? 'selected' : '';
                                                echo "<option value=\"$option\" $selected>$label</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="update-user"><i class='bx bx-edit'></i> Update</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qUser = mysqli_query($con, "SELECT * FROM tbl_user WHERE id = $id LIMIT 1");
                $dataUser = mysqli_fetch_array($qUser);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" action="<?=base_url()?>/process/delete.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataUser['photo']) ? $dataUser['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                            </div>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataUser['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" value="<?=$dataUser['email']?>" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" value="<?=$dataUser['nohp']?>" placeholder="No. Hp (62xxx)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" maxlength="100" value="<?=$dataUser['full_name']?>" placeholder="Nama Lengkap" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Role</label>
                                <input type="text" name="role" maxlength="20" value="<?=$dataUser['role']?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Plat Kendaraan</label>
                                <input type="text" name="no_vehicle" maxlength="20" value="<?=$dataUser['no_vehicle']?>" placeholder="No. Plat Kendaraan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>WA Token</label>
                                <input type="text" name="wa_token" maxlength="50" placeholder="WA Token" value="<?=$dataUser['wa_token']?>" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Active</label>
                                <input type="text" name="is_active" maxlength="20" value="<?=($dataUser['is_active'] === 'TRUE') ? 'YES' : (($dataUser['is_active'] === 'FALSE') ? 'NO' : 'NO')?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <button type="submit" class="submit" name="delete-user"><i class='bx bx-trash'></i> Delete</button>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else if (isset($_GET['type']) && $_GET['type'] == 'read' && isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $qUser = mysqli_query($con, "SELECT * FROM tbl_user WHERE id = $id LIMIT 1");
                $dataUser = mysqli_fetch_array($qUser);
        ?>
                <main>
                    <section class="container section section__height">
                        <form class="form-data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <div class="upload">
                                <img class="preview" src="<?=!empty($dataUser['photo']) ? $dataUser['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                            </div>
                            <div class="form-input">
                                <label>Kode</label>
                                <input type="text" name="code" maxlength="100" value="<?=$dataUser['code']?>" placeholder="Kode" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Email</label>
                                <input type="email" name="email" maxlength="50" value="<?=$dataUser['email']?>" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Hp</label>
                                <input type="text" name="nohp" maxlength="20" value="<?=$dataUser['nohp']?>" placeholder="No. Hp (62xxx)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" maxlength="100" value="<?=$dataUser['full_name']?>" placeholder="Nama Lengkap" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Role</label>
                                <input type="text" name="role" maxlength="20" value="<?=$dataUser['role']?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>No. Plat Kendaraan</label>
                                <input type="text" name="no_vehicle" maxlength="20" value="<?=$dataUser['no_vehicle']?>" placeholder="No. Plat Kendaraan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>WA Token</label>
                                <input type="text" name="wa_token" maxlength="50" placeholder="WA Token" value="<?=$dataUser['wa_token']?>" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" disabled>
                            </div>
                            <div class="form-input">
                                <label>Active</label>
                                <input type="text" name="is_active" maxlength="20" value="<?=($dataUser['is_active'] === 'TRUE') ? 'YES' : (($dataUser['is_active'] === 'FALSE') ? 'NO' : 'NO')?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" disabled>
                            </div>
                        </form>
                    </section>
                </main>
        <?php
            } else {
        ?>
                <main>
                    <section class="container section section__height">
                        <h2 class="section__title">Pengguna</h2>
                        <a class="add-button" href="<?=base_url()?>/users.php?type=create"><i class='bx bx-plus-circle'></i> Tambah</a>
                        <table id="record" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="text-align: left;">Kode User</th>
                                    <th style="text-align: left;">Email</th>
                                    <th style="text-align: left;">No. Hp</th>
                                    <th style="text-align: left;">Nama Lengkap</th>
                                    <th style="text-align: left;">Role</th>
                                    <th style="text-align: left;">No. Plat Kendaraan</th>
                                    <th style="text-align: left;">Status</th>
                                    <th style="text-align: left;">Aksi</th>
                                </tr>
                                <tr>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari Usercode"></th>
                                    <th><input type="text" style="width: 130px; height: 18px;" class="search-field" placeholder="Cari Email"></th>
                                    <th><input type="text" style="width: 100px; height: 18px;" class="search-field" placeholder="Cari No. Hp"></th>
                                    <th><input type="text" style="width: 130px; height: 18px;" class="search-field" placeholder="Cari Nama Lengkap"></th>
                                    <th><input type="text" style="width: 80px; height: 18px;" class="search-field" placeholder="Cari Role"></th>
                                    <th><input type="text" style="width: 150px; height: 18px;" class="search-field" placeholder="Cari No. Plat Kendaraan"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                        <script>
                            $(window).on('load', function() {
                                new DataTable('#record', {
                                    ajax: 'fetch.php?table=tbl_user',
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
                                            orderable: false,
                                            targets: 6,
                                            render: function(data, type, row) {
                                                let status = `<center><span class='status ${data === 'TRUE' ? 'approve' : 'reject'}'>${data === 'TRUE' ? 'Active' : 'Non Active'}</span></center>`;
                                                return status;
                                            }
                                        },
                                        {
                                            searchable: false,
                                            orderable: false,
                                            targets: 7,
                                            render: function(data, type, row) {
                                                let btn = '<center><a href=\'users.php?type=update&id='+data+'\' class=\'button-action\'><i class=\'bx bx-edit\'></i></a> <a href=\'users.php?type=delete&id='+data+'\' class=\'button-action\'><i class=\'bx bx-trash\'></i></a> <a href=\'users.php?type=read&id='+data+'\' class=\'button-action\'><i class=\'bx bx-bullseye\'></i></a></center>';
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