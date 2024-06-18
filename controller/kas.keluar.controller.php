<?php

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

function syncKasKeluar($data, $created_at)
{
    global $pdo;

    //update kas masuk
    $query = "UPDATE kas_keluar
     SET tgl_kaskeluar = ?, jml_kaskeluar = ?, ket_kaskeluar = ?, file = ?
     WHERE created_at >= ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['file'], $created_at]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function addKasKeluar($data)
{

    global $pdo;

    $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_donasi) VALUES (?, ?, ?, ?, ?)";

    if ($data['jenis_kasmasuk'] == "infaq") {
        //insert kas_masuk
        $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_infaq) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$data['id_kasmasuk'], $data['tgl_kasmasuk'], $data['jml_kasmasuk'], $data['ket_kasmasuk'], $data['id_infaq']]);
            return "success";
        } catch (PDOException $e) {
            //error
            return $e->getMessage();
        }
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['id_kasmasuk'], $data['tgl_kasmasuk'], $data['jml_kasmasuk'], $data['ket_kasmasuk'], $data['id_donasi']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}


function getAllKasKeluar($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_kaskeluar BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT * FROM kas_keluar $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}


function sumAllKasKeluar()
{
    global $pdo;

    $query = "SELECT 
    COALESCE(SUM(jml_kaskeluar), 0) AS jml_kaskeluar
FROM 
    kas_keluar km LIMIT 1;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();

    return $result;
}
