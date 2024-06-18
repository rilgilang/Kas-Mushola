<?php

include '../../bootstrap/db.php';
include '../../middleware/auth.php';
include '../../controller/donasi.controller.php';
include '../../controller/user.controller.php';
deleteUser($_GET['id']);
header("Location : user.php");
