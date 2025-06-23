<?php
session_start();
include 'db.php';

if (!isset($_POST['item_id'], $_POST['type'], $_POST['status'])) {
    echo "Missing data.";
    exit;
}

$itemId = intval($_POST['item_id']);
$type = $_POST['type'] === 'found' ? 'found_items' : 'lost_items';
$status = $_POST['status'];

if (!in_array($status, ['Open', 'Resolved'])) {
    echo "Invalid status.";
    exit;
}

// Authorization: Only the owner can update
$sql = "SELECT reported_by FROM $type WHERE id = $1";
$result = pg_query_params($conn, $sql, array($itemId));
$row = pg_fetch_assoc($result);

if (!$row || $row['reported_by'] !== $_SESSION['username']) {
    echo "Unauthorized update.";
    exit;
}

$sql = "UPDATE $type SET status = $1 WHERE id = $2";
$updateResult = pg_query_params($conn, $sql, array($status, $itemId));

if (!$updateResult) {
    echo "Update failed: " . pg_last_error($conn);
    exit;
}

header("Location: index.php");
exit;
?>
