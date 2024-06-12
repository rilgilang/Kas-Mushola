<?php


function addPengeluaran($data)
{
    $latestTrx = getLatestTypeTrx();

    //insert transaksi_keluar
    global $pdo;

    $ids = generateAllIdForKas("pengeluaran");

    $query = "INSERT INTO detail_transaksi_keluar (id_transaksi_keluar ,jenis_transaksi_keluar, tgl_transaksi_keluar, jml_transaksi_keluar) VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['transaksi_keluar_id'], $data['jenis_transaksi_keluar'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas_keluar

    $query = "INSERT INTO kas_keluar (id_kaskeluar, tgl_kaskeluar, jml_kaskeluar, ket_kaskeluar, id_transaksi_keluar) VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kas_keluar_id'], $data['tgl_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['keterangan'], $ids['transaksi_keluar_id']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    //insert kas
    $query = "INSERT INTO kas (id_kas ,id_kaskeluar, saldo_kas) VALUES (?, ?, ?)";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ids['kas_id'], $ids['kas_keluar_id'], $latestTrx['latest_saldo'] - $data['jml_transaksi_keluar']]);
        return "success";
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }
}

function getAllPengeluaran($filter)
{
    global $pdo;

    $filter_query = "";

    if ($filter["start_date"] != "" && $filter["end_date"] != "") {
        $startDate = $filter["start_date"];
        $endDate = $filter["end_date"];

        $filter_query = "WHERE tgl_transaksi_keluar BETWEEN '$startDate' AND '$endDate' ";
    }

    $query = " SELECT 
            dtk.id_transaksi_keluar,
            dtk.tgl_transaksi_keluar,
            dtk.jenis_transaksi_keluar,
            dtk.jml_transaksi_keluar,
            COALESCE(kk.ket_kaskeluar, 'No Description') AS ket_kaskeluar
        FROM 
            detail_transaksi_keluar dtk
        LEFT JOIN 
            kas_keluar kk ON dtk.id_transaksi_keluar = kk.id_transaksi_keluar
        $filter_query ;";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kas = $stmt->fetchAll();

    return $kas;
}


function generatePengeluaranId($lastId)
{
    $result = "KP";
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

function getLatestPengeluaran()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM detail_transaksi_keluar ORDER BY created_at DESC LIMIT 1;");
    $stmt->execute();
    $donasi = $stmt->fetch();

    if (empty($donasi)) {
        return false;
    }

    return $donasi;
}

function getDetailedPengeluaranById($pengeluaran_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT k.id_kas, tk.id_transaksi_keluar, kk.id_kaskeluar ,k.saldo_kas, kk.ket_kaskeluar, kk.tgl_kaskeluar, kk.jml_kaskeluar, tk.tgl_transaksi_keluar, tk.jenis_transaksi_keluar, tk.jml_transaksi_keluar, tk.created_at FROM kas k JOIN kas_keluar kk ON k.id_kaskeluar = kk.id_kaskeluar JOIN detail_transaksi_keluar tk ON kk.id_transaksi_keluar = tk.id_transaksi_keluar WHERE tk.id_transaksi_keluar = ? LIMIT 1;");
    $stmt->execute([$pengeluaran_id]);
    $kas = $stmt->fetch();

    if (empty($kas)) {
        return false;
    }

    return $kas;
}

function updatePengeluaranById($pengeluaran_id, $data)
{
    global $pdo;

    $pengeluaran = getDetailedPengeluaranById($pengeluaran_id);
    $dif_value =  $data['jml_transaksi_keluar'] > $pengeluaran['jml_transaksi_keluar'] ? $data['jml_transaksi_keluar'] -  $pengeluaran['jml_transaksi_keluar'] : $pengeluaran['jml_transaksi_keluar'] - $data['jml_transaksi_keluar'];
    $math_op_query = $data['jml_transaksi_keluar'] > $pengeluaran['jml_transaksi_keluar'] ? "($dif_value + saldo_kas)" : "(saldo_kas - $dif_value)";

    //update pengeluaran
    $query = "UPDATE detail_transaksi_keluar
    SET jenis_transaksi_keluar = ?, jml_transaksi_keluar = ?, tgl_transaksi_keluar = ?
    WHERE id_transaksi_keluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['jenis_transaksi_keluar'], $data['jml_transaksi_keluar'], $data['tgl_transaksi_keluar'], $pengeluaran_id]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    syncKasMasuk($data, $pengeluaran['created_at']);

    syncSaldo($math_op_query, $pengeluaran['created_at']);
}

function deletePengeluaran($pengeluaran_id)
{
    $pengeluaran = getDetailedPengeluaranById($pengeluaran_id);
    $dif_value = $pengeluaran['jml_transaksi_keluar'];

    global $pdo;

    //delete pengeluaran
    $query = "DELETE FROM detail_transaksi_keluar
       WHERE id_transaksi_keluar = ?;";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pengeluaran['id_transaksi_keluar']]);
    } catch (PDOException $e) {
        //error
        return $e->getMessage();
    }

    deleteKasMasuk($pengeluaran['id_kaskeluar']);
    deleteKas($pengeluaran['id_kas']);

    syncSaldo("(saldo_kas + $dif_value)", $pengeluaran['created_at']);
}

//
// function generatePengeluaranId($lastId)
// {
//     $result = "DK";
//     $newNumber = intval(substr($lastId, 3)) + 1;
//     if (strlen($newNumber) <= 3) {
//         for ($x = 0; $x <= 3 - strlen($newNumber); $x++) {
//             $result = $result . "0";
//         }
//         $result = $result . $newNumber;
//         return $result;
//     } else {
//         $result = $newNumber;
//         return $result;
//     }
// }
