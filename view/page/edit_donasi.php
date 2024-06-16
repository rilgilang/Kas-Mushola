<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/donasi.controller.php';
include '../../controller/kas.controller.php';

checkLogin();
if (!isAdminOrTakmir()) {
    header("Location: donasi.php");
}


$donasi_id = $_GET['id'];
$donasi = getDetailedDonasiById($donasi_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_donatur = $_POST['nama_donatur'];
    $tgl_donasi = $_POST['tgl_donasi'];
    $jml_donasi = $_POST['jml_donasi'];
    $keterangan = $_POST['keterangan'];

    $data = [
        "nama_donatur" => $nama_donatur,
        "tgl_donasi" => $tgl_donasi,
        "jml_donasi" => $jml_donasi,
        "keterangan" => $keterangan,
    ];

    $result = updatedonasi($donasi_id, $data);

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
                                <h4 class="card-title">Edit donasi</h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="edit_donasi.php?id=<?= $donasi_id ?>">

                                    <div class="form-group row">
                                        <label for="nama_donatur" class="col-sm-3 col-form-label">Nama Donatur</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="nama_donatur" name="nama_donatur" value="<?= $donasi['nama_donatur'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jml_donasi" class="col-sm-3 col-form-label">Jumlah</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jml_donasi" placeholder="Jumlah" name="jml_donasi" value="<?= $donasi['jml_donasi'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_donasi" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" id="tgl_donasi" name="tgl_donasi" value="<?= $donasi['tgl_donasi'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="keterangan" rows="4" name="keterangan"><?= $donasi['ket_kasmasuk'] ?></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button type="button" class="btn btn-light" onclick="window.location.href='donasi_list.php'">Cancel</button>
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