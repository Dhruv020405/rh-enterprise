<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: /rh-enterprise/admin/login.php");
    exit();
}
