<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/pengeluaran.controller.php';
include '../../controller/kas.controller.php';

checkLogin();
$pengeluaran_id = $_GET['id'];
$pengeluaran = getDetailedPengeluaranById($pengeluaran_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_transaksi_keluar = $_POST['jenis_transaksi_keluar'];
    $tgl_transaksi_keluar = $_POST['tgl_transaksi_keluar'];
    $jml_transaksi_keluar = $_POST['jml_transaksi_keluar'];
    $keterangan = $_POST['keterangan'];

    $data = [
        "jenis_transaksi_keluar" => $jenis_transaksi_keluar,
        "tgl_transaksi_keluar" => $tgl_transaksi_keluar,
        "jml_transaksi_keluar" => $jml_transaksi_keluar,
        "keterangan" => $keterangan,
    ];

    $result = updatePengeluaranById($pengeluaran_id, $data);

    if ($result == "success") {
        header("Refresh:0");
    } else {
        $error = $result;
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
                                <h4 class="card-title">Edit Pengeluaran</h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="edit_pengeluaran.php?id=<?= $pengeluaran_id ?>">

                                    <div class="form-group row">
                                        <label for="jenis_pengeluaran" class="col-sm-3 col-form-label">Jenis Pengeluaran</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jenis_pengeluaran" name="jenis_transaksi_keluar" value="<?= $pengeluaran['jenis_transaksi_keluar'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jml_transaksi_keluar" class="col-sm-3 col-form-label">Jumlah</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jml_transaksi_keluar" placeholder="Jumlah" name="jml_transaksi_keluar" value="<?= $pengeluaran['jml_transaksi_keluar'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_pengeluaran" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" id="tgl_transaksi_keluar" name="tgl_transaksi_keluar" value="<?= $pengeluaran['tgl_transaksi_keluar'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="keterangan" rows="4" name="keterangan"><?= $pengeluaran['ket_kaskeluar'] ?></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button type="button" class="btn btn-light" onclick="window.location.href='pengeluaran_list.php'">Cancel</button>
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