<?php
    require "connect.php";
    require "components/notify.php";

    if (isset($_SESSION['user'])) {
        echo "<script>window.location='".base_url()."/account.php';</script>";
    } else {
        if (isset($_POST['code']) && isset($_POST['email']) && isset($_POST['otp']) && isset($_POST['otp_time']) && isset($_POST['purpose'])) {
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
                        <h2>Konfirmasi OTP</h2>
                        <img src="<?=base_url()?>/assets/img/Vfoodia.png">
                        <?php
                            if (isset($_POST['confirm'])) {
                                $code = $_POST['code'];
                                $nohp = $_POST['nohp'];
                                $email = $_POST['email'];
                                $pass = $_POST['pass'] ?? '';
                                $purpose = $_POST['purpose'];
                                $otp = $_POST['otp'];
                                $cotp = $_POST['cotp'];
                                $otp_time = $_POST['otp_time'];
                                $now = date("Y-m-d H:i:s");
                                $sql_otp = mysqli_query($con, "SELECT * FROM tbl_user WHERE code = '$code' LIMIT 1") or die (mysqli_error($con));
                                if (mysqli_num_rows($sql_otp) > 0) {
                                    $dataOTP = mysqli_fetch_array($sql_otp);
                                    $otp_time_db = strtotime($dataOTP['otp_time']);
                                    $now_time = strtotime($now);
                                    $diff_seconds = $now_time - $otp_time_db;
                                    if ($otp == $cotp) {
                                        if ($diff_seconds <= 180) {
                                            if ($purpose == 'RGSTR') {
                                                $qOTP = mysqli_query($con, "UPDATE tbl_user SET is_verified='TRUE', otp=null, otp_time=null, update_at='$now', update_by='{$dataOTP['code']}' WHERE code = '$code'") or die(mysqli_error($con));
                                                if ($qOTP) {
                                                    alert('success', 'Confirmed', 'Akun Berhasil Dibuat!', base_url().'/login.php');
                                                } else {
                                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                                }
                                            } else if ($purpose == 'FPASS') {
                                                $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
                                                $qNewPass = mysqli_query($con, "UPDATE tbl_user SET pass='$hashedPassword', otp=null, otp_time=null, update_at='$now', update_by='{$dataOTP['code']}' WHERE code = '$code'") or die(mysqli_error($con));
                                                if ($qNewPass) {
                                                    alert('success', 'Confirmed', 'Password Diperbarui!', base_url().'/login.php');
                                                } else {
                                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                                }
                                            } else {
                                                alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                            }
                                        } else {
                                            alert('error', 'Expired', 'OTP telah kadaluarsa', null);
                                        }
                                    } else {
                                        alert('error', 'Oops...', 'Invalid OTP', null);
                                    }
                                } else {
                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                }
                            }

                            if (isset($_POST['resendOTP'])) {
                                $code = $_POST['code'];
                                $nohp = $_POST['nohp'];
                                $email = $_POST['email'];
                                $purpose = $_POST['purpose'];
                                $otp = mt_rand(100000, 999999);
                                $otp_time = date("Y-m-d H:i:s");
                                $qResend = mysqli_query($con, "UPDATE tbl_user SET otp='$otp', otp_time='$otp_time', update_at='$otp_time', update_by='$code' WHERE code = '$code'") or die(mysqli_error($con));
                                if ($qResend) {
                                    $perihal = '';
                                    if ($purpose == 'RGSTR') {
                                        $perihal = 'VERIFICATION';
                                    } else if ($purpose == 'FPASS') {
                                        $perihal = 'RESET PASSWORD';
                                    }
                                    $sendMail = sendMail (
                                        $email,
                                        $perihal,
                                        mailTemplate("<b>User</b> : ".$code."<br><b>OTP</b> : $otp")
                                    );
                                    if ($sendMail) {
                                        alert('success', 'OTP Sended', 'Cek Email Kamu!', null);
                                        autoDirect(base_url().'/otp.php', [
                                            'code' => $code,
                                            'nohp' => $nohp,
                                            'email' => $email,
                                            'otp' => $otp,
                                            'otp_time' => $otp_time,
                                            'purpose' => $purpose
                                        ]);
                                    } else {
                                        alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                    }
                                } else {
                                    alert('error', 'Oops...', 'Something went wrong', base_url().'/login.php');
                                }
                            }
                        ?>
                        <form method="POST" action="">
                            <div class="field input-field">
                                <input type="text" placeholder="Kode User / Email" name="code" value="<?=$_POST['code']?>" class="input" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" readonly>
                            </div>

                            <input type="hidden" name="nohp" value="<?=$_POST['nohp']?>">
                            <input type="hidden" name="email" value="<?=$_POST['email']?>">
                            <input type="hidden" name="otp" value="<?=$_POST['otp']?>">
                            <input type="hidden" name="otp_time" value="<?=$_POST['otp_time']?>">
                            <input type="hidden" name="purpose" value="<?=$_POST['purpose']?>">

                            <div class="field input-field">
                                <input type="text" placeholder="OTP" name="cotp" class="input" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>

                            <?php
                                if ($_POST['purpose'] == 'FPASS') {
                            ?>
                                    <div class="field input-field">
                                        <input type="password" placeholder="New Password" name="pass" class="password" maxlength="15" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" required>
                                        <i class='bx bx-hide eye-icon'></i>
                                    </div>
                            <?php
                                }
                            ?>

                            <div class="form-link">
                                <button id="resendOTP" name="resendOTP" type="button" disabled></button>
                            </div>

                            <div class="field button-field">
                                <button type="submit" name="confirm">Konfirmasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <?php
            include "components/afterLoad.php";
        ?>

        <script>
            var otpTimeString = document.querySelector('input[name="otp_time"]').value;
            var otpTime = new Date(otpTimeString).getTime() / 1000;
            var currentTime = Math.floor(Date.now() / 1000);
            var timeElapsed = currentTime - otpTime;
            var resendButton = document.getElementById('resendOTP');

            if (timeElapsed < 30) {
                resendButton.disabled = true;
                var countdownTime = 30 - timeElapsed;
                resendButton.innerText = 'Tunggu ' + countdownTime + ' detik';
                var countdownInterval = setInterval(function() {
                    countdownTime--;
                    resendButton.innerText = 'Tunggu ' + countdownTime + ' detik';
                    if (countdownTime <= 0) {
                        clearInterval(countdownInterval);
                        resendButton.disabled = false;
                        resendButton.innerText = 'Resend OTP';
                    }
                }, 1000);
            } else {
                resendButton.disabled = false;
                resendButton.innerText = 'Resend OTP';
            }

            resendButton.addEventListener('click', function() {
                var code = document.querySelector('input[name="code"]').value;
                var nohp = document.querySelector('input[name="nohp"]').value;
                var email = document.querySelector('input[name="email"]').value;
                var otp = document.querySelector('input[name="otp"]').value;
                var otp_time = document.querySelector('input[name="otp_time"]').value;
                var purpose = document.querySelector('input[name="purpose"]').value;

                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?=base_url()?>/otp.php';

                var inputResendOTP = document.createElement('input');
                inputResendOTP.type = 'hidden';
                inputResendOTP.name = 'resendOTP';
                inputResendOTP.value = 'resendOTP';
                form.appendChild(inputResendOTP);

                var inputCode = document.createElement('input');
                inputCode.type = 'hidden';
                inputCode.name = 'code';
                inputCode.value = code;
                form.appendChild(inputCode);

                var inputPhone = document.createElement('input');
                inputPhone.type = 'hidden';
                inputPhone.name = 'nohp';
                inputPhone.value = nohp;
                form.appendChild(inputPhone);

                var inputEmail = document.createElement('input');
                inputEmail.type = 'hidden';
                inputEmail.name = 'email';
                inputEmail.value = email;
                form.appendChild(inputEmail);

                var inputOTP = document.createElement('input');
                inputOTP.type = 'hidden';
                inputOTP.name = 'otp';
                inputOTP.value = otp;
                form.appendChild(inputOTP);

                var inputOTPtime = document.createElement('input');
                inputOTPtime.type = 'hidden';
                inputOTPtime.name = 'otp_time';
                inputOTPtime.value = otp_time;
                form.appendChild(inputOTPtime);

                var inputPurpose = document.createElement('input');
                inputPurpose.type = 'hidden';
                inputPurpose.name = 'purpose';
                inputPurpose.value = purpose;
                form.appendChild(inputPurpose);

                document.body.appendChild(form);
                form.submit();
            });
        </script>
    </body>
</html>
<?php
        } else {
            echo "<script>window.location='".base_url()."/login.php';</script>";
        }
    }
?>