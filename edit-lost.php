<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Get the item ID from the URL
$id = $_GET['id'];

// Fetch the lost item from the database
$sql = "SELECT * FROM lost_items WHERE id = $1";
$result = pg_query_params($conn, $sql, array($id));
$item = pg_fetch_assoc($result);

// Check if the item exists and was submitted by the current user
if (!$item || $item['username'] !== $_SESSION['username']) {
    echo "Unauthorized access.";
    exit;
}

// If form submitted, update the status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['status'];
    $sql = "UPDATE lost_items SET status = $1 WHERE id = $2";
    pg_query_params($conn, $sql, array($new_status, $id));
    // Optionally, handle delete request
    if (isset($_POST['delete']) && $_POST['delete'] === '1') {
        $sql = "DELETE FROM lost_items WHERE id = $1 AND username = $2";
        pg_query_params($conn, $sql, array($id, $_SESSION['username']));
        header("Location: search.php?deleted=1&type=lost");
        exit;
    }
    header("Location: index.php"); // redirect to homepage after update
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Lost Item</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Edit Lost Report Status</h2>
  <form method="POST">
    <label for="status">Status:</label>
    <select name="status">
      <option value="Open" <?= $item['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
      <option value="Resolved" <?= $item['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>
    <button type="submit">Update</button>
    <button type="submit" name="delete" value="1" style="background:#e53935;color:#fff;margin-left:10px;">Delete Report</button>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
