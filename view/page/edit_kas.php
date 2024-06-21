<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/kas.controller.php';
include '../../controller/kas.masuk.controller.php';
include '../../controller/kas.keluar.controller.php';

checkLogin();
if (!isAdminOrTakmir()) {
    header("Location: kas_masuk.php");
}

$filter = [
    'start_date' => "",
    'end_date' => "",
];

$kasMasukList = getAllKasMasuk($filter);
$kasKeluarList = getAllKasKeluar($filter);
$sumKasMasuk = sumAllKasMasuk();
$sumKasKeluar = sumAllKasKeluar();
$latestKas = getLatestSaldo();

$error = '';
$id_kas = $_GET['id'];
$kasData = getKasById($id_kas);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_kas        = $_POST['tgl_kas'];
    $id_kasmasuk    = $_POST['id_kasmasuk'];
    $jml_kasmasuk   = $_POST['jml_kasmasuk'];
    $id_kaskeluar   = $_POST['id_kaskeluar'];
    $jml_kaskeluar  = $_POST['jml_kaskeluar'];
    $saldo          = $_POST['saldo_kas'];

    $data = [
        "tgl_kas" => $tgl_kas,
        "id_kasmasuk" => $id_kasmasuk,
        "jml_kasmasuk" => $jml_kasmasuk,
        "id_kaskeluar" => $id_kaskeluar,
        "jml_kaskeluar" => $jml_kaskeluar,
        "saldo_kas" => $saldo,
    ];

    $result = updateKas($id_kas, $data);

    if ($result == "success") {
        header("Location: kas.php"); // Redirect to list page after success
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
        <?php // include("../component/navbar.php"); 
        ?>
        <div class="container-fluid page-body-wrapper">
            <?php include("../component/sidebar.php"); ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Kas</h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="edit_kas.php?id=<?= $id_kas ?>">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="id_kas" value="<?= $kasData['id_kas'] ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Tanggal Kas</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" name="tgl_kas" value="<?= $kasData['tgl_kas'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Masuk</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_kasmasuk" name="id_kasmasuk">
                                                <option value="" selected>-</option>
                                                <?php foreach ($kasMasukList as $key => $kasmasuk) { ?>
                                                    <option value="<?= $kasmasuk['id_kasmasuk'] ?>" data-jml="<?= $kasmasuk['jml_kasmasuk'] ?>" <?= $kasData['id_kasmasuk'] == $kasmasuk['id_kasmasuk'] ? 'selected' : '' ?>><?= $kasmasuk['id_kasmasuk'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jml_kasmasuk" name="jml_kasmasuk" value="<?= $kasData['jml_kasmasuk'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Keluar</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_kaskeluar" name="id_kaskeluar">
                                                <option value="" selected>-</option>
                                                <?php foreach ($kasKeluarList as $key => $kaskeluar) { ?>
                                                    <option value="<?= $kaskeluar['id_kaskeluar'] ?>" data-jml="<?= $kaskeluar['jml_kaskeluar'] ?>" <?= $kasData['id_kaskeluar'] == $kaskeluar['id_kaskeluar'] ? 'selected' : '' ?>><?= $kaskeluar['id_kaskeluar'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Keluar</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="jml_kaskeluar" name="jml_kaskeluar" value="<?= $kasData['jml_kaskeluar'] ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Saldo</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="saldo_kas" name="saldo_kas" value="<?= $kasData['saldo_kas'] ?>">
                                        </div>
                                    </div>

                                    <input type="text" name="trx_type" id="trx_type" value="<?= $kasData['id_kaskeluar'] ? 'kredit' : 'debit' ?>" hidden>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <a href="./kas.php" class="btn btn-light">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="https://www.bootstrapdash.com/" target="_blank">bootstrapdash.com </a>2021</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Only the best <a href="https://www.bootstrapdash.com/" target="_blank"> Bootstrap dashboard </a> templates</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>

    <script>
        function toggleFields() {
            var idKasMasuk = document.getElementById('id_kasmasuk');
            var idKasKeluar = document.getElementById('id_kaskeluar');
            var jmlKasMasuk = document.getElementById('jml_kasmasuk');
            var jmlKasKeluar = document.getElementById('jml_kaskeluar');
            var saldoKas = document.getElementById('saldo_kas');

            // Set jumlah kasmasuk value based on selected id_kasmasuk
            var selectedOption = idKasMasuk.options[idKasMasuk.selectedIndex];
            jmlKasMasuk.value = selectedOption.getAttribute('data-jml') || "";

            var selectedOption = idKasKeluar.options[idKasKeluar.selectedIndex];
            jmlKasKeluar.value = selectedOption.getAttribute('data-jml') || ""

            saldoKas.value = jmlKasMasuk.value - jmlKasKeluar.value;
        }

        document.getElementById('id_kasmasuk').addEventListener('change', toggleFields);
        document.getElementById('id_kaskeluar').addEventListener('change', toggleFields);
    </script>
</body>

</html>