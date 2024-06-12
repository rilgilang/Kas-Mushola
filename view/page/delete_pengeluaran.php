<?php

include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/pengeluaran.controller.php';
include '../../controller/kas.controller.php';
deletePengeluaran($_GET['id']);
header("Location : pengeluaran.php");
