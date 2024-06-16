<?php

function getDetailedDonasiById($donasi_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT k.id_kas, d.id_donasi, km.id_kasmasuk ,k.saldo_kas, km.tgl_kasmasuk, km.jml_kasmasuk, km.ket_kasmasuk, d.nama_donatur, d.tgl_donasi, d.jml_donasi, d.created_at FROM kas k JOIN kas_masuk km ON k.id_kasmasuk = km.id_kasmasuk JOIN donasi d ON km.id_donasi = d.id_donasi WHERE d.id_donasi = ? LIMIT 1;");
    $stmt->execute([$donasi_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}


function addDonasi($data)
{
    $latestTrx = getLatestTypeTrx();


    //insert donasi
    global $pdo;

    $ids = generateAllIdForKas("donasi");

    $query = "INSERT INTO donasi (id_donasi, nama_donatur, tgl_donasi, jml_donasi) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids["donasi_id"], $data['nama_donatur'], $data['tgl_donasi'], $data['jml_donasi']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas_masuk
    $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_donasi) VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kasmasuk_id'], $data['tgl_donasi'], $data['jml_donasi'], $data['keterangan'], $ids['donasi_id']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    //insert kas
    $query = "INSERT INTO kas (id_kas ,id_kasmasuk, saldo_kas) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kas_id'], $ids['kasmasuk_id'], $latestTrx['latest_saldo'] + $data['jml_donasi']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function getAllDonasi($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_donasi BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT 
            d.id_donasi,
            d.tgl_donasi,
            d.nama_donatur,
            d.jml_donasi,
            COALESCE(km.id_kasmasuk, 0) AS id_kasmasuk,
            COALESCE(km.tgl_kasmasuk, '0000-00-00') AS tgl_kasmasuk,
            COALESCE(km.jml_kasmasuk, 0) AS jml_kasmasuk,
            COALESCE(km.ket_kasmasuk, 'No Description') AS ket_kasmasuk
        FROM 
            donasi d
        LEFT JOIN 
            kas_masuk km ON d.id_donasi = km.id_donasi
        $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
}

function getLatestDonasi()
{

    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM donasi ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $donasi = $stmt->fetch();

    if (empty($donasi)) {
        return false;
    }

    return $donasi;
}


function generateDonasiId($lastId)
{
    $result = "KD";
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

function deleteDonasi($donasi_id)
{
    $donasi = getDetailedDonasiById($donasi_id);
    $dif_value = $donasi['jml_donasi'];

    global $pdo;

    //delete donasi
    $query = "DELETE FROM donasi
       WHERE id_donasi = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$donasi['id_donasi']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    deleteKasMasuk($donasi['id_kasmasuk']);
    deleteKas($donasi['id_kas']);

    syncSaldo("(saldo_kas - $dif_value)", $donasi['created_at']);
}

function updateDonasi($donasi_id, $data)
{
    global $pdo;

    $donasi = getDetailedDonasiById($donasi_id);
    $dif_value =  $data['jml_donasi'] > $donasi['jml_donasi'] ? $data['jml_donasi'] -  $donasi['jml_donasi'] : $donasi['jml_donasi'] - $data['jml_donasi'];
    $math_op_query = $data['jml_donasi'] > $donasi['jml_donasi'] ? "($dif_value + saldo_kas)" : "(saldo_kas - $dif_value)";

    //update donasi
    $query = "UPDATE donasi
    SET nama_donatur = ?, jml_donasi = ?, tgl_donasi = ?
    WHERE id_donasi = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['nama_donatur'], $data['jml_donasi'], $data['tgl_donasi'], $donasi_id]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $data['tgl_kasmasuk'] = $data['tgl_donasi'];
    $data['jml_kasmasuk'] = $data['jml_donasi'];

    syncKasMasuk($data, $donasi['created_at']);

    syncSaldo($math_op_query, $donasi['created_at']);
}


function sumAllDonasi()
{
    global $pdo;

    //get all sum of kas masuk
    $query = "SELECT 
    COALESCE(SUM(jml_donasi), 0) AS total_donasi
    FROM 
    donasi;";

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
