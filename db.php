<<<<<<< HEAD
<?php 
// InfinityFree MySQL credentials
$host = 'dpg-d1bqnqodl3ps73eupri0-a'; 
$username = 'lostandfound'; 
$password = 'bXH9PCnHXpCcnXqTXfpKt41cESxQzYFs'; // Replace with your real password
$database = 'shabab'; 
=======
<?php
$host = 'dpg-d1bqnqodl3ps73eupri0-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'shabab';
$user = 'lostandfound';
$password = 'bXH9PCnHXpCcnXqTXfpKt41cESxQzYFs';
>>>>>>> 8de350a (Anything)

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("System maintenance in progress. Please try again later.");
}
?>