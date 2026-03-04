<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM brand_clients WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

header("Location:index.php");
exit();