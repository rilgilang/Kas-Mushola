<?php
// somewhere early in your project's loading, require the Composer autoloader
// see: http://getcomposer.org/doc/00-intro.md
require '../../vendor/autoload.php';
include "../../bootstrap/db.php";
include "../../controller/pdf.controller.php";
include "../../controller/infaq.controller.php";
include "../../controller/pengeluaran.controller.php";
include "../../controller/donasi.controller.php";
include "../../controller/kas.controller.php";

$type = $_GET['type'];

//data
$filter = ["start_date" => "", "end_date" => ""];


generatePdf($type);

header("Location : $type.php");
