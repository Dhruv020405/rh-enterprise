<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {

    foreach ($data as $item) {

        $stmt = $conn->prepare("UPDATE categories SET sort_order=? WHERE id=?");
        $stmt->bind_param("ii", $item['position'], $item['id']);
        $stmt->execute();
    }
}

echo "Category Order Updated";
