<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/pengeluaran.controller.php';
include '../../controller/kas.controller.php';
include '../../controller/file.controller.php';

checkLogin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_transaksi_keluar = $_POST['jenis_transaksi_keluar'];
    $tgl_transaksi_keluar = $_POST['tgl_transaksi_keluar'];
    $jml_transaksi_keluar = $_POST['jml_transaksi_keluar'];
    // $keterangan = $_POST['keterangan'];

    $data = [
        "jenis_transaksi_keluar" => $jenis_transaksi_keluar,
        "tgl_transaksi_keluar" => $tgl_transaksi_keluar,
        "jml_transaksi_keluar" => $jml_transaksi_keluar,
        // "keterangan" => $keterangan,
        "file" => "",
    ];

    // Handle file upload
    $image = ProcessAndSaveImage($_FILES['image']);
    if (!$image['status']) {
        $error = $image['error'];
    } else {
        $data['file'] = $image['path'];
        $result = addPengeluaran($data);

        if ($result == "success") {
            header("Refresh:0");
        } else {
            $error = $result;
        }
    }
}

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
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Tambah Pengeluaran</h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="tambah_pengeluaran.php" enctype="multipart/form-data">

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Jenis Pengeluaran</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="exampleInputEmail2" name="jenis_transaksi_keluar">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="exampleInputUsername2" placeholder="Jumlah" name="jml_transaksi_keluar">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" id="exampleInputEmail2" placeholder="DD/MM/YYYY" name="tgl_transaksi_keluar">
                                        </div>
                                    </div>

                                    <!-- <div class="form-group row">
                                        <label for="exampleInputConfirmPassword2" class="col-sm-3 col-form-label">Keterangan</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="exampleTextarea1" rows="4" name="keterangan"></textarea>
                                        </div>
                                    </div> -->

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Bukti Pengeluaran</label>
                                        <div class="col-sm-9">
                                            <input type="file" class="form-control" id="exampleInputEmail2" name="image">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button class="btn btn-light">Cancel</button>
                                </form>
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