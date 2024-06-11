<?php


function addPengeluaran($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert transaksi_keluar
    global $pdo;

    $query = "INSERT INTO detail_transaksi_keluar (jenis_transaksi_keluar, tgl_transaksi_keluar, jml_transaksi_keluar) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_transaksi_keluar'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    $stmt = $pdo->prepare("SELECT * FROM detail_transaksi_keluar ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $transaksi_keluar = $stmt->fetch();

    //insert kas_keluar

    $query = "INSERT INTO kas_keluar (tgl_kaskeluar, jml_kaskeluar, ket_kaskeluar, id_transaksi_keluar) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['keterangan'], $transaksi_keluar['id_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $stmt = $pdo->prepare("SELECT * FROM kas_keluar ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $kaskeluar = $stmt->fetch();

    //insert kas
    $query = "INSERT INTO kas (id_kaskeluar, saldo_kas) VALUES (?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kaskeluar['id_kaskeluar'], $latestTrx['latest_saldo'] - $data['jml_transaksi_keluar']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
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
            dtk.id_transaksi_keluar,
            dtk.tgl_transaksi_keluar,
            dtk.jenis_transaksi_keluar,
            dtk.jml_transaksi_keluar,
            COALESCE(kk.ket_kaskeluar, 'No Description') AS ket_kaskeluar
        FROM 
            detail_transaksi_keluar dtk
        LEFT JOIN 
            kas_keluar kk ON dtk.id_transaksi_keluar = kk.id_transaksi_keluar
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
