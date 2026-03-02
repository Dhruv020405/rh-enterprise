<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit();