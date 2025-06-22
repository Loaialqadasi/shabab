<?php
include 'db.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $sql = "SELECT password FROM users WHERE username = $1";
    $result = pg_query_params($conn, $sql, array($username));
    if ($result && $row = pg_fetch_assoc($result)) {
        $hashed = $row['password'];
        if (password_verify($password, $hashed)) {
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid login credentials!";
        }
    } else {
        $message = "Invalid login credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login | Shabab Lost & Found</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Login to Your Account</h2>
  <form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>