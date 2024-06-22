<?php

include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/infaq.controller.php';
include '../../controller/kas.controller.php';
deleteInfaq($_GET['id']);
