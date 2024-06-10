<?php
include '../../middleware/auth.php';

logout();
header("Location: login.php");
exit();
