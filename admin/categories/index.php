<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

function displayCategories($parent_id = NULL, $level = 0) {
    global $conn;

    if ($parent_id === NULL) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $parent_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        // Category Name with indentation
        echo "<td>" . str_repeat("— ", $level) . htmlspecialchars($row['name']) . "</td>";

        // Image
        echo "<td>";
        if ($row['image']) {
            echo "<img src='../../uploads/categories/{$row['image']}' width='50'>";
        } else {
            echo "No Image";
        }
        echo "</td>";

        // Status
        echo "<td>";
        if ($row['status']) {
            echo "<span class='badge bg-success'>Active</span>";
        } else {
            echo "<span class='badge bg-secondary'>Inactive</span>";
        }
        echo "</td>";

        // Created Date
        echo "<td>" . $row['created_at'] . "</td>";

        // Actions
        echo "<td>
                <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                <a href='delete.php?id={$row['id']}'
                   class='btn btn-sm btn-danger'
                   onclick=\"return confirm('Are you sure?')\">
                   Delete
                </a>
              </td>";

        echo "</tr>";

        // Recursive call
        displayCategories($row['id'], $level + 1);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h3>Category Tree</h3>

    <a href="add.php" class="btn btn-danger mb-3">Add Category</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Image</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php displayCategories(); ?>
        </tbody>
    </table>

</div>

</body>
</html>