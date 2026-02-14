<?php
require_once 'includes/initialisation.php';
session_start();
session_destroy();
header("Location: index.php");
exit();
?>
