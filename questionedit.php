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
    <div class="sec-container-light">
        <?php

        // show prefilled question title and question body
        if(isset($_GET["qid"]) && isset($_GET["quid"])){
            if ($_GET["quid"] == $uid) {// make sure that the current user is only allowed to edit his/her own question
                $in_qid = $_GET["qid"];
                $in_quid = $_GET["quid"];

                // update tuple of the question with updated question title or question body
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
                    if(isset($_POST["qtitle"]) && isset($_POST["qbody"])){
                        $in_qtitle= $_POST["qtitle"];
                        $in_qbody= $_POST["qbody"];

                        $query = "UPDATE Question SET qtitle = ?, qbody = ? WHERE qid = ?";
                        if ($stmt = $conn->prepare($query)) {
                            $stmt->bind_param("ssi", $in_qtitle, $in_qbody, $in_qid);
                            $stmt->execute();
                            $stmt->close();
                            echo "<div class=\"regularMsg\">You have successfully updated your question.</div><br>";
                            header("refresh: 3; questiondetail.php?qid=$in_qid");
                        }
                    }
                }

                // show title and body of the question;
                $query = "SELECT qtitle, qbody
                          FROM Question
                          WHERE qid = ? AND quid = ?";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("ii", $in_qid, $in_quid);
                    $stmt->execute();
                    $question = $stmt->get_result();
                    $stmt->close();

                    if ($row = $question->fetch_assoc()){
                        $qtitle = $row["qtitle"];
                        $qbody = $row["qbody"];

                        echo "<form method=\"POST\">";
                        echo "<div style=\"font-size:30px; color: #666666;\"><b>Edit Your Question:</b></div> <br> <br>";
                        echo "<div class=\"label\">Question Title(*):</div>";
                        echo "<input class= \"textbox\" type= \"text\" name = \"qtitle\" value = \"$qtitle\" required> <br> <br>";
                        echo "<div class=\"label\">Question Details(*):</div>";
                        echo "<textarea class=\"multilinetextbox\" name=\"qbody\" required>$qbody</textarea> <br> <br>";
                        echo "<div class=\"label\">* required filed</div> <br> <br>";
                        echo "<input class=\"button\" type=\"submit\" name=\"submit\" value=\"Submit\" /> <br> <br>";
                        echo "</form>";
                    }
                }

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
