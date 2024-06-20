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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_kas = $_POST['id_kas'];
    $tgl_kas = $_POST['tgl_kas'];
    $id_kasmasuk = $_POST['trx_type'] == "debit" ? $_POST['id_kasmasuk'] : "";
    $jml_kasmasuk = $_POST['trx_type'] == "debit" ? $_POST['jml_kasmasuk'] : "";
    $id_kaskeluar = $_POST['trx_type'] == "kredit" ? $_POST['id_kaskeluar'] : "";
    $jml_kaskeluar = $_POST['trx_type'] == "kredit" ? $_POST['jml_kaskeluar'] : "";
    $saldo = $_POST['saldo_kas']; // Assuming you need saldo from the form

    $data = [
        "trx_type" => $_POST['trx_type'],
        "id_kas" => $id_kas,
        "tgl_kas" => $tgl_kas,
        "id_kasmasuk" => $id_kasmasuk,
        "jml_kasmasuk" => $jml_kasmasuk,
        "id_kaskeluar" => $id_kaskeluar,
        "jml_kaskeluar" => $jml_kaskeluar,
        "saldo_kas" => $saldo,
    ];

    $result = addKas($data);

    if ($result != "success") {
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

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Tambah Kas</h4>
                                <h4 class="card-title" id="selectedValue"></h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="tambah_kas.php">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="id_kas">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Tanggal Kas</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" placeholder="DD/MM/YYYY" name="tgl_kas">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Masuk</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_kasmasuk" name="id_kasmasuk">
                                                <option value="" selected>-</option>
                                                <?php foreach ($kasMasukList as $key => $kasmasuk) { ?>
                                                    <option value="<?= $kasmasuk['id_kasmasuk'] ?>" data-jml="<?= $kasmasuk['jml_kasmasuk'] ?>"><?= $kasmasuk['id_kasmasuk'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumlah" id="jml_kasmasuk" name="jml_kasmasuk">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Keluar</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_kaskeluar" name="id_kaskeluar">
                                                <option value="" selected>-</option>
                                                <?php foreach ($kasKeluarList as $key => $kaskeluar) { ?>
                                                    <option value="<?= $kaskeluar['id_kaskeluar'] ?>" data-jml="<?= $kaskeluar['jml_kaskeluar'] ?>"><?= $kaskeluar['id_kaskeluar'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Keluar</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumlah Kas Keluar" id="jml_kaskeluar" name="jml_kaskeluar">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Saldo</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumlah Saldo" id="saldo_kas" name="saldo_kas" value="">
                                        </div>
                                    </div>

                                    <input type="text" name="trx_type" id="trx_type" value="" hidden>

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
            const lastSaldoFromDb = <?php echo json_encode($latestKas); ?>;
            var idKasMasuk = document.getElementById('id_kasmasuk');
            var idKasKeluar = document.getElementById('id_kaskeluar');
            var jmlKasMasuk = document.getElementById('jml_kasmasuk');
            var jmlKasKeluar = document.getElementById('jml_kaskeluar');
            var saldoKas = document.getElementById('saldo_kas');

            // Set jumlah kasmasuk value based on selected id_kasmasuk
            var selectedOption = idKasMasuk.options[idKasMasuk.selectedIndex];
            jmlKasMasuk.value = selectedOption.getAttribute('data-jml') || "";

            var selectedOption = idKasKeluar.options[idKasKeluar.selectedIndex];
            jmlKasKeluar.value = selectedOption.getAttribute('data-jml') || "";

            if (idKasMasuk.value != "") {
                idKasKeluar.disabled = true;
                jmlKasKeluar.disabled = true;
                idKasKeluar.value = "";
                jmlKasKeluar.value = "";
                document.getElementById('trx_type').value = "debit";
                saldoKas.value = lastSaldoFromDb.saldo_kas ? parseInt(lastSaldoFromDb.saldo_kas) + parseInt(jmlKasMasuk.value) : 0 + parseInt(jmlKasMasuk.value);
            } else {
                idKasKeluar.disabled = false;
                jmlKasKeluar.disabled = false;
            }

            if (idKasKeluar.value != "") {
                idKasMasuk.disabled = true;
                jmlKasMasuk.disabled = true;
                idKasMasuk.value = "";
                jmlKasMasuk.value = "";
                document.getElementById('trx_type').value = "kredit";
                saldoKas.value = lastSaldoFromDb.saldo_kas ? parseInt(lastSaldoFromDb.saldo_kas) - parseInt(jmlKasKeluar.value) : 0 - parseInt(jmlKasKeluar.value);
            } else {
                idKasMasuk.disabled = false;
                jmlKasMasuk.disabled = false;
            }

        }

        document.getElementById('id_kasmasuk').addEventListener('change', toggleFields);
        document.getElementById('id_kaskeluar').addEventListener('change', toggleFields);
    </script>
</body>

</html>