<?php
require_once "../includes/auth.php";
require_once "../../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

/* ==============================
   GET MAIN IMAGE
============================== */
$stmt = $conn->prepare("SELECT main_image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

/* DELETE MAIN IMAGE FILE */
if (!empty($product['main_image'])) {
    $mainPath = "../../uploads/products/" . $product['main_image'];
    if (file_exists($mainPath)) {
        unlink($mainPath);
    }
}

/* ==============================
   DELETE GALLERY IMAGES (FILES)
============================== */
$stmtGallery = $conn->prepare("SELECT image FROM product_images WHERE product_id = ?");
$stmtGallery->bind_param("i", $id);
$stmtGallery->execute();
$galleryImages = $stmtGallery->get_result();

while ($img = $galleryImages->fetch_assoc()) {
    $galleryPath = "../../uploads/products/" . $img['main_image'];
    if (file_exists($galleryPath)) {
        unlink($galleryPath);
    }
}

/* ==============================
   DELETE PRODUCT (CASCADE WILL HANDLE OTHERS)
============================== */
$stmtDelete = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmtDelete->bind_param("i", $id);
$stmtDelete->execute();

header("Location: index.php");
exit();
