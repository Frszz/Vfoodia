<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="header" id="header">
    <nav class="nav container">
        <a href="<?=base_url()?>/dashboard.php" class="nav__logo">Vfoodia</a>

        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <?php
                    if ($_SESSION['role'] == 'ADMIN') {
                ?>
                        <li class="nav__item">
                            <a href="<?=base_url()?>/dashboard.php" class="nav__link <?=in_array($currentPage, ['dashboard.php', 'index.php']) ? 'active-link' : ''?>">
                                <i class='bx bx-home-alt nav__icon'></i>
                                <span class="nav__name">Beranda</span>
                            </a>
                        </li>
                <?php
                    }
                ?>

                <li class="nav__item">
                    <a href="<?=base_url()?>/delivery.php" class="nav__link <?=($currentPage == 'delivery.php') ? 'active-link' : ''?>">
                        <i class='bx bx-map nav__icon'></i>
                        <span class="nav__name">Pengantaran</span>
                    </a>
                </li>

                <?php
                    if ($_SESSION['role'] == 'ADMIN') {
                ?>
                        <li class="nav__item">
                            <a href="<?=base_url()?>/customer.php" class="nav__link <?=($currentPage == 'customer.php') ? 'active-link' : ''?>">
                                <i class='bx bx-user-pin nav__icon'></i>
                                <span class="nav__name">Pelanggan</span>
                            </a>
                        </li>
                <?php
                    }
                ?>

                <?php
                    if ($_SESSION['role'] == 'ADMIN') {
                ?>
                        <li class="nav__item">
                            <a href="<?=base_url()?>/sales.php" class="nav__link <?=($currentPage == 'sales.php') ? 'active-link' : ''?>">
                                <i class='bx bx-wallet nav__icon'></i>
                                <span class="nav__name">Penjualan</span>
                            </a>
                        </li>
                <?php
                    }
                ?>

                <?php
                    if ($_SESSION['role'] == 'ADMIN') {
                ?>
                        <li class="nav__item">
                            <a href="<?=base_url()?>/users.php" class="nav__link <?=($currentPage == 'users.php') ? 'active-link' : ''?>">
                                <i class='bx bx-group nav__icon'></i>
                                <span class="nav__name">Pengguna</span>
                            </a>
                        </li>
                <?php
                    }
                ?>
            </ul>
        </div>

        <a href="<?=base_url()?>/account.php" class="nav__img">
            <?php
                if (isset($_SESSION['user'])) {
                    $qPP = mysqli_query($con, "SELECT code, photo FROM tbl_user WHERE code = '{$_SESSION['user']}' LIMIT 1");
                    $dataPP = mysqli_fetch_array($qPP);
                    if ($dataPP['photo'] != null && !empty($dataPP['photo'])) {
            ?>
                        <img src="<?=$dataPP['photo']?>" alt="">
            <?php
                    } else {
            ?>
                        <img src="<?=base_url()?>/assets/img/no-pp.webp" alt="">
            <?php
                    }
                } else {
                    echo "<script>window.location='".base_url()."/login.php';</script>";
                }
            ?>
        </a>
    </nav>
</header>