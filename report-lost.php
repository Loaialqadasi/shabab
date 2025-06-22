<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db.php';
session_start();


if (!isset($_SESSION['username'])) {
    echo '<script>
            alert("Please log in or register before submitting a report.");
            window.location.href = "login.php";
          </script>';
    exit();
}


$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST["item_name"]);
    $location = trim($_POST["location"]);
    $date_lost = $_POST["date_lost"];
    $description = trim($_POST["description"]);
    
    $imageName = '';
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $imageName = basename($_FILES["image"]["name"]);
        $targetPath = "uploads/" . $imageName;
        // Ensure uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath);
    }

    $sql = "INSERT INTO lost_items (item_name, date_lost, location, description, image) VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($conn, $sql, array($item_name, $date_lost, $location, $description, $imageName));
    if ($result) {
        $message = "Lost item reported successfully!";
    } else {
        $message = "Failed to report item.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Report Lost Item</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Report a Lost Item</h2>
  <form method="POST" enctype="multipart/form-data" action="report-lost.php">
    <input type="text" name="item_name" placeholder="Item Name" required>
    <input type="text" name="location" placeholder="Location Lost" required>
    <input type="date" name="date_lost" required>
    <textarea name="description" placeholder="Describe the item..." required></textarea>
    <input type="file" name="image">
    <button type="submit">Submit Report</button>
    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>