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
            COALESCE(k.id_kasmasuk, '') AS id_kasmasuk,
            COALESCE(k.id_kaskeluar, '') AS id_kaskeluar,
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
            -- COALESCE(kk.id_kaskeluar, '') AS id_kaskeluar,
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

function getKasByKasMasukId($kasmasuk_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1");
    $stmt->execute([$kasmasuk_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getKasByKasKeluarId($kaskeluar_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1");
    $stmt->execute([$kaskeluar_id]);
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

    $latestTrxType = $data['trx_type'];

    if ($latestTrxType == "kredit") {
        $query = "UPDATE kas SET 
            tgl_kas = ?,
            id_kasmasuk = ?,
            jml_kasmasuk = ?,
            saldo_kas = ?
            WHERE id_kas = '$id'";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $data['tgl_kas'],
                $data['id_kasmasuk'],
                $data['jml_kasmasuk'],
                $data['saldo_kas']
            ]);
            return "success";
        } catch (PDOException $e) {
            //error
            return $e->getMessage();
        }
    } else {
        $query = "UPDATE kas SET 
            tgl_kas = ?,
            id_kasmasuk = ?,
            jml_kasmasuk = ?,
            saldo_kas = ?
            WHERE id_kas = '$id'";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $data['tgl_kas'],
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
}

function deleteKas($kas_id)
{
    global $pdo;

    // Get the kas entry to be deleted
    $kas = getKasById($kas_id);

    // Determine the difference value and the operator
    $dif_value = 0;
    if ($kas['id_kasmasuk']) {
        $dif_value = $kas['jml_kasmasuk'];
    } else {
        $dif_value = -$kas['jml_kaskeluar'];
    }

    // Update saldo_kas for subsequent entries
    $queryUpdate = "UPDATE kas
                    SET saldo_kas = saldo_kas - ?
                    WHERE created_at > ?";

    try {
        $stmtUpdate = $pdo->prepare($queryUpdate);
        $stmtUpdate->execute([$dif_value, $kas['created_at']]);
    } catch (PDOException $e) {
        // error
        return $e->getMessage();
    }

    // Delete the kas entry
    $queryDelete = "DELETE FROM kas WHERE id_kas = ?";

    try {
        $stmtDelete = $pdo->prepare($queryDelete);
        $stmtDelete->execute([$kas_id]);
        header("Location: kas.php");
    } catch (PDOException $e) {
        // error
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

    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1;");
    $stmt->execute([$data['id_kasmasuk']]);
    $kasMasuk = $stmt->fetch();

    if (!empty($kasMasuk)) {
        return "data id kas masuk telah di gunakan";
    }


    $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1;");
    $stmt->execute([$data['id_kaskeluar']]);
    $kasKeluar = $stmt->fetch();

    if (!empty($kasKeluar)) {
        return "data id kas keluar telah di gunakan";
    }

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

function getAllRelationKas($id)
{
    $id_code = substr($id, 0, 2);
    $id_type = '';

    if (preg_match('~[0-9]+~', $id_code)) {
        $id_code = "id_kas";
    }

    $result = [
        'donasi' => null,
        'infaq' => null,
        'pengeluaran' => null,
        'kasmasuk' => null,
        'kaskeluar' => null,
        'kas' => null,
    ];

    switch ($id_code) {
        case 'KD':
            $id_type = 'id_donasi';


            $donasi = getDetailedDonasiById($id);
            $kasmasuk = getKasMasukByDonasiId($donasi['id_donasi']);
            $kas = getKasByKasMasukId($kasmasuk['id_kas']);

            $result = ['donasi' => $donasi, 'kasmasuk' => $kasmasuk, 'kas' => $kas];
            break; // Add break to avoid fallthrough
        case 'KI':
            $id_type = 'id_infaq';

            $infaq = getDetailedInfaqbyId($id);
            $kasmasuk = getKasMasukByInfaqId($infaq['id_infaq']);
            $kas = getKasByKasMasukId($kasmasuk['id_kasmasuk']);

            $result = ['infaq' => $infaq, 'kasmasuk' => $kasmasuk, 'kas' => $kas];
            break;
        case 'DK':
            $id_type = 'id_transaksi_keluar';

            $pengeluaran = getDetailedPengeluaranById($id);
            $kas_keluar = getKasKeluarByPengeluaranId($id);
            $kas = getKasByKasKeluarId($kas_keluar['id_kaskeluar']);

            $result = ['pengeluaran' => $pengeluaran, 'kaskeluar' => $kas_keluar, 'kas' => $kas];
            break;
        case 'KM':
            $id_type = 'id_kasmasuk';

            $kasmasuk = getKasMasukById($id);
            $infaq = getDetailedInfaqbyId($id);
            $kas = getKasByKasMasukId($kasmasuk['id_kas']);



            //donasi
            if (!$kasmasuk['id_infaq']) {
                $donasi = getDetailedDonasiById($kasmasuk['id_donasi']);
                $result = ['donasi' => $infaq, 'kasmasuk' => $kasmasuk, 'kas' => $kas];
                break;
            }

            //infaq
            $infaq = getDetailedInfaqbyId($kasmasuk['id_infaq']);
            $result = ['infaq' => $infaq, 'kasmasuk' => $kasmasuk, 'kas' => $kas];
            break;
        case 'KK':
            $id_type = 'id_kaskeluar';
            $kas_keluar = getKasKeluarById($id);
            $pengeluaran = getDetailedPengeluaranById($kas_keluar['id_transaksi_keluar']);
            $kas = getKasByKasKeluarId($kas_keluar['id_kaskeluar']);

            $result = ['pengeluaran' => $pengeluaran, 'kaskeluar' => $kas_keluar, 'kas' => $kas];

            break;
        default:
            return false;
    }


    // Construct the SQL query
    $sql = "
    SELECT
        kas.*,
        infaq.*,
        donasi.*,
        kas_masuk.*,
        kas_keluar.*,
        detail_transaksi_keluar.*
    FROM
        kas
        LEFT JOIN infaq ON infaq.id_infaq = :id AND :id_type = 'id_infaq'
        LEFT JOIN kas_masuk ON (kas_masuk.id_infaq = infaq.id_infaq OR kas_masuk.id_donasi = donasi.id_donasi) AND (:id_type = 'id_infaq' OR :id_type = 'id_donasi')
        LEFT JOIN donasi ON donasi.id_donasi = :id AND :id_type = 'id_donasi'
        LEFT JOIN kas_keluar ON kas_keluar.id_kaskeluar = :id AND :id_type = 'id_kaskeluar'
        LEFT JOIN detail_transaksi_keluar ON detail_transaksi_keluar.id_transaksi_keluar = kas_keluar.id_transaksi_keluar AND :id_type = 'id_kaskeluar'
    WHERE
        (:id_type = 'id_infaq' AND infaq.id_infaq = :id) OR
        (:id_type = 'id_donasi' AND donasi.id_donasi = :id) OR
        (:id_type = 'id_kasmasuk' AND kas_masuk.id_kasmasuk = :id) OR
        (:id_type = 'id_kas' AND kas.id_kas = :id) OR
        (:id_type = 'id_transaksi_keluar' AND detail_transaksi_keluar.id_transaksi_keluar = :id) OR
        (:id_type = 'id_kaskeluar' AND kas_keluar.id_kaskeluar = :id);
    ";

    global $pdo;

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id, 'id_type' => $id_type]);
    $result = $stmt->fetch();

    return $result;
}
