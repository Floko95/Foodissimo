<?php require_once("server.php"); ?>

<?php
session_start();
session_destroy();
header('location: home.php');
exit;
?>
