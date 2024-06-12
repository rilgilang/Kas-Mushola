<?php

function getDashboardData()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas");
    $stmt->execute();
    $kas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "total_pengeluaran" => 0,
        "total_pemasukan" => 0,
        "saldo_akhir" => 0,
    ];

    foreach ($kas as $val) {
        switch ($val['jenis']) {
            case 'pengeluaran':
                $data["total_pengeluaran"] += $val["kredit"];
            case 'pemasukan':
                $data["total_pemasukan"] += $val["debit"];
        }
    };
    return $data;
}

function getAllKas($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE 
         (km.tgl_kasmasuk BETWEEN '$startDate' AND '$endDate' OR
         i.tgl_infaq BETWEEN '$startDate' AND '$endDate' OR
         d.tgl_donasi BETWEEN '$startDate' AND '$endDate' OR
         kk.tgl_kaskeluar BETWEEN '$startDate' AND '$endDate' OR
         dt.tgl_transaksi_keluar BETWEEN '$startDate' AND '$endDate')";
    } else {
        $filter_query = "ORDER BY kas_created_at ASC";
    }

    $query = "SELECT 
            k.id_kas,
            COALESCE(k.saldo_kas, 0) AS saldo_kas,
            COALESCE(k.created_at, '0000-00-00') AS kas_created_at,
            COALESCE(km.tgl_kasmasuk, '0000-00-00') AS tgl_kasmasuk,
            COALESCE(km.jml_kasmasuk, 0) AS jml_kasmasuk,
            COALESCE(km.ket_kasmasuk, 'No Description') AS ket_kasmasuk,
            COALESCE(d.nama_donatur, 'No Donatur') AS nama_donatur,
            COALESCE(d.tgl_donasi, '0000-00-00') AS tgl_donasi,
            COALESCE(d.jml_donasi, 0) AS jml_donasi,
            COALESCE(i.jenis_infaq, 'No Infaq') AS jenis_infaq,
            COALESCE(i.tgl_infaq, '0000-00-00') AS tgl_infaq,
            COALESCE(i.jml_infaq, 0) AS jml_infaq,
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

function getKasMasukByInfaqId($infaq_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk WHERE id_infaq = ? DESC LIMIT 1;");
    $stmt->execute([$infaq_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getKasById($kas_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kas = ? DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getLatestTypeTrx()
{
    $latest = [
        "type" => '',
        "value" => 0,
        "latest_saldo" => 0,
    ];

    global $pdo;

    $kas_masuk = getLatestKasMasuk();
    $kas_keluar = getLatestKasKeluar();


    if ($kas_masuk == false && $kas_keluar == false) {
        return $latest;
    }

    if (!$kas_masuk && $kas_keluar) {
        $latest['type'] = 'kredit';
        $latest['value'] = $kas_keluar['jml_kaskeluar'];

        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1;");
        $stmt->execute([$kas_keluar["id_kaskeluar"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];

        return $latest;
    } else {
        $latest['type'] = 'debit';
        $latest['value'] = $kas_masuk['jml_kasmasuk'];

        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1;");
        $stmt->execute([$kas_masuk["id_kasmasuk"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];

        return $latest;
    }



    if ($kas_masuk['created_at'] >  $kas_keluar['created_at']) {
        $latest['type'] = 'debit';
        $latest['value'] = $kas_masuk['jml_kasmasuk'];
    } else {
        $latest['type'] = 'kredit';
        $latest['value'] = $kas_keluar['jml_kaskeluar'];
    }

    if ($latest['type'] = 'debit') {
        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1;");
        $stmt->execute([$kas_masuk["id_kasmasuk"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1;");
        $stmt->execute([$kas_keluar["id_kaskeluar"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];
    }

    return $latest;
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

function getLatestKasMasuk()
{

    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getLatestKasKeluar()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas_keluar ORDER BY created_at DESC LIMIT 1;");
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

function generateKasMasukId($lastId)
{
    $result = "KM";
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

function generateKasKeluarId($lastId)
{
    $result = "KK";
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


function generateAllIdForKas($type)
{
    $donasi_id = "KD0001";
    $infaq_id = "KI0001";
    $kasmasuk_id = "KM001";
    $detail_transaksi_keluar_id = "DK001";
    $kaskeluar_id = "KK001";
    $kas_id = "K001";
    $kasmasuk = getLatestKasMasuk();
    $kaskeluar = getLatestKasKeluar();
    $kas = getLatestKas();

    if ($kasmasuk != false) {
        $kasmasuk_id = generateKasMasukId($kasmasuk['id_kasmasuk']);
    }


    if ($kaskeluar != false) {
        $kaskeluar_id = generateKasKeluarId($kaskeluar['id_kaskeluar']);
    }

    if ($kas != false) {
        $kas_id = generateKasId($kas['id_kas']);
    }

    switch ($type) {
        case 'donasi':
            $donasi = getLatestDonasi();
            if ($donasi != false) {
                $donasi_id = generateDonasiId($donasi['id_donasi']);
            }
            break;

        case 'infaq':
            $infaq = getLatestInfaq();
            if ($infaq != false) {
                $infaq_id = generateInfaqId($infaq['id_infaq']);
            }
            break;

        case 'pengeluaran':
            $pengeluaran = getLatestPengeluaran();
            if ($pengeluaran != false) {
                $detail_transaksi_keluar_id = generatePengeluaranId($pengeluaran['id_transaksi_keluar']);
            }
            break;

        default:
            return false;
    }

    return [
        'donasi_id' => $donasi_id,
        'infaq_id' => $infaq_id,
        'kasmasuk_id' => $kasmasuk_id,
        'kas_id' => $kas_id,
        'transaksi_keluar_id' => $detail_transaksi_keluar_id,
        'kas_keluar_id' => $kaskeluar_id
    ];
}


function syncKasMasuk($data, $created_at)
{
    global $pdo;

    //update kas masuk
    $query = "UPDATE kas_masuk
     SET tgl_kasmasuk = ?, jml_kasmasuk = ?, ket_kasmasuk = ?
     WHERE created_at >= ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_infaq'], $data['jml_infaq'], $data['keterangan'], $created_at]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
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


function deleteKasMasuk($kasmasuk_id)
{
    global $pdo;

    //update kas
    $query = "DELETE FROM kas_masuk
       WHERE id_kasmasuk = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kasmasuk_id]);
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
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}
