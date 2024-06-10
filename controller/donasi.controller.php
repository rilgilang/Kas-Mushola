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
