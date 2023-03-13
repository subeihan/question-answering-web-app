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
    <div class="sec-container-dark">
        <?php

        // show a list of topics of a particular category
        if (isset($_GET["cid"]) && isset($_GET["cname"])){
            $in_cid = $_GET["cid"];
            $in_cname = $_GET["cname"];
            echo "<div style=\"text-align:left; font-size:30px; color: white;\">$in_cname</div> <br> <br>";
            $query = "SELECT tid, tname FROM Topic WHERE cid = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $in_cid);
                $stmt->execute();
                $stmt->bind_result($tid, $tname);


                while ($stmt->fetch()) {
                    echo "<div class=\"sec-item\">
                                <button class=\"sec-button-light\">
                                    <a href=\"getquestion.php?tid=$tid&tname=$tname\">$tname</a>
                                </button>
                           </div>";
                }
                $stmt->close();;
            }
        }
        else {
            echo "cid or cname is not set\n";
        }
        $conn->close();
        ?>
    </div>
</section>


</body>
</html>
