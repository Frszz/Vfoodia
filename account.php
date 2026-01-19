<?php
    require "connect.php";
    require "components/notify.php";

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

        <main>
            <section class="container section section__height">
                <h2 class="section__title">Akun</h2>
                <?php
                    $qAccount = mysqli_query($con, "SELECT * FROM tbl_user WHERE code = '{$_SESSION['user']}' LIMIT 1") or die(mysqli_error($con));
                    if (mysqli_num_rows($qAccount) > 0) {
                        $dataAccount = mysqli_fetch_array($qAccount);
                    } else {
                        echo "<script>window.location='".base_url()."/login.php';</script>";
                    }

                    if (isset($_POST['update'])) {
                        $code = trim(mysqli_real_escape_string($con, $_POST['code']));
                        $role = trim(mysqli_real_escape_string($con, $_POST['role']));
                        $email = trim($_POST['email'] ?? null);
                        $nohp = trim($_POST['nohp'] ?? null);
                        $full_name = trim(mysqli_real_escape_string($con, $_POST['full_name']));
                        $pass = trim(mysqli_real_escape_string($con, $_POST['pass']));
                        $photo = trim($_POST['base64_photo'] ?? null);
                        $no_vehicle = trim($_POST['no_vehicle'] ?? null);
                        $wa_token = trim($_POST['wa_token'] ?? null);
                        $update_at = date("Y-m-d H:i:s");
                        $qPreUser = mysqli_query($con, "SELECT * FROM tbl_user WHERE code = '$code' LIMIT 1") or die(mysqli_error($con));
                        $dataPreUser = mysqli_fetch_array($qPreUser);
                        if (!password_verify($pass, $dataPreUser['pass'])) {
                            $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
                            $updateUser = mysqli_query($con, "UPDATE tbl_user SET
                                email='$email',
                                nohp='$nohp',
                                photo='$photo',
                                full_name='$full_name',
                                pass='$hashedPassword',
                                no_vehicle='$no_vehicle',
                                wa_token='$wa_token',
                                update_at='$update_at',
                                update_by='$code'
                            WHERE code = '$code'") or die(mysqli_error($con));
                            if ($updateUser) {
                                alert('success', 'Update Success', 'Silahkan Login Kembali', base_url().'/logout.php');
                            } else {
                                alert('error', 'Update Failed', 'Something went wrong', base_url().'/account.php');
                            }
                        } else {
                            $updateUser = mysqli_query($con, "UPDATE tbl_user SET
                                email='$email',
                                nohp='$nohp',
                                photo='$photo',
                                full_name='$full_name',
                                no_vehicle='$no_vehicle',
                                wa_token='$wa_token',
                                update_at='$update_at',
                                update_by='$code'
                            WHERE code = '$code'") or die(mysqli_error($con));
                            if ($updateUser) {
                                alert('success', 'Update Success', 'Data Diperbarui', base_url().'/account.php');
                            } else {
                                alert('error', 'Update Failed', 'Something went wrong', base_url().'/account.php');
                            }
                        }
                    }
                ?>
                <form class="form-data" method="POST" action="" enctype="multipart/form-data">
                    <div class="upload">
                        <img class="preview" src="<?=!empty($dataAccount['photo']) ? $dataAccount['photo'] : base_url().'/assets/img/no-pp.webp'?>">
                        <div class="round">
                            <input type="file" accept=".png, .jpg, .jpeg" name="photo">
                            <input type="hidden" name="base64_photo" value="<?=$dataAccount['photo']?>">
                            <i class='bx bx-camera'></i>
                        </div>
                    </div>
                    <center><small><i>*Ukuran Maksimal File : 100kb</i></small></center>
                    <div class="form-input">
                        <label>Kode User</label>
                        <input type="text" name="code" maxlength="20" value="<?=$dataAccount['code']?>" placeholder="Kode User" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')" readonly required>
                    </div>
                    <div class="form-input">
                        <label>Role</label>
                        <input type="text" name="role" maxlength="20" value="<?=$dataAccount['role']?>" placeholder="Role" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')" readonly required>
                    </div>
                    <div class="form-input">
                        <label>Email</label>
                        <input type="email" name="email" maxlength="50" value="<?=$dataAccount['email']?>" placeholder="Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')">
                    </div>
                    <div class="form-input">
                        <label>No. Hp</label>
                        <input type="text" name="nohp" maxlength="20" value="<?=$dataAccount['nohp']?>" placeholder="No. Hp (62xxx)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="form-input">
                        <label>Nama Lengkap</label>
                        <input type="text" name="full_name" maxlength="100" value="<?=$dataAccount['full_name']?>" placeholder="Nama Lengkap" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '')" required>
                    </div>
                    <?php
                        if ($_SESSION['role'] == 'KURIR') {
                    ?>
                            <div class="form-input">
                                <label>No. Plat Kendaraan</label>
                                <input type="text" name="no_vehicle" maxlength="20" value="<?=$dataAccount['no_vehicle']?>" placeholder="No. Plat Kendaraan" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')" required>
                            </div>
                    <?php
                        }
                    ?>
                    <?php
                        if ($_SESSION['role'] == 'KURIR') {
                    ?>
                            <div class="form-input">
                                <label>WA Token</label>
                                <input type="text" name="wa_token" maxlength="50" value="<?=$dataAccount['wa_token']?>" placeholder="WA Token" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" required>
                            </div>
                    <?php
                        }
                    ?>
                    <div class="form-input">
                        <label>Password</label>
                        <input type="password" class="password" name="pass" maxlength="20" value="<?=$_SESSION['pass']?>" placeholder="Password" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" required>
                        <i class='bx bx-hide eye-icon-account'></i>
                    </div>
                    <div class="form-input">
                        <button type="submit" class="submit" name="update"><i class='bx bx-save'></i> Update</button>
                        <a class="sub-off" href="<?=base_url()?>/logout.php"><i class='bx bx-power-off'></i> Log Out</a>
                    </div>
                </form>
                                
                <?php
                    $allowedRoles = ['ADMIN'];
                    if (in_array($_SESSION['role'], $allowedRoles)) {
                ?>
                <div class="form-input" style="margin-top: 20px;">
                    <button type="button" class="btn-clear-cache" onclick="clearRouteCache()" style="background: #ff0000; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class='bx bx-trash'></i> Clear Route Cache
                    </button>
                </div>
                <?php
                    }
                ?>
            </section>
        </main>

                <script>
                function clearRouteCache() {
                    Swal.fire({
                        title: 'Hapus Semua Cache Rute?',
                        text: "Semua data cache rute akan dihapus dan sistem akan fetch ulang dari API!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74c3c',
                        cancelButtonColor: '#95a5a6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menghapus Cache...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            fetch('<?=base_url()?>/utils/clear_cache.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        confirmButtonColor: '#27ae60'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: data.message,
                                        confirmButtonColor: '#e74c3c'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan: ' + error.message,
                                    confirmButtonColor: '#e74c3c'
                                });
                            });
                        }
                    });
                }
                </script>
            </section>
        </main>

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