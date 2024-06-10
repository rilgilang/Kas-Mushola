<?php


function addPengeluaran($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert transaksi_keluar
    global $pdo;

    $query = "INSERT INTO detail_transaksi_keluar (jenis_transaksi_keluar, tgl_transaksi_keluar, jml_transaksi_keluar) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_transaksi_keluar'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }


    $stmt = $pdo->prepare("SELECT * FROM detail_transaksi_keluar ORDER BY tgl_transaksi_keluar DESC LIMIT 1;");
    $stmt->execute();
    $transaksi_keluar = $stmt->fetch();

    //insert kas_keluar

    $query = "INSERT INTO kas_keluar (tgl_kaskeluar, jml_kaskeluar, ket_kaskeluar, id_transaksi_keluar) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['keterangan'], $transaksi_keluar['id_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    $stmt = $pdo->prepare("SELECT * FROM kas_keluar ORDER BY tgl_kaskeluar DESC LIMIT 1;");
    $stmt->execute();
    $kaskeluar = $stmt->fetch();

    //insert kas
    $query = "INSERT INTO kas (id_kaskeluar, saldo_kas) VALUES (?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kaskeluar['id_kaskeluar'], $latestTrx['latest_saldo'] - $data['jml_transaksi_keluar']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}
