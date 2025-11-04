<?php
    require "connect.php";
    require "components/notify.php";

    if (isset($_SESSION['user'])) {
        echo "<script>window.location='".base_url()."/account.php';</script>";
    } else {
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <?php
            include "components/beforeLoad.php";
        ?>
    </head>
    <body>
        <main>
            <section class="container section section__height">
                <div class="form login">
                    <div class="form-content">
                        <h2>Lupa Password</h2>
                        <img src="<?=base_url()?>/assets/img/Vfoodia.png">
                        <?php
                            if (isset($_POST['send'])) {
                                $requester = trim(mysqli_real_escape_string($con, $_POST['requester']));
                                $otp = mt_rand(100000, 999999);
                                $otp_time = date("Y-m-d H:i:s");
                                $checker = mysqli_query($con, "SELECT * FROM tbl_user WHERE code = '$requester' OR email = '$requester' LIMIT 1") or die (mysqli_error($con));
                                if (mysqli_num_rows($checker) > 0) {
                                    $dataChecker = mysqli_fetch_array($checker);
                                    $user = $dataChecker['code'];
                                    $email = $dataChecker['email'];
                                    $nohp = $dataChecker['nohp'];
                                } else {
                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/fpass.php');
                                }
                                $qFpass = mysqli_query($con, "UPDATE tbl_user SET otp='$otp', otp_time='$otp_time', update_at='$otp_time', update_by='$user' WHERE code = '$user'") or die(mysqli_error($con));
                                if ($qFpass) {
                                    $sendMail = sendMail (
                                        $email,
                                        "RESET PASSWORD",
                                        mailTemplate("<b>User</b> : $user<br><b>OTP</b> : $otp")
                                    );
                                    if ($sendMail) {
                                        alert('success', 'OTP Sended', 'Cek Email Kamu!', null);
                                        autoDirect(base_url().'/otp.php', [
                                            'code' => $user,
                                            'nohp' => $nohp,
                                            'email' => $email,
                                            'otp' => $otp,
                                            'otp_time' => $otp_time,
                                            'purpose' => 'FPASS'
                                        ]);
                                    } else {
                                        alert('error', 'Oops...', 'Something went wrong', base_url().'/fpass.php');
                                    }
                                } else {
                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/fpass.php');
                                }
                            }
                        ?>
                        <form method="POST" action="">
                            <div class="field input-field">
                                <input type="text" placeholder="Kode User / Email" name="requester" class="input" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>

                            <div class="form-link">
                                <a href="<?=base_url()?>/login.php" class="forgot-pass">Ingat Password?</a>
                            </div>

                            <div class="field button-field">
                                <button type="submit" name="send">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <?php
            include "components/afterLoad.php";
        ?>
    </body>
</html>
<?php
    }
?>