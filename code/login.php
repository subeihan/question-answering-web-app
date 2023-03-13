<?php
ob_start();
session_start();

include("connectdb.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Expertise Login</title>
    <meta charset = "UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<body>
<header>
    <h2> Welcome to Expertise</h2>
</header>

<?php
if(isset($_SESSION["uid"])){
    echo "<div class=\"regularMsg\">You are already logged in.</div>";
    echo "<div class=\"regularMsg\">You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.</div>";
    header("refresh: 3; index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
    if(isset($_POST["email"]) && isset($_POST["password"])){
        $in_email= $_POST["email"];
        $in_password= $_POST["password"];

        //check if the combination of the email and password exist.
        $query = "SELECT uid, username, upassword FROM Users WHERE email = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $in_email);
            $stmt->execute();
            $stmt->bind_result($uid,$username, $password);

            // insert data if email has not been used
            if ($stmt->fetch()) {//if email exists
                if($in_password == $password){//if password is correct
                    $_SESSION["uid"] = $uid;
                    $_SESSION["username"] = $username;
                    echo "<div class=\"regularMsg\">Login successful.</div>";
                    echo "<div class=\"regularMsg\">You will be redirected in 5 seconds or click 
                                <a href=\"index.php\">here</a>.</div>";
                    header("refresh: 5; index.php");
                }
                else{//give a message and refresh if the password is incorrect
                    echo "<div class=\"warningMsg\">We can't find that username and password.</div>";
                    header("refresh: 3");
                }

            }
            else {//give a message and refresh if the email does not exist
                echo "<div class=\"warningMsg\">We can't find that username and password.</div>";
                header("refresh: 3");

            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<section>
<div class = "smallbox">
    <form method="POST">
        <div style="font-size:40px; color: #666666;">Login</div> <br> <br>
        <div class="label">Email:</div>
        <input class="textbox" type="text" name="email" required><br><br>
        <div class="label">Password:</div>
        <input class="textbox" type="text" name="password" required><br><br>

        <input class="button" type="submit" name='submit' value="Login"/><br><br>

        <a href="signup.php" style = "font-size:22px; color:#666666;">Click to Signup</a>
    </form>
</div>
</section>

</body>
</html>