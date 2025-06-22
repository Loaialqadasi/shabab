<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';
session_start();


if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Please log in or register before submitting a report.');
            window.location.href = 'login.php';
          </script>";
    exit(); // stop further execution so the page doesn’t load
}


$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST["item_name"]);
    $location = trim($_POST["location"]);
    $date_found = $_POST["date_found"];
    $description = trim($_POST["description"]);
    
  $image_name = '';
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image_name = basename($_FILES["image"]["name"]);
    $targetPath = "uploads/" . $image_name;
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath);
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$sql = "INSERT INTO found_items (item_name, date_found, location, description, image, username) VALUES ($1, $2, $3, $4, $5, $6)";
$result = pg_query_params($conn, $sql, array($item_name, $date_found, $location, $description, $image_name, $username));


if ($result) {
        $message = "Found item reported successfully!";
    } else {
        $message = "Failed to report item.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Report Found Item</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Report a Found Item</h2>
  <form method="POST" enctype="multipart/form-data" action="report-found.php">
    <input type="text" name="item_name" placeholder="Item Name" required>
    <input type="text" name="location" placeholder="Location Found" required>
    <input type="date" name="date_found" required>
    <textarea name="description" placeholder="Describe the item..." required></textarea>
    <input type="file" name="image">
    <button type="submit">Submit Report</button>
    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>