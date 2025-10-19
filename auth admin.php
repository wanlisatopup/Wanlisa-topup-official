<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}
?>
