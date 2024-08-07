<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/pengeluaran.controller.php';
include '../../controller/kas.keluar.controller.php';

checkLogin();
if (!isAdminOrTakmir()) {
    header("Location: kas_keluar.php");
}

$filter = [
    'start_date' => "",
    'end_date' => "",
];

$kas_keluar_id = $_GET['id'];
$kas_keluar = getKasKeluarById($kas_keluar_id);
$pengeluaranList = getAllPengeluaran($filter);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jenis_kaskeluar = $_POST['jenis_kaskeluar'];
    $id_transaksi_keluar = $_POST['id_transaksi_keluar'];
    $tgl_kaskeluar = $_POST['tgl_kaskeluar'];
    $ket_kaskeluar = $_POST['ket_kaskeluar'];
    $jml_kaskeluar = $_POST['jml_kaskeluar'];

    $data = [
        "jenis_kaskeluar" => $jenis_kaskeluar,
        "id_transaksi_keluar" => $id_transaksi_keluar,
        "tgl_kaskeluar" => $tgl_kaskeluar,
        "ket_kaskeluar" => $ket_kaskeluar,
        "jml_kaskeluar" => $jml_kaskeluar,
    ];

    $result = updateKasKeluar($kas_keluar_id, $data);

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

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Kas Keluar ?> </h4>
                                <h4 class="card-title" id="selectedValue"></h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="edit_kas_keluar.php?id=<?= $kas_keluar['id_kaskeluar'] ?>">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Keluar</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="id_kaskeluar" value="<?= $kas_keluar['id_kaskeluar'] ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Jenis Kas Keluar</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="jenis_kaskeluar" name="jenis_kaskeluar">
                                                <option value="Transaksi Keluar">Transaksi Keluar</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Transaksi Kas Keluar</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_transaksi_keluar" name="id_transaksi_keluar">
                                                <?php foreach ($pengeluaranList as $key => $kaskeluar) { ?>
                                                    <option value="<?= $kaskeluar['id_transaksi_keluar'] ?>" data-jenis="<?= $kaskeluar['jenis_transaksi_keluar'] ?>" data-jml="<?= $kaskeluar['jml_transaksi_keluar'] ?>" <?= $kas_keluar['id_transaksi_keluar'] == $kaskeluar['id_transaksi_keluar'] ? 'selected' : '' ?>><?= $kaskeluar['id_transaksi_keluar'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Tanggal Kas Keluar</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" placeholder="DD/MM/YYYY" name="tgl_kaskeluar" value="<?= $kas_keluar['tgl_kaskeluar'] ?>">
                                        </div>
                                    </div>

                                    <div class=" form-group row">
                                        <label for="exampleInputConfirmPassword2" class="col-sm-3 col-form-label">Keterangan Kas Keluar</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="4" id="ket_kaskeluar" name="ket_kaskeluar"><?= $kas_keluar['ket_kaskeluar'] ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumlah" id="jml_kaskeluar" name="jml_kaskeluar" value="<?= $kas_keluar['jml_kaskeluar'] ?>">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <button class="btn btn-light">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="https://www.bootstrapdash.com/" target="_blank">bootstrapdash.com </a>2021</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Only the best <a href="https://www.bootstrapdash.com/" target="_blank"> Bootstrap dashboard </a> templates</span>
                    </div>
                </footer> -->
            </div>
        </div>
    </div>

    <?php include("../component/plugin.php"); ?>

    <script>
        const pengeluaranList = <?php echo json_encode($pengeluaranList); ?>;

        function toggleFields() {
            var idKasKeluar = document.getElementById('id_transaksi_keluar').value;
            var selectedKasKeluar = pengeluaranList.find(kasKeluar => kasKeluar.id_transaksi_keluar == idKasKeluar);
            if (selectedKasKeluar) {
                document.getElementById('ket_kaskeluar').value = selectedKasKeluar.jenis_transaksi_keluar;
                document.getElementById('jml_kaskeluar').value = selectedKasKeluar.jml_transaksi_keluar;
            }
        }


        document.getElementById('id_transaksi_keluar').addEventListener('change', function() {
            toggleFields();
        });
    </script>
</body>

</html>