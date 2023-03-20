<!DOCTYPE html>
<html>
<head>
    <title>Expertise Logout</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<body>
<header>
    <h2> Expertise</h2>
</header>

<?php
ob_start();
session_start();
session_destroy();
echo "<div class=\"regularMsg\"> You are logged out. You will be redirected in 3 seconds</div>";
header("refresh: 3; login.php");
?>

</body>

</html>
