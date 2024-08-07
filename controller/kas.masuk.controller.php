<?php

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


function syncKasMasuk($data, $created_at)
{
    global $pdo;

    //update kas masuk
    $query = "UPDATE kas_masuk
     SET tgl_kasmasuk = ?, jml_kasmasuk = ?, ket_kasmasuk = ?
     WHERE created_at >= ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_kasmasuk'], $data['jml_kasmasuk'], $data['keterangan'], $created_at]);
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
        header("Location: kas_masuk.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function addKasMasuk($data)
{

    global $pdo;
    

    $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_donasi) VALUES (?, ?, ?, ?, ?)";

    if ($data['jenis_kasmasuk'] == "infaq") {

        $stmt = $pdo->prepare("SELECT * FROM kas_masuk WHERE id_infaq = ? LIMIT 1;");
        $stmt->execute([$data['id_infaq']]);
        $cek_infaq = $stmt->fetch();
    
        if (!empty($cek_infaq)) {
            return "data id infaq telah di gunakan";
        }

        //insert kas_masuk
        $query = "INSERT INTO kas_masuk (id_kasmasuk, tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_infaq) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$data['id_kasmasuk'], $data['tgl_kasmasuk'], $data['jml_kasmasuk'], $data['ket_kasmasuk'], $data['id_infaq']]);
            header("Location: kas_masuk.php");
        } catch (PDOException $e) {
            //error
            return $e->getMessage();
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk WHERE id_donasi = ? LIMIT 1;");
    $stmt->execute([$data['id_donasi']]);
    $cek_donasi = $stmt->fetch();

    if (!empty($cek_donasi)) {
        return "data id donasi telah di gunakan";
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['id_kasmasuk'], $data['tgl_kasmasuk'], $data['jml_kasmasuk'], $data['ket_kasmasuk'], $data['id_donasi']]);
        header("Location: kas_masuk.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}


function getAllKasMasuk($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_kasmasuk BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT * FROM kas_masuk $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}

function sumAllKasMasuk()
{
    global $pdo;

    $query = "SELECT 
    COALESCE(SUM(jml_kasmasuk), 0) AS jml_kasmasuk
FROM 
    kas_masuk km LIMIT 1;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch();

    return $result;
}

function updateKasMasuk($id, $data)
{
    global $pdo;

    //update donasi
    $query = "UPDATE kas_masuk
    SET jenis_kasmasuk = ?, id_donasi = ?, id_infaq = '', tgl_kasmasuk = ?, ket_kasmasuk = ?, jml_kasmasuk = ?
    WHERE id_kasmasuk = ?;";

    if ($data['jenis_kasmasuk'] == "infaq") {
        //insert kas_masuk
        //update donasi
        $query = "UPDATE kas_masuk
            SET jenis_kasmasuk = ?, id_infaq = ?, id_donasi = '', tgl_kasmasuk = ?, ket_kasmasuk = ?, jml_kasmasuk = ?
            WHERE id_kasmasuk = ?;";

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute([$data['jenis_kasmasuk'], $data['id_infaq'],  $data['tgl_kasmasuk'], $data['ket_kasmasuk'], $data['jml_kasmasuk'], $id]);
            header("Location: kas_masuk.php");
        } catch (PDOException $e) {
            //error
            return $e->getMessage();
        }
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_kasmasuk'], $data['id_donasi'],  $data['tgl_kasmasuk'], $data['ket_kasmasuk'], $data['jml_kasmasuk'], $id]);
        header("Location: kas_masuk.php");
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}


function getKasMasukById($id)
{
    global $pdo;

    $query = " SELECT * FROM kas_masuk WHERE id_kasmasuk = ? ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $result = $stmt->fetch();

    return $result;
}
