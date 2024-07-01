<?php

// reference the Dompdf namespace
use Dompdf\Dompdf;

function generatePdf($type, $filter)
{
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    $html = "";

    switch ($type) {
        case 'kas_keluar':
            $html = kasKeluarTemplate($filter);
            break;
        case 'kas_masuk':
            $html = kasMasukTemplate($filter);
            break;
        case 'kas':
            $html = kasTemplate($filter);
            break;
        default:
            # code...
            break;
    }
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();
}

function kasMasukTemplate($filter)
{
    $data = getAllKasMasuk($filter);
    $total = sumAllKasMasuk();
    $total = 0;
    $value = "";

    if ($data == false) {
        $value = "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tanggal = $val['tgl_kasmasuk'];
            $keterangan = $val['ket_kasmasuk'];
            $jumlah = $val['jml_kasmasuk'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $new_date = date_format(date_create($tanggal), "d-m-Y");
            $value .= "<td>$new_date</td>";
            $value .= "<td>$keterangan</td>";
            $value .= "<td>Rp. " . number_format($jumlah, 0, ',', '.') . "</td>";
            $value .= "</tr>";

            $total = $total + $jumlah;
        }

        $value .= "<tr class=" . "border border-white" . ">";
        $value .= '<td colspan="3">Total</td>';
        $value .= "<td>Rp." . number_format($total, 0, ',', '.') . "</td>";
        $value .= "</tr>";
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Kas</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .table-container {
                width: 100%;
                margin: 20px 0;
            }
            .table-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
                text-align: center;
            }
            .table-description {
                font-size: 14px;
                margin-bottom: 20px;
                text-align: center;
                color: #555;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
                color: #333;
                text-align: center;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .center {
                text-align: center;
                margin: auto;
                width: 50%;
                padding: 10px;
            }
        </style>
    </head>
    
    <body>
        <div class="table-container">
        <div class="table-title">Laporan Kas Masuk</div>
        <p class="center">Periode ' .  date_format(date_create($filter["start_date"]), "d-m-Y") . ' s/d ' . date_format(date_create($filter["end_date"]), "d-m-Y") . ' </p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $value . '
                </tbody>
            </table>
        </div>
    </body>
    </html>';

    return $html;
}

function kasKeluarTemplate($filter)
{
    $data = getAllKasKeluar($filter);
    $total = 0;

    $value = "";

    if ($data == false) {
        $value = "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tanggal = $val['tgl_kaskeluar'];
            $keterangan = $val['ket_kaskeluar'];
            $jumlah = $val['jml_kaskeluar'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $new_date = date_format(date_create($tanggal), "d-m-Y");
            $value .= "<td>$new_date</td>";
            $value .= "<td>$keterangan</td>";
            $value .= "<td>Rp. " . number_format($jumlah, 0, ',', '.') . "</td>";
            $value .= "</tr>";
            $total = $total + $jumlah;
        }

        $value .= "<tr class=" . "border border-white" . ">";
        $value .= '<td colspan="3">Total</td>';
        $value .= "<td>Rp." . number_format($total, 0, ',', '.') . "</td>";
        $value .= "</tr>";
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Kas</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .table-container {
                width: 100%;
                margin: 20px 0;
            }
            .table-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
                text-align: center;
            }
            .table-description {
                font-size: 14px;
                margin-bottom: 20px;
                text-align: center;
                color: #555;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
                color: #333;
                text-align: center;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .center {
                text-align: center;
                margin: auto;
                width: 50%;
                padding: 10px;
            }
        </style>
    </head>
    
    <body>
        <div class="table-container">
        <div class="table-title">Laporan Kas Keluar</div>
        <p class="center">Periode ' . date_format(date_create($filter["start_date"]), "d-m-Y")  . ' s/d ' . date_format(date_create($filter["end_date"]), "d-m-Y") . ' </p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $value . '
                </tbody>
            </table>
        </div>
    </body>
    </html>';

    return $html;
}


function kasTemplate($filter)
{
    $latest = getLatestSaldo();
    $data = getAllKas($filter);
    $total = [
        "total_kasmasuk" => 0,
        "total_kaskeluar" => 0,
        "total_saldo" => 0,
    ];

    $value = "";

    if ($data == false) {
        $value = "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tanggal = $val['tgl_kas'];
            $keterangan = $val['ket_kaskeluar'] == "No Description" ? $val['ket_kasmasuk'] : $val['ket_kaskeluar'];
            $debit = $val['jml_donasi'] == 0 ? $val['jml_infaq'] : $val['jml_donasi'];
            $kredit = $val['jml_transaksi_keluar'];
            $saldo = $val['saldo_kas'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $new_date = date_format(date_create($tanggal), "d-m-Y");
            $value .= "<td>$new_date</td>";
            $value .= "<td>$keterangan</td>";
            $value .= "<td>Rp. " . number_format($debit, 0, ',', '.') . "</td>";
            $value .= "<td>Rp. " . number_format($kredit, 0, ',', '.') . "</td>";
            $value .= "<td>Rp. " . number_format($saldo, 0, ',', '.') . "</td>";
            $value .= "</tr>";

            $total['total_kasmasuk'] = $total['total_kasmasuk'] + $debit;
            $total['total_kaskeluar'] = $total['total_kaskeluar'] + $kredit;
            // $total['total_saldo'] = $total['total_saldo'] + $saldo;
        }

        $value .= "<tr class=" . "border border-white" . ">";
        $value .= '<td colspan="3">Total</td>';
        $value .= "<td>Rp." . number_format($total['total_kasmasuk'], 0, ',', '.') . "</td>";
        $value .= "<td>Rp." . number_format($total['total_kaskeluar'], 0, ',', '.') . "</td>";
        $value .= "<td>Rp." . number_format($latest['saldo_kas'], 0, ',', '.') . "</td>";

        $value .= "</tr>";
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Kas</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .table-container {
                width: 100%;
                margin: 20px 0;
            }
            .table-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
                text-align: center;
            }
            .table-description {
                font-size: 14px;
                margin-bottom: 20px;
                text-align: center;
                color: #555;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
                color: #333;
                text-align: center;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .center {
                text-align: center;
                margin: auto;
                width: 50%;
                padding: 10px;
            }
        </style>
    </head>
    
    <body>
        <div class="table-container">
            <div class="table-title">Laporan Kas</div>
            <p class="center">Periode ' . date_format(date_create($filter["start_date"]), "d-m-Y")  . ' s/d ' . date_format(date_create($filter["end_date"]), "d-m-Y") . ' </p>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $value . '
                </tbody>
            </table>
        </div>
    </body>
    </html>';

    return $html;
}
