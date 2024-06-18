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

    $query = "INSERT INTO kas_keluar (id_kaskeluar, tgl_kaskeluar, jenis_kaskeluar, ket_kaskeluar, jml_kaskeluar, id_transaksi_keluar) VALUES (?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['id_kaskeluar'], $data['tgl_kaskeluar'], $data['jenis_kaskeluar'], $data['ket_kaskeluar'], $data['jml_kaskeluar'], $data['id_transaksi_keluar']]);
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


function getKasKeluarById($id)
{
    global $pdo;

    $query = " SELECT * FROM kas_keluar WHERE id_kaskeluar = $id ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();

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



function updateKasKeluar($id, $data)
{
    global $pdo;

    //update donasi
    $query = "UPDATE kas_keluar
    SET jenis_kaskeluar = ?, id_transaksi_keluar = ?, tgl_kaskeluar = ?, ket_kaskeluar = ?, jml_kaskeluar = ?
    WHERE id_kaskeluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_kaskeluar'], $data['id_transaksi_keluar'],  $data['tgl_kaskeluar'], $data['ket_kaskeluar'], $data['jml_kaskeluar'], $id]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}


function deleteKasKeluar($id)
{
    global $pdo;

    //update kas
    $query = "DELETE FROM kas_keluar
       WHERE id_kaskeluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        header("Location: kas_masuk.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}
