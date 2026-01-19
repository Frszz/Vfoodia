<?php
require "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$allowedRoles = ['ADMIN'];
if (!in_array($_SESSION['role'], $allowedRoles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    $deleteStmt = $con->prepare("DELETE FROM tbl_route_cache");
    $deleteStmt->execute();
    
    $deletedRows = $deleteStmt->affected_rows;
    
    echo json_encode([
        'success' => true, 
        'message' => "Cache berhasil dihapus! ($deletedRows rute dihapus)",
        'deleted_count' => $deletedRows
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Gagal menghapus cache: ' . $e->getMessage()
    ]);
}
?>
