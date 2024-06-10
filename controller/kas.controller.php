<?php

function getDashboardData()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas");
    $stmt->execute();
    $kas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "total_pengeluaran" => 0,
        "total_pemasukan" => 0,
        "saldo_akhir" => 0,
    ];

    foreach ($kas as $val) {
        switch ($val['jenis']) {
            case 'pengeluaran':
                $data["total_pengeluaran"] += $val["kredit"];
            case 'pemasukan':
                $data["total_pemasukan"] += $val["debit"];
        }
    };
    return $data;
}

function getLatestTypeTrx()
{
    $latest = [
        "type" => '',
        "value" => 0,
        "latest_saldo" => 0,
    ];

    global $pdo;

    $kas_masuk = getLatestKasMasuk();
    $kas_keluar = getLatestKasKeluar();


    if ($kas_masuk == false && $kas_keluar == false) {
        return $latest;
    }

    if (!$kas_masuk && $kas_keluar) {
        $latest['type'] = 'kredit';
        $latest['value'] = $kas_keluar['jml_kaskeluar'];

        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1;");
        $stmt->execute([$kas_keluar["id_kaskeluar"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];

        return $latest;
    } else {
        $latest['type'] = 'debit';
        $latest['value'] = $kas_masuk['jml_kasmasuk'];

        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1;");
        $stmt->execute([$kas_masuk["id_kasmasuk"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];

        return $latest;
    }



    if ($kas_masuk['tgl_kasmasuk'] >  $kas_keluar['tgl_kaskeluar']) {
        $latest['type'] = 'debit';
        $latest['value'] = $kas_masuk['jml_kasmasuk'];
    } else {
        $latest['type'] = 'kredit';
        $latest['value'] = $kas_keluar['jml_kaskeluar'];
    }

    if ($latest['type'] = 'debit') {
        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kasmasuk = ? LIMIT 1;");
        $stmt->execute([$kas_masuk["id_kasmasuk"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM kas WHERE id_kaskeluar = ? LIMIT 1;");
        $stmt->execute([$kas_keluar["id_kaskeluar"]]);
        $kas = $stmt->fetch();

        if (empty($kas)) {
            return false;
        }

        $latest['latest_saldo'] = (int)$kas['saldo_kas'];
    }

    return $latest;
}

function getLatestKasMasuk()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas_masuk ORDER BY tgl_kasmasuk DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function getLatestKasKeluar()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kas_keluar ORDER BY tgl_kaskeluar DESC LIMIT 1;");
    $stmt->execute();
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}
