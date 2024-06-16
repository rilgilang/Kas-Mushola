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
                                    <h4 class="font-weight-bold mb-0">Dashboard Kas Mushola Rahmatullah</h4>
                                </div>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Donasi</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?= number_format($dashboard_data["total_donasi"], 0, ',', '.'); ?></h3>
                                        <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                    <!-- <p class="mb-0 mt-2 text-danger">0.12% <span class="text-black ms-1"><small>(30 days)</small></span></p> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Infaq</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?= number_format($dashboard_data["total_infaq"], 0, ',', '.'); ?></h3>
                                        <i class="ti-user icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                    <!-- <p class="mb-0 mt-2 text-danger">0.47% <span class="text-black ms-1"><small>(30 days)</small></span></p> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Total Pengeluaran</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?= number_format($dashboard_data['total_pengeluaran'], 0, ',', '.') ?></h3>
                                        <i class="ti-agenda icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                    <!-- <p class="mb-0 mt-2 text-success">64.00%<span class="text-black ms-1"><small>(30 days)</small></span></p> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title text-md-center text-xl-left">Saldo Akhir</p>
                                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                                        <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0"><?= number_format($dashboard_data['total_saldo'], 0, ',', '.') ?></h3>
                                        <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                                    </div>
                                    <!-- <p class="mb-0 mt-2 text-success">23.00%<span class="text-black ms-1"><small>(30 days)</small></span></p> -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="https://www.bootstrapdash.com/" target="_blank">bootstrapdash.com </a>2021</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Only the best <a href="https://www.bootstrapdash.com/" target="_blank"> Bootstrap dashboard </a> templates</span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>

</body>

</html>