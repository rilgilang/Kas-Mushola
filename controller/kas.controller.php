<?php

function getDashboardData()
{
    global $pdo;

    $result = [
        'total_infaq' => sumAllInfaq() != false ?  sumAllInfaq()['total_infaq'] : 0,
        'total_donasi' => sumAllDonasi() != false ?  sumAllDonasi()['total_donasi'] : 0,
        'total_pengeluaran' => sumAllPengeluaran() != false ? sumAllPengeluaran()['total_kaskeluar'] : 0,
        'total_saldo' => getLatestSaldo() != false ? getLatestSaldo()['saldo_kas'] : 0,
    ];

    return $result;
}

function getAllKas($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_kas BETWEEN '$startDate' AND '$endDate' ";
    } else {
        $filter_query = "ORDER BY kas_created_at ASC";
    }

    $query = "SELECT 
            k.id_kas,
            COALESCE(k.saldo_kas, 0) AS saldo_kas,
            COALESCE(k.tgl_kas, '0000-00-00') AS tgl_kas,
            COALESCE(k.created_at, '0000-00-00') AS kas_created_at,
            COALESCE(km.tgl_kasmasuk, '0000-00-00') AS tgl_kasmasuk,
            COALESCE(km.jml_kasmasuk, 0) AS jml_kasmasuk,
            COALESCE(km.ket_kasmasuk, 'No Description') AS ket_kasmasuk,
            COALESCE(d.id_donasi, 'No Donatur') AS id_donasi,
            COALESCE(d.nama_donatur, 'No Donatur') AS nama_donatur,
            COALESCE(d.tgl_donasi, '0000-00-00') AS tgl_donasi,
            COALESCE(d.jml_donasi, 0) AS jml_donasi,
            COALESCE(i.id_infaq, 'No Infaq') AS id_infaq,
            COALESCE(i.jenis_infaq, 'No Infaq') AS jenis_infaq,
            COALESCE(i.tgl_infaq, '0000-00-00') AS tgl_infaq,
            COALESCE(i.jml_infaq, 0) AS jml_infaq,
            COALESCE(kk.id_kaskeluar, '') AS id_kaskeluar,
            COALESCE(kk.tgl_kaskeluar, '0000-00-00') AS tgl_kaskeluar,
            COALESCE(kk.jml_kaskeluar, 0) AS jml_kaskeluar,
            COALESCE(kk.ket_kaskeluar, 'No Description') AS ket_kaskeluar,
            COALESCE(dt.jenis_transaksi_keluar, 'No Transaction') AS jenis_transaksi_keluar,
            COALESCE(dt.tgl_transaksi_keluar, '0000-00-00') AS tgl_transaksi_keluar,
            COALESCE(dt.jml_transaksi_keluar, 0) AS jml_transaksi_keluar,
            CASE 
                WHEN km.id_kasmasuk IS NOT NULL THEN 'Kredit'
                WHEN kk.id_kaskeluar IS NOT NULL THEN 'Debit'
                ELSE 'Unknown'
            END AS transaction_type
        FROM 
            kas k
        LEFT JOIN 
            kas_masuk km ON k.id_kasmasuk = km.id_kasmasuk
        LEFT JOIN 
            donasi d ON km.id_donasi = d.id_donasi
        LEFT JOIN 
            infaq i ON km.id_infaq = i.id_infaq
        LEFT JOIN 
            kas_keluar kk ON k.id_kaskeluar = kk.id_kaskeluar
        LEFT JOIN 
            detail_transaksi_keluar dt ON kk.id_transaksi_keluar = dt.id_transaksi_keluar $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kas = $stmt->fetchAll();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getKasById($kas_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kas = ? LIMIT 1;");
    $stmt->execute([$kas_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getLatestKas()
{

    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function generateKasId($lastId)
{
    $result = "K";
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

function syncSaldo($math_op_query, $created_at)
{
    global $pdo;

    //update kas
    $query = "UPDATE kas
       SET saldo_kas = $math_op_query
       WHERE created_at >= ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$created_at]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function updateKas($id, $data)
{
    global $pdo;


    $query = "UPDATE kas SET 
        tgl_kas = ?,
        id_kaskeluar = ?,
        jml_kaskeluar = ?,
        id_kasmasuk = ?,
        jml_kasmasuk = ?,
        saldo_kas = ?
        WHERE id_kas = '$id'";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $data['tgl_kas'],
            $data['id_kaskeluar'],
            $data['jml_kaskeluar'],
            $data['id_kasmasuk'],
            $data['jml_kasmasuk'],
            $data['saldo_kas']
        ]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function deleteKas($kas_id)
{
    global $pdo;

    //update kas
    $query = "DELETE FROM kas
       WHERE id_kas = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kas_id]);
        header("Location: kas.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}


function getLatestSaldo()
{
    global $pdo;

    //update kas
    $query = "SELECT * FROM kas ORDER BY created_at DESC LIMIT 1";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $kas = $stmt->fetch();
        return $kas;
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function addKas($data)
{
    global $pdo;

    // Determine the latest transaction type
    $latestTrxType = $data['trx_type'];

    // Insert into kas table based on the latest transaction type
    if ($latestTrxType == "kredit") {
        // Insert as kredit
        $query = "INSERT INTO kas (id_kas, tgl_kas, id_kaskeluar, jml_kaskeluar, saldo_kas) VALUES (?, ?, ?, ?, ?)";
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$data['id_kas'], $data['tgl_kas'], $data['id_kaskeluar'], $data['jml_kaskeluar'], $data['saldo_kas']]);
            header("Location: kas.php");
        } catch (PDOException $e) {
            // Handle error
            return $e->getMessage();
        }
    } else {
        // Insert as debit
        $query = "INSERT INTO kas (id_kas, tgl_kas, id_kasmasuk, jml_kasmasuk, saldo_kas) VALUES (?, ?, ?, ?, ?)";
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$data['id_kas'], $data['tgl_kas'], $data['id_kasmasuk'], $data['jml_kasmasuk'], $data['saldo_kas']]);
            header("Location: kas.php");
        } catch (PDOException $e) {
            // Handle error
            return $e->getMessage();
        }
    }
}

function getLatestTrxBeforeDate($date)
{
    global $pdo;

    $query = " SELECT 
            *
        FROM 
            kas
        WHERE tgl_kas < ? ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$date]);
    $result = $stmt->fetch();

    return $result;
}

function sumAllKas()
{
    global $pdo;

    //get all sum of kas keluar
    $query = "SELECT 
    COALESCE(SUM(jml_kasmasuk), 0) AS total_kasmasuk,
    COALESCE(SUM(jml_kaskeluar), 0) AS total_kaskeluar,
    COALESCE(SUM(saldo_kas), 0) AS total_saldo
FROM 
    kas;";

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
