<?php
// PostgreSQL credentials for Render.com
$host = 'dpg-d1bqnqodl3ps73eupri0-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'shabab';
$user = 'lostandfound';
$password = 'bXH9PCnHXpCcnXqTXfpKt41cESxQzYFs';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("System maintenance in progress. Please try again later.");
}
?>
