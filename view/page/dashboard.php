<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/kas.controller.php';
include '../../controller/donasi.controller.php';
include '../../controller/infaq.controller.php';
include '../../controller/pengeluaran.controller.php';

checkLogin();

$dashboard_data = getDashboardData()

?>
<!DOCTYPE html>
<html>

<head>
    <?php include("../component/header.php"); ?>
</head>

<body>
    <div class="container-scroller">
        <?php include("../component/navbar.php"); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include("../component/sidebar.php"); ?>

            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="fw-bold mb-0 text-white">Dashboard Kas Mushola Rahmatullah</h4>
                                </div>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card rounded">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Donasi</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 fw-light">Rp. <?= number_format($dashboard_data["total_donasi"], 0, ',', '.'); ?></h3>
                                        <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card rounded">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Infaq</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 fw-light">Rp. <?= number_format($dashboard_data["total_infaq"], 0, ',', '.'); ?></h3>
                                        <i class="ti-user icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card rounded">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Pengeluaran</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 fw-light">Rp. <?= number_format($dashboard_data['total_pengeluaran'], 0, ',', '.') ?></h3>
                                        <i class="ti-agenda icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card rounded">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Saldo Akhir</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0 fw-light">Rp. <?= number_format($dashboard_data['total_saldo'], 0, ',', '.') ?></h3>
                                        <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer inside content-wrapper -->
                    <footer class="footer mt-auto py-3">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 text-center text-md-left">
                                </div>
                                <div class="col-md-6 text-center text-md-right text-white row">
                                    <span>Oleh :</span>
                                    <span>Deslia Miratunnisa (213210018)</span>
                                    <span>Sistem Informasi Akuntansi</span>
                                    <span>Univ Teknologi Digital Indonesia</span>
                                    <span>Yogyakarta</span>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- main-panel ends -->
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>

</body>

</html>