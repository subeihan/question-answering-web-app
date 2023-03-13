<?php
ob_start();
session_start();

include("connectdb.php");
include("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Expertise Sigup</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<body>
<header>
    <h2> Welcome to Expertise</h2>
</header>

<?php if(isset($_SESSION['uid'])){
    echo "<div class=\"regularMsg\">You are already logged in.</div>";
    echo "<div class=\"regularMsg\">You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.</div>";
    header("refresh: 3; index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
    if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])
        && isset($_POST["city"]) && isset($_POST["state"]) && isset($_POST["country"])) {
        $in_username = $_POST["username"];
        $in_password= $_POST["password"];
        $in_email= $_POST["email"];
        $in_city= $_POST["city"];
        $in_state= $_POST["state"];
        $in_country= $_POST["country"];
        $in_profile= $_POST["profile"];

        //check if email already exists in database
        $query = "SELECT email FROM Users WHERE email = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $in_email);
            $stmt->execute();
            $stmt->bind_result($email);

            //give a message and refresh if email has been used
            if ($stmt->fetch()) {
                $stmt->close();
                $conn->close();
                echo "<div class=\"warningMsg\">This email has already been used.</div>";
                header("refresh: 3");
            }
            else{//insert data if email has not been used
                $stmt->close();
                $query = "INSERT INTO Users(username, email, upassword, city, state, country, uprofile) 
                                values (?, ?, ?, ?, ?, ?, ?)";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("sssssss", $in_username, $in_email, $in_password, $in_city,
                        $in_state, $in_country, $in_profile);
                    $stmt->execute();
                    $stmt->close();
                    $conn->close();
                    echo "<div class=\"regularMsg\">You have successfully signed up your account.
                                You will be redirected to the login page in 5 seconds.</div><br>";
                    header("refresh: 5; login.php");
                }
            }
        }
    }
}
?>

<section>
<div class="smallbox">
    <form method="POST">
        <div style="font-size:40px; color: #666666;">Signup</div> <br> <br>
        <div class="label">Email Address(*):</div>
        <input class= "textbox" type= "text" name = "email" required><br><br>
        <div class="label">Username(*):</div>
        <input class="textbox" type = "text" name="username" required><br><br>
        <div class="label">Password(*):</div>
        <input class="textbox" type="text" name="password" required><br><br>
        <div class="label">City(*):</div>
        <input class="textbox" type="text" name="city" required><br><br>
        <div class="label">State(two-letter abbreviation)(*):</div>
        <input class="textbox" type="text" name="state" required><br><br>
        <div class="label">Country(*):</div>
        <input class="textbox" type="text" name="country" required><br><br>
        <div class="label">Enter a brief description about yourself:</div>
        <textarea class="multilinetextbox" name="profile"></textarea><br>
        <div class="label">* required filed</div> <br>
        <input class="button" type="submit" name="submit" value="Signup"><br><br>
        <a href="login.php" style = "font-size:22px; color:#666666;">Click to Login</a>
    </form>

</div>
</section>

</body>
</html>