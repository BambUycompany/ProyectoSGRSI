<?php
session_start();
session_unset();
session_destroy();
header("Location: ../public/login.php");
exit;
require_once __DIR__ . "/../config/config.php";

?>