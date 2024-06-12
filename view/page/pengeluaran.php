<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/pengeluaran.controller.php';

checkLogin();

$error = '';
$filter = [
    'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
    'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
];

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
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Filter</h4>
                                    <form class="form-inline" method="get" action="pengeluaran.php">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <div class="input-group mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-black">Dari</div>
                                                    </div>
                                                    <input type="date" class="form-control" id="inlineFormInputGroupUsername2" placeholder="DD/MM/YYYY" name="start_date">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-black">Sampai</div>
                                                    </div>
                                                    <input type="date" class="form-control" id="inlineFormInputGroupUsername2" placeholder="DD/MM/YYYY" name="end_date">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-primary font-weight-bold text-white">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Kas Pengeluaran</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Jenis Transaksi</th>
                                                <th>Keterangan</th>
                                                <th>Jumlah</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $data = getAllPengeluaran($filter);

                                            if ($data == false) {
                                            ?>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                            } else {
                                                foreach ($data as $key => $val) {
                                                ?>
                                                    <tr>
                                                        <td><?= $key + 1 ?></td>
                                                        <td><?= $val['tgl_transaksi_keluar'] ?></td>
                                                        <td><?= $val['jenis_transaksi_keluar'] ?></td>
                                                        <td><?= $val['ket_kaskeluar'] == "No Description" ? null : $val['ket_kaskeluar'] ?></td>
                                                        <td><?= $val['jml_transaksi_keluar'] ?></td>
                                                        <td>
                                                            <a type="button" href="./edit_pengeluaran.php?id=<?= $val['id_transaksi_keluar'] ?>" class="btn btn-outline-secondary btn-icon-text">
                                                                Edit
                                                                <i class="ti-file btn-icon-append"></i>
                                                            </a>
                                                            <a type="button" href="./delete_pengeluaran.php?id=<?= $val['id_transaksi_keluar'] ?>" class="btn btn-outline-danger btn-icon-text">
                                                                <i class="ti-upload btn-icon-prepend"></i>
                                                                Hapus
                                                            </a>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            }

                                            ?>
                                        </tbody>
                                    </table>
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