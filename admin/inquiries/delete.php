<?php

require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM inquiries WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

header("Location:index.php");
exit();