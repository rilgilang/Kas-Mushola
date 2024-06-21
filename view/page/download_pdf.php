<?php
// somewhere early in your project's loading, require the Composer autoloader
// see: http://getcomposer.org/doc/00-intro.md
require '../../vendor/autoload.php';
include "../../bootstrap/db.php";
include "../../controller/pdf.controller.php";
include "../../controller/kas.keluar.controller.php";
include "../../controller/kas.masuk.controller.php";
include "../../controller/kas.controller.php";

$type = $_GET['type'];

//data
$filter = [
    'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
    'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
];


generatePdf($type, $filter);

header("Location : $type.php");
