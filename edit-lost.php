<?php 
session_start(); 
include 'db.php'; 

$id = $_GET['id'] ?? null; 
if (!is_numeric($id)) { 
    echo "Invalid ID."; 
    exit; 
} 

// Ensure user is logged in 
if (!isset($_SESSION['username'])) { 
    header('Location: login.php'); 
    exit; 
} 

// Fetch the lost item from the database 
$sql = "SELECT * FROM lost_items WHERE id = $1"; 
$result = pg_query_params($conn, $sql, array($id)); 
$item = pg_fetch_assoc($result); 

// Check if the item exists and belongs to the user 
if (!$item || $item['reported_by'] !== $_SESSION['username']) { 
    echo "Unauthorized access."; 
    exit; 
} 

// Handle update/delete request 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $new_status = $_POST['status'] ?? null;

    // Update item status
    $sql = "UPDATE lost_items SET status = $1 WHERE id = $2"; 
    $updateResult = pg_query_params($conn, $sql, array($new_status, $id)); 
    if (!$updateResult) {
        echo "Update failed: " . pg_last_error($conn);
        exit;
    }

    // Optional delete
    if (isset($_POST['delete']) && $_POST['delete'] === '1') { 
        $sql = "DELETE FROM lost_items WHERE id = $1 AND reported_by = $2"; 
        $deleteResult = pg_query_params($conn, $sql, array($id, $_SESSION['username'])); 
        if (!$deleteResult) {
            echo "Delete failed: " . pg_last_error($conn);
            exit;
        }
        header("Location: search.php?deleted=1&type=lost"); 
        exit; 
    } 

    header("Location: index.php"); 
    exit; 
} 
?>
