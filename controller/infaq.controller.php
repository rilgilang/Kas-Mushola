<?php

function addInfaq($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert infaq
    global $pdo;

    $ids = generateAllIdForKas("infaq");

    $query = "INSERT INTO infaq (id_infaq, jenis_infaq, tgl_infaq, jml_infaq) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['infaq_id'], $data['jenis_infaq'], $data['tgl_infaq'], $data['jml_infaq']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    // //insert kas_masuk

    // $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_infaq) VALUES (?, ?, ?, ?, ?)";

    // try {
    //     $stmt = $pdo->prepare($query);
    //     $stmt->execute([$ids['kasmasuk_id'], $data['tgl_infaq'], $data['jml_infaq'], $data['keterangan'], $ids['infaq_id']]);
    // } catch (PDOException $e) {
    //     //error
    //     return $e->getMessage();
    // }

    // //insert kas
    // $query = "INSERT INTO kas (id_kas, id_kasmasuk, saldo_kas) VALUES (?, ?, ?)";

    // try {
    //     $stmt = $pdo->prepare($query);
    //     $stmt->execute([$ids['kas_id'], $ids['kasmasuk_id'], $latestTrx['latest_saldo'] + $data['jml_infaq']]);
    //     return "success";
    // } catch (PDOException $e) {
    //     //error
    //     return $e->getMessage();
    // }
}

function getAllInfaq($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_infaq BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT 
           *
        FROM 
            infaq i
        $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}

function getLatestInfaq()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM infaq ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $donasi = $stmt->fetch();

    if (empty($donasi)) {
        return false;
    }

    return $donasi;
}

function getDetailedInfaqbyId($infaqId)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT k.id_kas, i.id_infaq, km.id_kasmasuk, k.saldo_kas, km.tgl_kasmasuk, km.jml_kasmasuk, km.ket_kasmasuk, i.jenis_infaq, i.tgl_infaq, i.jml_infaq, i.created_at FROM kas k JOIN kas_masuk km ON k.id_kasmasuk = km.id_kasmasuk JOIN  infaq i ON km.id_infaq = i.id_infaq WHERE i.id_infaq = ? LIMIT 1;");
    $stmt->execute([$infaqId]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function generateInfaqId($lastId)
{
    $result = "KI";
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

function getInfaqbyId($infaqId)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM infaq WHERE id_infaq = ? LIMIT 1;");
    $stmt->execute([$infaqId]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function updateInfaq($infaqId, $data)
{
    global $pdo;

    $infaq = getInfaqbyId($infaqId);
    $dif_value =  $data['jml_infaq'] > $infaq['jml_infaq'] ? $data['jml_infaq'] -  $infaq['jml_infaq'] : $infaq['jml_infaq'] - $data['jml_infaq'];
    $math_op_query = $data['jml_infaq'] > $infaq['jml_infaq'] ? "($dif_value + saldo_kas)" : "(saldo_kas - $dif_value)";

    //update infaq
    $query = "UPDATE infaq
    SET jenis_infaq = ?, jml_infaq = ?, tgl_infaq = ?
    WHERE id_infaq = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_infaq'], $data['jml_infaq'], $data['tgl_infaq'], $infaqId]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $data['tgl_kasmasuk'] = $data['tgl_infaq'];
    $data['jml_kasmasuk'] = $data['jml_infaq'];

    syncKasMasuk($data, $infaq['created_at']);

    syncSaldo($math_op_query, $infaq['created_at']);
}


function deleteInfaq($infaqId)
{
    $infaq = getDetailedInfaqbyId($infaqId);
    $dif_value = $infaq['jml_infaq'];

    global $pdo;

    //delete infaq
    $query = "DELETE FROM infaq
       WHERE id_infaq = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$infaq['id_infaq']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    deleteKasMasuk($infaq['id_kasmasuk']);
    deleteKas($infaq['id_kas']);

    syncSaldo("(saldo_kas - $dif_value)", $infaq['created_at']);
}

function sumAllInfaq()
{
    global $pdo;

    //get all sum of infaq masuk
    $query = "SELECT 
    COALESCE(SUM(jml_infaq), 0) AS total_infaq
    FROM 
    infaq;";

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
