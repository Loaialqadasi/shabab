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
    $new_status = $_POST['status'];
    $sql = "UPDATE found_items SET status = $1 WHERE id = $2";
    pg_query_params($conn, $sql, array($new_status, $id));
    header("Location: index.php"); // redirect to homepage after update
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Found Item</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Edit Found Report Status</h2>
  <form method="POST">
    <label for="status">Status:</label>
    <select name="status">
      <option value="open" <?= $item['status'] == 'open' ? 'selected' : '' ?>>Open</option>
      <option value="resolved" <?= $item['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>
    <button type="submit">Update</button>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>