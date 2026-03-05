<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

// Get JSON POST payload from SortableJS
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    // Loop through the array of mapped positions and update database
    foreach ($data as $item) {
        $stmt = $conn->prepare("UPDATE products SET sort_order = ? WHERE id = ?");
        $stmt->bind_param("ii", $item['position'], $item['id']);
        $stmt->execute();
    }
    
    echo "Product Order Updated Successfully";
} else {
    echo "No data received";
}
?>