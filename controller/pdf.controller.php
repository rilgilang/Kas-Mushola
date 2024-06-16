<?php

// reference the Dompdf namespace
use Dompdf\Dompdf;

function generatePdf($type)
{
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    $html = "";

    switch ($type) {
        case 'infaq':
            $html = infaqTemplate();
            break;
        case 'donasi':
            $html = donasiTemplate();
            break;
        case 'pengeluaran':
            $html = pengeluaranTemplate();
            break;
        case 'kas':
            $html = kasTemplate();
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

function infaqTemplate()
{
    $filter = ["start_date" => "", "end_date" => ""];
    $data = getAllInfaq($filter);

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
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tgl_infaq = $val['tgl_infaq'];
            $jenis_infaq = $val['jenis_infaq'];
            $ket_kasmasuk = $val['ket_kasmasuk'];
            $jml_infaq = $val['jml_infaq'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $value .= "<td>$tgl_infaq</td>";
            $value .= "<td>$jenis_infaq</td>";
            $value .= "<td>$ket_kasmasuk</td>";
            $value .= "<td>$jml_infaq</td>";
            $value .= "</tr>";
        }
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Infaq Report</title>
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
        </style>
    </head>
    
    <body>
        <div class="table-container">
            <div class="table-title">Laporan Infaq</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Infaq</th>
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


function donasiTemplate()
{
    $filter = ["start_date" => "", "end_date" => ""];
    $data = getAllDonasi($filter);

    $value = "";

    if ($data == false) {
        $value = "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tgl_donasi = $val['tgl_donasi'];
            $nama_donatur = $val['nama_donatur'];
            $ket_kasmasuk = $val['ket_kasmasuk'];
            $jml_donasi = $val['jml_donasi'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $value .= "<td>$tgl_donasi</td>";
            $value .= "<td>$nama_donatur</td>";
            $value .= "<td>$ket_kasmasuk</td>";
            $value .= "<td>$jml_donasi</td>";
            $value .= "</tr>";
        }
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Infaq Report</title>
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
        </style>
    </head>
    
    <body>
        <div class="table-container">
            <div class="table-title">Laporan Donasi</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Donatur</th>
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


function pengeluaranTemplate()
{
    $filter = ["start_date" => "", "end_date" => ""];
    $data = getAllPengeluaran($filter);

    $value = "";

    if ($data == false) {
        $value = "
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>";
    } else {
        foreach ($data as $key => $val) {
            $number =  $key + 1;
            $tgl_transaksi_keluar = $val['tgl_transaksi_keluar'];
            $jenis_transaksi_keluar = $val['jenis_transaksi_keluar'];
            $ket_kaskeluar = $val['ket_kaskeluar'];
            $jml_transaksi_keluar = $val['jml_transaksi_keluar'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $value .= "<td>$tgl_transaksi_keluar</td>";
            $value .= "<td>$jenis_transaksi_keluar</td>";
            $value .= "<td>$ket_kaskeluar</td>";
            $value .= "<td>$jml_transaksi_keluar</td>";
            $value .= "</tr>";
        }
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Infaq Report</title>
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
        </style>
    </head>
    
    <body>
        <div class="table-container">
            <div class="table-title">Laporan Pengeluaran</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Pengeluaran</th>
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


function kasTemplate()
{
    $filter = ["start_date" => "", "end_date" => ""];
    $data = getAllKas($filter);

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
            $tanggal = $val['transaction_type'] == "Kredit" ? $val['jml_donasi'] == 0 ? $val['tgl_infaq'] : $val['tgl_donasi'] : $val['tgl_kaskeluar'];
            $keterangan = $val['ket_kaskeluar'] == "No Description" ? $val['ket_kasmasuk'] : $val['ket_kaskeluar'];
            $debit = $val['jml_donasi'] == 0 ? $val['jml_infaq'] : $val['jml_donasi'];
            $kredit = $val['jml_transaksi_keluar'];
            $saldo = $val['saldo_kas'];

            $value .= "<tr>";
            $value .= "<td>$number</td>";
            $value .= "<td>$tanggal</td>";
            $value .= "<td>$keterangan</td>";
            $value .= "<td>$debit</td>";
            $value .= "<td>$kredit</td>";
            $value .= "<td>$saldo</td>";
            $value .= "</tr>";
        }
    }

    $html = '<!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Infaq Report</title>
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
        </style>
    </head>
    
    <body>
        <div class="table-container">
            <div class="table-title">Laporan Kas</div>
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
