<?php
include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/infaq.controller.php';
include '../../controller/donasi.controller.php';
include '../../controller/kas.masuk.controller.php';

checkLogin();
if (!isAdminOrTakmir()) {
    header("Location: kas_masuk.php");
}

$filter = [
    'start_date' => "",
    'end_date' => "",
];

$infaqList = getAllInfaq($filter);
$donasiList = getAllDonasi($filter);


$latestKasMasuk = getLatestKasMasuk()['id_kasmasuk'];
$lastId = generateKasMasukId($latestKasMasuk);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_kasmasuk = $lastId;
    $tgl_kasmasuk = $_POST['tgl_kasmasuk'];
    $jenis_kasmasuk = $_POST['jenis_kasmasuk'];
    $ket_kasmasuk = $_POST['ket_kasmasuk'];
    $jml_kasmasuk = $_POST['jml_kasmasuk'];
    $id_infaq = $jenis_kasmasuk == "infaq" ?  $_POST['id_infaq'] : "";
    $id_donasi = $jenis_kasmasuk == "donasi" ? $_POST['id_donasi'] : "";



    $data = [
        "id_kasmasuk" => $id_kasmasuk,
        "tgl_kasmasuk" => $tgl_kasmasuk,
        "jenis_kasmasuk" => $jenis_kasmasuk,
        "ket_kasmasuk" => $ket_kasmasuk,
        "jml_kasmasuk" => $jml_kasmasuk,
        "id_infaq" => $id_infaq,
        "id_donasi" => $id_donasi,
    ];

    $result = addKasMasuk($data);

    if ($result == "success") {
        // header("Refresh:0");
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
                                <h4 class="card-title">Tambah Kas Masuk</h4>
                                <h4 class="card-title" id="selectedValue"></h4>
                                <p class="text-danger"><?= $error !== "" ? $error : "" ?></p>
                                <form class="forms-sample" method="post" action="tambah_kas_masuk.php">
                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Kas Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="id_kasmasuk" value="<?= $lastId ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Jenis Kas Masuk</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="jenis_kasmasuk" name="jenis_kasmasuk">
                                                <option value="-" selected>Pilih salah satu</option>
                                                <option value="infaq">Infaq</option>
                                                <option value="donasi">Donasi</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Infaq</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_infaq" name="id_infaq" disabled>
                                                <option value="" selected>Pilih Id infaq</option>
                                                <?php foreach ($infaqList as $key => $infaq) { ?>
                                                    <option value="<?= $infaq['id_infaq'] ?>"><?= $infaq['id_infaq'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">ID Donasi</label>
                                        <div class="col-sm-3 align-self-center">
                                            <select class="form-control" id="id_donasi" name="id_donasi" disabled>
                                                <option value="" selected>Pilih Id donasi</option>
                                                <?php foreach ($donasiList as $key => $donasi) { ?>
                                                    <option value="<?= $donasi['id_donasi'] ?>"><?= $donasi['id_donasi'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Tanggal Kas Masuk</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" placeholder="DD/MM/YYYY" name="tgl_kasmasuk">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputConfirmPassword2" class="col-sm-3 col-form-label">ket_kasmasuk</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" rows="4" id="ket_kasmasuk" name="ket_kasmasuk"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Jumlah Kas Masuk</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="Jumlah" id="jml_kasmasuk" name="jml_kasmasuk">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                    <a href="./kas_masuk.php" class="btn btn-light">Cancel</a>
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
        const infaqList = <?php echo json_encode($infaqList); ?>;
        const donasiList = <?php echo json_encode($donasiList); ?>;

        function toggleFields() {
            var jenisKasMasuk = document.getElementById('jenis_kasmasuk').value;
            var idInfaq = document.getElementById('id_infaq');
            var idDonasi = document.getElementById('id_donasi');

            if (jenisKasMasuk === 'infaq') {
                idInfaq.disabled = false;
                idDonasi.disabled = true;
                idDonasi.value = '';
            } else if (jenisKasMasuk === 'donasi') {
                idInfaq.disabled = true;
                idInfaq.value = '';
                idDonasi.disabled = false;
            } else if (jenisKasMasuk === '-') {
                idInfaq.disabled = true;
                idInfaq.value = '';
                idDonasi.disabled = true;
                idDonasi.value = '';
            }
        }

        function fillKeteranganAndJumlah() {
            var jenisKasMasuk = document.getElementById('jenis_kasmasuk').value;

            if (jenisKasMasuk === 'infaq') {
                var idInfaq = document.getElementById('id_infaq').value;
                var selectedInfaq = infaqList.find(infaq => infaq.id_infaq == idInfaq);
                if (selectedInfaq) {
                    document.getElementById('ket_kasmasuk').value = selectedInfaq.jenis_infaq;
                    document.getElementById('jml_kasmasuk').value = selectedInfaq.jml_infaq;
                }
            } else if (jenisKasMasuk === 'donasi') {
                var idDonasi = document.getElementById('id_donasi').value;
                var selectedDonasi = donasiList.find(donasi => donasi.id_donasi == idDonasi);
                if (selectedDonasi) {
                    document.getElementById('ket_kasmasuk').value = selectedDonasi.nama_donatur;
                    document.getElementById('jml_kasmasuk').value = selectedDonasi.jml_donasi;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFields(); // Call on page load
            document.getElementById('jenis_kasmasuk').addEventListener('change', function() {
                toggleFields();
                fillKeteranganAndJumlah();
            });
            document.getElementById('id_infaq').addEventListener('change', fillKeteranganAndJumlah);
            document.getElementById('id_donasi').addEventListener('change', fillKeteranganAndJumlah);
        });
    </script>
</body>

</html>