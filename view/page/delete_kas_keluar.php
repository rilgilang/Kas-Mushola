<?php

include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/kas.keluar.controller.php';
deleteKasKeluar($_GET['id']);
