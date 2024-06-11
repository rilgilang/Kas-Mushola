<?php

function addInfaq($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert infaq
    global $pdo;

    $ids = generateAllIdForKasMasuk("infaq");

    $query = "INSERT INTO infaq (id_infaq, jenis_infaq, tgl_infaq, jml_infaq) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['infaq_id'], $data['jenis_infaq'], $data['tgl_infaq'], $data['jml_infaq']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas_masuk

    $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_infaq) VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kasmasuk_id'], $data['tgl_infaq'], $data['jml_infaq'], $data['keterangan'], $ids['infaq_id']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas
    $query = "INSERT INTO kas (id_kas, id_kasmasuk, saldo_kas) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kas_id'], $ids['kasmasuk_id'], $latestTrx['latest_saldo'] + $data['jml_infaq']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
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
            i.id_infaq,
            i.jenis_infaq,
            i.tgl_infaq,
            i.jml_infaq,
            COALESCE(km.id_kasmasuk, 0) AS id_kasmasuk,
            COALESCE(km.tgl_kasmasuk, '0000-00-00') AS tgl_kasmasuk,
            COALESCE(km.jml_kasmasuk, 0) AS jml_kasmasuk,
            COALESCE(km.ket_kasmasuk, 'No Description') AS ket_kasmasuk
        FROM 
            infaq i
        LEFT JOIN 
            kas_masuk km ON i.id_infaq = km.id_infaq
        $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
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


function getDetailedInfaq()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT k.id_kas, k.saldo_kas, km.tgl_kasmasuk, km.jml_kasmasuk, i.jenis_infaq, i.tgl_infaq, i.jml_infaq FROM kas k JOIN kas_masuk km ON k.id_kasmasuk = km.id_kasmasuk JOIN  infaq i ON km.id_infaq = i.id_infaq ORDER BY  i.tgl_infaq DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetchAll();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}
