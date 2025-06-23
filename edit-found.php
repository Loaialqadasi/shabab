<?php 
session_start(); 
include 'db.php'; 

// Validate ID from URL
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

// Fetch the found item from the database 
$sql = "SELECT * FROM found_items WHERE id = $1"; 
$result = pg_query_params($conn, $sql, array($id)); 
$item = pg_fetch_assoc($result); 

// Check if the item exists and was submitted by the current user 
if (!$item || $item['username'] !== $_SESSION['username']) { 
    echo "Unauthorized access."; 
    exit; 
} 

// If form submitted, update the status 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $new_status = $_POST['status'] ?? null;

    if (!$new_status) {
        echo "Status is required.";
        exit;
    }

    $sql = "UPDATE found_items SET status = $1 WHERE id = $2"; 
    $updateResult = pg_query_params($conn, $sql, array($new_status, $id)); 
    
    if (!$updateResult) { 
        echo "Update failed: " . pg_last_error($conn); 
        exit; 
    }

    header("Location: index.php"); 
    exit; 
} 
?>
