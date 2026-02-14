<?php
require_once '../includes/initialisation.php';
session_destroy();
header("Location: connexion.php");
exit();
?>
