<?php

include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/donasi.controller.php';
include '../../controller/kas.controller.php';
deleteDonasi($_GET['id']);
header("Location : donasi.php");
