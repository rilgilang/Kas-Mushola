<?php


function addInfaq($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert infaq
    global $pdo;

    $query = "INSERT INTO infaq (jenis_infaq, tgl_infaq, jml_infaq) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_infaq'], $data['tgl_infaq'], $data['jml_infaq']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    $stmt = $pdo->prepare("SELECT * FROM infaq ORDER BY tgl_infaq DESC LIMIT 1;");
    $stmt->execute();
    $infaq = $stmt->fetch();

    //insert kas_masuk

    $query = "INSERT INTO kas_masuk (tgl_kasmasuk, jml_kasmasuk, ket_kasmasuk, id_infaq) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_infaq'], $data['jml_infaq'], $data['keterangan'], $infaq['id_infaq']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk ORDER BY tgl_kasmasuk DESC LIMIT 1;");
    $stmt->execute();
    $kasmasuk = $stmt->fetch();

    print_r($latestTrx);

    //insert kas
    $query = "INSERT INTO kas (id_kasmasuk, saldo_kas) VALUES (?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kasmasuk['id_kasmasuk'], $latestTrx['latest_saldo'] + $data['jml_infaq']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function getAllInfaq()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM infaq");
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
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
