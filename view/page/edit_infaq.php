<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/infaq.controller.php';
include '../../controller/kas.controller.php';

checkLogin();
if (!isAdminOrTakmir()) {
    header("Location: infaq.php");
}

$infaq_id = $_GET['id'];
$infaq = getDetailedInfaqbyId($infaq_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_infaq = $_POST['jenis_infaq'];
    $tgl_infaq = $_POST['tgl_infaq'];
    $jml_infaq = $_POST['jml_infaq'];

    $data = [
        "jenis_infaq" => $jenis_infaq,
        "tgl_infaq" => $tgl_infaq,
        "jml_infaq" => $jml_infaq,
    ];

    $result = updateInfaq($infaq_id, $data);

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
                                <h4 class="card-title">Edit Infaq</h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="edit_infaq.php?id=<?= $infaq_id ?>">

                                    <div class="form-group row">
                                        <label for="nama_donatur" class="col-sm-3 col-form-label">Id Infaq</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="id_infaq" name="id_infaq" value="<?= $donasi['id_infaq'] ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jenis_infaq" class="col-sm-3 col-form-label">Jenis Infaq</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jenis_infaq" name="jenis_infaq" value="<?= htmlspecialchars($infaq['jenis_infaq']) ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="jml_infaq" class="col-sm-3 col-form-label">Jumlah</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jml_infaq" placeholder="Jumlah" name="jml_infaq" value="<?= htmlspecialchars($infaq['jml_infaq']) ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tgl_infaq" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" id="tgl_infaq" name="tgl_infaq" value="<?= htmlspecialchars($infaq['tgl_infaq']) ?>">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button type="button" class="btn btn-light" onclick="window.location.href='infaq_list.php'">Cancel</button>
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