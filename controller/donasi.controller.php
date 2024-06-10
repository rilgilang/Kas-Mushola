<?php

function getDetailedDonasi()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT k.id_kas, k.saldo_kas, km.tgl_kasmasuk, km.jml_kasmasuk, d.nama_donatur, d.tgl_donasi, d.jml_donasi FROM kas k JOIN kas_masuk km ON k.id_kasmasuk = km.id_kasmasuk");
    $stmt->execute();
    $kas = $stmt->fetchAll();

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

    $query = "INSERT INTO donasi (nama_donatur, tgl_donasi, jml_donasi) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['nama_donatur'], $data['tgl_donasi'], $data['jml_donasi']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    $stmt = $pdo->prepare("SELECT * FROM donasi ORDER BY tgl_donasi DESC LIMIT 1;");
    $stmt->execute();
    $donasi = $stmt->fetch();

    //insert kas_masuk

    $query = "INSERT INTO kas_masuk (tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_donasi) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_donasi'], $data['jml_donasi'], $data['keterangan'], $donasi['id_donasi']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk ORDER BY tgl_kasmasuk DESC LIMIT 1;");
    $stmt->execute();
    $kasmasuk = $stmt->fetch();

    //insert kas
    $query = "INSERT INTO kas (id_kasmasuk, saldo_kas) VALUES (?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kasmasuk['id_kasmasuk'], $latestTrx['latest_saldo'] + $data['jml_donasi']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function getAllDonasi()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM donasi");
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
}
