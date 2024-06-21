<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/infaq.controller.php';
include '../../controller/donasi.controller.php';
include '../../controller/pdf.controller.php';
include '../../controller/kas.masuk.controller.php';

checkLogin();

$error = '';
$filter = [
    'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
    'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
];

$total = 0;


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
                                    <form class="form-inline" method="get" action="kas_masuk.php">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-md-5">
                                                <div class="input-group mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-black">Dari</div>
                                                    </div>
                                                    <input type="date" class="form-control" id="inlineFormInputGroupUsername2" placeholder="DD/MM/YYYY" name="start_date">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text text-black">Sampai</div>
                                                    </div>
                                                    <input type="date" class="form-control" id="inlineFormInputGroupUsername2" placeholder="DD/MM/YYYY" name="end_date">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary font-weight-bold text-white">Cari</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between grid-margin">
                        <?php if (isAdminOrTakmir()) : ?>
                            <a href="./tambah_kas_masuk.php" class="btn btn-primary font-weight-bold text-white">Tambah Kas Masuk</a>
                        <?php endif; ?>

                        <?php if ($filter["start_date"] != "" && $filter["end_date"] != "") : ?>
                            <a href="./download_pdf.php?type=kas_masuk&start_date=<?= $filter["start_date"] ?>&end_date=<?= $filter["end_date"] ?>" class="btn btn-primary font-weight-bold text-white">Export Laporan</a>
                        <?php else :  ?>
                            <a href="./download_pdf.php?type=kas_masuk" class="btn btn-primary font-weight-bold text-white">Export Laporan</a>
                        <?php endif; ?>
                    </div>


                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Kas</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>ID Kas Masuk</th>
                                                <th>Tanggal Kas Masuk</th>
                                                <th>Id Infaq</th>
                                                <th>Id Donasi</th>
                                                <th>Keterangan</th>
                                                <th>Jumlah</th>
                                                <?php if (isAdminOrTakmir()) : ?>
                                                    <th>Aksi</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $data = getAllKasMasuk($filter);

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
                                                    <?php if (isAdminOrTakmir()) : ?>
                                                        <td></td>
                                                    <?php endif; ?>

                                                </tr>
                                                <?php
                                            } else {
                                                foreach ($data as $key => $val) {
                                                ?>
                                                    <tr>
                                                        <td><?= $key + 1 ?></td>
                                                        <td><?= $val['id_kasmasuk'] ?></td>
                                                        <td><?= $val['tgl_kasmasuk'] ?></td>
                                                        <td><?= $val['id_infaq'] ?></td>
                                                        <td><?= $val['id_donasi'] ?></td>
                                                        <td><?= $val['ket_kasmasuk'] ?></td>
                                                        <td>Rp. <?= number_format($val['jml_kasmasuk'], 0, ',', '.'); ?></td>

                                                        <?php if (isAdminOrTakmir()) : ?>
                                                            <td>
                                                                <a type="button" href="./edit_kas_masuk.php?id=<?= $val['id_kasmasuk'] ?>" class="btn btn-outline-secondary btn-icon-text">
                                                                    Edit
                                                                    <i class="ti-file btn-icon-append"></i>
                                                                </a>
                                                                <a type="button" href="./delete_kas_masuk.php?id=<?= $val['id_kasmasuk'] ?>" class="btn btn-outline-danger btn-icon-text">
                                                                    <i class="ti-upload btn-icon-prepend"></i>
                                                                    Hapus
                                                                </a>
                                                            </td>
                                                        <?php endif; ?>


                                                    </tr>
                                            <?php
                                                    $total = $total + $val['jml_kasmasuk'];
                                                }
                                            }

                                            ?>

                                            <tr class="border border-white">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td>Rp. <?= number_format($total, 0, ',', '.'); ?></td>
                                                <td></td>
                                            </tr>

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