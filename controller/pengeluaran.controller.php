<?php


function addPengeluaran($data)
{
    //insert transaksi_keluar
    global $pdo;


    $query = "INSERT INTO detail_transaksi_keluar (id_transaksi_keluar ,jenis_transaksi_keluar, tgl_transaksi_keluar, jml_transaksi_keluar, file) VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['id_transaksi_keluar'], $data['jenis_transaksi_keluar'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['file']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas_keluar

    // $query = "INSERT INTO kas_keluar (id_kaskeluar, tgl_kaskeluar, jml_kaskeluar, ket_kaskeluar, id_transaksi_keluar) VALUES (?, ?, ?, ?, ?)";

    // try {
    //     $stmt = $pdo->prepare($query);
    //     $stmt->execute([$ids['kas_keluar_id'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['keterangan'], $ids['transaksi_keluar_id']]);
    // } catch (PDOException $e) {
    //     //error
    //     return $e->getMessage();
    // }

    // //insert kas
    // $query = "INSERT INTO kas (id_kas ,id_kaskeluar, saldo_kas) VALUES (?, ?, ?)";

    // try {
    //     $stmt = $pdo->prepare($query);
    //     $stmt->execute([$ids['kas_id'], $ids['kas_keluar_id'], $latestTrx['latest_saldo'] - $data['jml_transaksi_keluar']]);
    //     return "success";
    // } catch (PDOException $e) {
    //     //error
    //     return $e->getMessage();
    // }
}

function getAllPengeluaran($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_transaksi_keluar BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT 
            *
        FROM 
            detail_transaksi_keluar
        $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
}


function generatePengeluaranId($lastId)
{
    $result = "KP";
    $newNumber = intval(substr($lastId, 3)) + 1;
    if (strlen($newNumber) <= 3) {
        for ($x = 0; $x <= 3 - strlen($newNumber); $x++) {
            $result = $result . "0";
        }
        $result = $result . $newNumber;
        return $result;
    } else {
        $result = $newNumber;
        return $result;
    }
}

function getLatestPengeluaran()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM detail_transaksi_keluar ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $donasi = $stmt->fetch();

    if (empty($donasi)) {
        return false;
    }

    return $donasi;
}

function getDetailedPengeluaranById($pengeluaran_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM detail_transaksi_keluar WHERE id_transaksi_keluar = ? LIMIT 1;");
    $stmt->execute([$pengeluaran_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function updatePengeluaranById($pengeluaran_id, $data)
{
    global $pdo;

    $pengeluaran = getDetailedPengeluaranById($pengeluaran_id);
    // $dif_value =  $data['jml_transaksi_keluar'] > $pengeluaran['jml_transaksi_keluar'] ? $data['jml_transaksi_keluar'] -  $pengeluaran['jml_transaksi_keluar'] : $pengeluaran['jml_transaksi_keluar'] - $data['jml_transaksi_keluar'];
    // $math_op_query = $data['jml_transaksi_keluar'] > $pengeluaran['jml_transaksi_keluar'] ? "($dif_value + saldo_kas)" : "(saldo_kas - $dif_value)";

    //update pengeluaran
    $query = "UPDATE detail_transaksi_keluar
    SET jenis_transaksi_keluar = ?, jml_transaksi_keluar = ?, tgl_transaksi_keluar = ?, file = ? 
    WHERE id_transaksi_keluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['tgl_transaksi_keluar'], $data['file'], $pengeluaran_id]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    // $data['tgl_kaskeluar'] = $data['tgl_transaksi_keluar'];
    // $data['jml_kaskeluar'] = $data['jml_transaksi_keluar'];

    // syncKasKeluar($data, $pengeluaran['created_at']);

    // syncSaldo($math_op_query, $pengeluaran['created_at']);
}

function deletePengeluaran($pengeluaran_id)
{
    global $pdo;

    //delete pengeluaran
    $query = "DELETE FROM detail_transaksi_keluar
       WHERE id_transaksi_keluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pengeluaran_id]);
        header("Location: donasi.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function sumAllPengeluaran()
{
    global $pdo;

    //get all sum of kas keluar
    $query = "SELECT 
    COALESCE(SUM(jml_transaksi_keluar), 0) AS total_kaskeluar
FROM 
    detail_transaksi_keluar;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        return $kas;
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}
