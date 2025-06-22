<?php
include 'db.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check for existing user
    $sql = "SELECT id FROM users WHERE username=$1 OR email=$2";
    $result = pg_query_params($conn, $sql, array($username, $email));
    if (pg_num_rows($result) > 0) {
        $message = "Username or email already exists!";
    } else {
        $sql = "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)";
        $result = pg_query_params($conn, $sql, array($username, $email, $password));
        if ($result) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $message = "Error during registration!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register | Shabab Lost & Found</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
  <h2>Create an Account</h2>
  <form method="POST" action="register.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>