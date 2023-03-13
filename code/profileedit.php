<!DOCTYPE html>
<html lang="en">
<head>
    <title>Expertise</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<?php
ob_start();
session_start();

include("connectdb.php");
include("functions.php");
$userdata = check_login($conn);
$uid=$userdata["uid"];
$username = $userdata["username"];
?>

<body>
<?php include("navigation.php");?>

<section>
    <div class="largebox">
        <?php
        if(isset($_GET["uid"])){
            if ($_GET["uid"] == $uid) {// make sure that the current user is only allowed to edit his/her own profile
                $email = $userdata["email"];
                $upassword = $userdata["upassword"];
                $city = $userdata["city"];
                $state = $userdata["state"];
                $country = $userdata["country"];
                $uprofile = $userdata["uprofile"];

                // update users tuple with updated information
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
                    if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["city"])
                        && isset($_POST["state"]) && isset($_POST["country"])){
                        $in_username = $_POST["username"];
                        $in_password= $_POST["password"];
                        $in_city= $_POST["city"];
                        $in_state= $_POST["state"];
                        $in_country= $_POST["country"];
                        $in_profile= $_POST["profile"];

                        // check if password is correct
                        if ($in_password == $upassword){
                            $query = "UPDATE Users SET username = ?, city = ?, state = ?, country = ?, uprofile = ?
                                      WHERE uid = ?";
                            if ($stmt = $conn->prepare($query)) {
                                $stmt->bind_param("sssssi", $in_username, $in_city, $in_state, $in_country, $in_profile, $uid);
                                $stmt->execute();
                                $stmt->close();
                                echo "<div class=\"regularMsg\">You have successfully updated your profile.</div><br>";
                                header("refresh: 3");
                            }
                        }
                        else{

                            echo "<div class=\"warningMsg\">The password you entered is incorrect</div><br>";
                            header("refresh: 3");
                        }
                    }
                }

                // show user information
                echo "<div style=\"font-size:40px; color: #666666;\"><b>Your Account Info:</b></div><br><br>";
                echo "<form method=\"POST\">";
                echo "<div class=\"label\">Email: $email</div> <br> <br>";
                echo "<div class=\"label\">Username(*):</div>";
                echo "<input class=\"textbox\" type = \"text\" name=\"username\" value=$username required><br><br>";
                echo "<div class=\"label\">City(*):</div>";
                echo "<input class=\"textbox\" type=\"text\" name=\"city\" value=$city required><br><br>";
                echo "<div class=\"label\">State(two-letter abbreviation)(*):</div>";
                echo "<input class=\"textbox\" type=\"text\" name=\"state\" value=$state required><br><br>";
                echo "<div class=\"label\">Country(*):</div>";
                echo "<input class=\"textbox\" type=\"text\" name=\"country\" value=$country required><br><br>";
                echo "<div class=\"label\">Brief description about yourself:</div>";
                echo "<textarea class=\"multilinetextbox\" name=\"profile\">$uprofile</textarea><br><br>";
                echo "<div class=\"label\">Please Enter Your Current Password(*):</div>";
                echo "<input class=\"textbox\" type=\"text\" name=\"password\" required><br><br>";
                echo "<div class=\"label\">* required filed</div> <br> <br>";
                echo "<input class=\"button\" type=\"submit\" name=\"submit\" value=\"Update\"><br><br>";
                echo "</form>";
            }
            else{
                echo "<div style=\"font-size:30px; color: #666666;\">You are not allowed to access this page</div> <br> <br>";
            }
            $conn->close();
        }
        ?>
    </div>
</section>
</body>

</html>
