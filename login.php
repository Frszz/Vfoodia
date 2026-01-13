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
                        <h2>Login</h2>
                        <img src="<?=base_url()?>/assets/img/Vfoodia.png">
                        <?php
                            if (isset($_POST['login'])) {
                                $user = trim(mysqli_real_escape_string($con, $_POST['user']));
                                $pass = trim(mysqli_real_escape_string($con, $_POST['pass']));
                                $sql_login = mysqli_query($con, "SELECT * FROM tbl_user WHERE code = '$user' OR email = '$user' LIMIT 1") or die (mysqli_error($con));
                                if (mysqli_num_rows($sql_login) > 0) {
                                    $dataLogin = mysqli_fetch_array($sql_login);
                                    if (password_verify($pass, $dataLogin['pass'])) {
                                        $_SESSION['user'] = $dataLogin['code'];
                                        $_SESSION['pass'] = $pass;
                                        $_SESSION['role'] = $dataLogin['role'];
                                        mysqli_query($con, "UPDATE tbl_user SET otp=null, otp_time=null WHERE code = '$user'") or die(mysqli_error($con));
                                        alert('success', 'Login Success', 'Hello '.$dataLogin['code'].'!', base_url().'/account.php');
                                    } else {
                                        alert('error', 'Login Failed', 'Invalid User or Password', base_url().'/login.php');
                                    }
                                } else {
                                    alert('error', 'Login Failed', 'Invalid User or Password', base_url().'/login.php');
                                }
                            }
                        ?>
                        <form method="POST" action="">
                            <div class="field input-field">
                                <input type="text" placeholder="Kode User / Email" name="user" class="input" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '')" required>
                            </div>

                            <div class="field input-field">
                                <input type="password" placeholder="Password" name="pass" class="password" maxlength="15" oninput="this.value = this.value.replace(/[ '\&quot;,.]/g, '')" required>
                                <i class='bx bx-hide eye-icon'></i>
                            </div>

                            <div class="form-link">
                                <a href="<?=base_url()?>/fpass.php" class="forgot-pass">Lupa Password?</a>
                            </div>

                            <div class="field button-field">
                                <button type="submit" name="login">Login</button>
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