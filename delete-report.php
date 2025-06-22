<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['id']) && !empty($_POST['type'])) {
        $id = intval($_POST['id']);
        $type = trim($_POST['type']); // Trim to remove hidden characters or whitespace

        if ($type === 'lost') {
            $sql = "DELETE FROM lost_items WHERE id = $1";
        } elseif ($type === 'found') {
            $sql = "DELETE FROM found_items WHERE id = $1";
        } else {
            echo "Invalid type value: " . htmlspecialchars($type);
            exit;
        }

        $result = pg_query_params($conn, $sql, array($id));

        if ($result) {
            header("Location: search.php?deleted=1&type=$type&status=" . urlencode($_POST['status'] ?? ''));
            exit;
        } else {
            echo "Error deleting the record.";
        }

    } else {
        echo "Missing or empty form data.";
        echo "<br><pre>";
        print_r($_POST);
        echo "</pre>";
    }

} else {
    echo "Invalid request method.";
}
?>