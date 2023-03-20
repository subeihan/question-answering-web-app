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
        if(isset($_GET["aid"]) && isset($_GET["auid"])){
            if ($_GET["auid"] == $uid) {// make sure that the current user is only allowed to edit his/her own answer
                $in_aid = $_GET["aid"];
                $in_auid = $_GET["auid"];

                // show question details and body of the answer;
                $query = "SELECT qid, qtitle, qbody, qdate, qtime, username, abody
                          FROM Question NATURAL JOIN Answer, Users
                          WHERE aid = ? AND auid = ? AND quid = uid";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("ii", $in_aid, $in_auid);
                    $stmt->execute();
                    $answer = $stmt->get_result();
                    $stmt->close();

                    if ($row = $answer->fetch_assoc()){
                        $qid= $row["qid"];
                        $qtitle= $row["qtitle"];
                        $qbody = $row["qbody"];
                        $qdate = $row["qdate"];
                        $qtime = $row["qtime"];
                        $qusername = $row["username"];
                        $abody = $row["abody"];

                        // show question details so that the current user is able to see the question while
                        // editing the answer
                        echo "<div class=\"questionbox\">";
                        echo "<a href=\"questiondetail.php?qid=$qid\">$qtitle</a> <br>";
                        echo "<div class=\"questiondatabox\">";
                        echo "<p>Posted $qdate at $qtime by $qusername</p>";
                        echo "</div>";
                        echo "<h4>Question Detail:</h4>";
                        echo "<p>$qbody</p> <br>";
                        echo "</div><br><br>";

                        // update tuple of the answer with updated answer body
                        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
                            if(isset($_POST["abody"])){
                                $in_abody= $_POST["abody"];

                                $query = "UPDATE Answer SET abody = ? WHERE aid = ?";
                                if ($stmt = $conn->prepare($query)) {
                                    $stmt->bind_param("si", $in_abody, $in_aid);
                                    $stmt->execute();
                                    $stmt->close();
                                    echo "<div class=\"regularMsg\">You have successfully updated your answer.</div><br>";
                                    header("refresh: 3; questiondetail.php?qid=$qid");
                                }
                            }
                        }

                        // show body of the answer
                        echo "<form method=\"POST\">";
                        echo "<div style=\"font-size:30px; color: #666666;\"><b>Edit Your Answer:</b></div><br><br>";
                        echo "<div class=\"label\">Answer Details(*):</div><br>";
                        echo "<textarea class=\"multilinetextbox\" name=\"abody\" required>$abody</textarea>";
                        echo "<div class=\"label\">* required filed</div><br><br>";
                        echo "<input class=\"button\" type=\"submit\" name=\"submit\" value=\"Submit\"/><br><br>";
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
