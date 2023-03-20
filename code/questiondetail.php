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
        if(isset($_GET["qid"])){
            // show details of the question
            $in_qid = $_GET["qid"];
            $query = "SELECT qid, qtitle, qbody, qdate, qtime, quid, isResolved, username
                      FROM Question, Users 
                      WHERE qid = ? AND quid = uid";
            if ($qstmt = $conn->prepare($query)) {
                $qstmt->bind_param("i", $in_qid);
                $qstmt->execute();
                $question = $qstmt->get_result();
                $qstmt->close();

                if($row = $question->fetch_assoc()) {
                    $qid = $row["qid"];
                    $qtitle = $row["qtitle"];
                    $qbody = $row["qbody"];
                    $qdate = $row["qdate"];
                    $qtime = $row["qtime"];
                    $quid = $row["quid"];
                    $isResolved = $row["isResolved"];
                    $qusername = $row["username"];

                    echo "<h1>$qtitle";
                    // show "Resolved" if the question has been marked as resolved
                    if ($isResolved){
                        echo "<span style =\"font-size:22px; color:darkblue\"> *Resolved</span>";
                    }
                    echo "</h1>";
                    echo "<div class=\"questiondatabox\">";
                    echo "<p>Posted $qdate at $qtime by $qusername</p>";
                    echo "</div>";
                    echo "<div class=\"questionbox\">";
                    echo "<h4>Question Detail:</h4>";
                    echo "<p>$qbody</p> <br>";


                    // show edit button if the question was posted by the current user
                    if ($quid == $uid){
                        echo "<form action=\"questionedit.php?qid=$qid&quid=$uid\" class=\"buttonform\" style=\"display:inline-block;\" method=\"POST\">";
                        echo "<input id=\"editbtn\" type=\"submit\" name=\"edit\" value=\"Edit\">";
                        echo "</form>";
                    }

                    // show resolved or unresolved button if the question was posted by the current user
                    if ($quid == $uid){
                        echo "<form class=\"buttonform\" style=\"display:inline-block; margin-left: 10px;\" method=\"POST\">";
                        // show resolved button if the question has not been marked as resolved, show unresolved button otherwise
                        if (!$isResolved){
                            echo "<input id=\"resolvedbtn\" type=\"submit\" name=\"resolved\" value=\"Resolved\">";
                        }
                        else{
                            echo "<input id=\"resolvedbtn\" type=\"submit\" name=\"unresolved\" value=\"Unresolved\">";
                        }
                        echo "</form>";

                        // update tuple for the question if the resolved button is pressed
                        if (isset($_POST["resolved"])){
                            $query = "UPDATE Question SET isResolved = 1 WHERE qid = ?";
                            if ($rstat = $conn->prepare($query)){
                                $rstat->bind_param("i", $qid);
                                $rstat->execute();
                                $rstat->close();
                                header("refresh: 2");
                            }
                        }

                        // update tuple for the question if the unresolved button is pressed
                        if (isset($_POST["unresolved"])){
                            $query = "UPDATE Question SET isResolved = 0 WHERE qid = ?";
                            if ($rstat = $conn->prepare($query)){
                                $rstat->bind_param("i", $qid);
                                $rstat->execute();
                                $rstat->close();
                                header("refresh: 2");
                            }
                        }
                    }

                    echo "</div><br>";

                    // insert tuple for the new answer if an answer is submitted
                    if (isset($_POST["answer"])) {
                        if (isset($_POST["abody"])) {
                            $in_abody = $_POST["abody"];

                            $query = "INSERT INTO Answer(qid, auid, abody) VALUES(?, ?, ?)";
                            if ($aistmt = $conn->prepare($query)) {
                                $aistmt->bind_param("iis", $qid, $uid, $in_abody);
                                $aistmt->execute();
                                $aistmt->close();
                                echo "<div class=\"regularMsgMsg\">You have successfully submitted your answer.</div>";
                                header("refresh: 3");
                            }
                        }
                    }

                    // form for new answer submission
                    echo "<form method=\"POST\">";
                    echo "<h2>Your Answer:</h2>";
                    echo "<textarea class=\"answertextbox\" name=\"abody\" required></textarea> <br> <br>";
                    echo "<input class=\"button\" type=\"submit\" name=\"answer\" value=\"Submit\"> <br> <br>";
                    echo "</form>";

                    // show posted answers
                    echo "<h1>Answers:</h1>";
                    $query = "SELECT aid, abody, adate, atime, auid, username, COUNT(tuid) AS thumbsups, isBestAns
                              FROM Answer NATURAL lEFT OUTER JOIN Thumbsup, Users
                              WHERE qid = ? AND auid = uid
                              GROUP BY aid, abody, adate, atime
                              ORDER BY adate, atime";
                    if($astmt = $conn->prepare($query)){
                        $astmt->bind_param("i", $qid);
                        $astmt->execute();
                        $answer = $astmt->get_result();
                        $astmt->close();

                        while($row = $answer->fetch_assoc()){
                            $aid = $row["aid"];
                            $abody = $row["abody"];
                            $adate = $row["adate"];
                            $atime = $row["atime"];
                            $auid = $row["auid"];
                            $ausername = $row["username"];
                            $thumbsups = $row["thumbsups"];
                            $isBestAns = $row["isBestAns"];

                            echo "<div class=\"answerbox\">";
                            echo "<div class=\"answerdatabox\">";
                            echo "<p>Answered $adate at $atime by $ausername</p><br>";
                            echo "</div>";
                            echo "<p>$abody</p>";
                            echo "<div class=\"answerdatabox\">";


                            // if thumbs-up button is pressed
                            if(isset($_POST["thumbsup$aid"])){
                                // check if the answer was posted by the current user;
                                // disallow thumbs-up and show a message if so; proceed if not
                                if($uid != $auid){
                                    // check if the current user gave a thumbs-up to the answer previously
                                    // cancel the thumbs-up if so; insert a thumbs-up tuple if not
                                    $query = "SELECT * FROM Thumbsup WHERE aid = ? AND tuid = ?";
                                    if($tstmt = $conn->prepare($query)){
                                        $tstmt->bind_param("ii", $aid, $uid);
                                        $tstmt->execute();
                                        $tresult = $tstmt->get_result();
                                        $tstmt->close();

                                        if($tresult && mysqli_num_rows($tresult) > 0){
                                            $query = "DELETE FROM Thumbsup WHERE aid = ? AND tuid = ?";
                                            if($tstmt = $conn->prepare($query)) {
                                                $tstmt->bind_param("ii", $aid, $uid);
                                                $tstmt->execute();
                                                $tstmt->close();
                                                echo "<div class=\"regularMsg\" style=\"font-size:16px\">You've cancelled your thumbs-up.</div>";
                                            }
                                        }
                                        else{
                                            $query = "INSERT INTO Thumbsup(aid, tuid) VALUES(?,?)";
                                            if($tstmt = $conn->prepare($query)){
                                                $tstmt->bind_param("ii", $aid, $uid);
                                                $tstmt->execute();
                                                $tstmt->close();
                                            }
                                        }
                                    }
                                }
                                else{
                                    echo "<div class=\"warningMsg\" style=\"font-size:16px\">You can't give your own answer a thumbs-up.</div>";
                                }
                                header("refresh: 3");
                            }

                            // show number of thumpups
                            echo "<p style=\"color:darkblue\"><b>Thumbs-ups $thumbsups</b></p>";

                            // check if the user gave a thumbs-up to this answer and show a message if so
                            $query = "SELECT * FROM Thumbsup WHERE aid = ? AND tuid = ?";
                            if($tstmt = $conn->prepare($query)) {
                                $tstmt->bind_param("ii", $aid, $uid);
                                $tstmt->execute();
                                $tresult = $tstmt->get_result();
                                $tstmt->close();

                                if ($tresult && mysqli_num_rows($tresult) > 0) {
                                    if($trow = $tresult->fetch_assoc()){
                                        $tdate= $trow["tdate"];
                                        $ttime= $trow["ttime"];
                                        echo "<p style=\"color:darkblue\">You gave a thumbs-up to this answer $tdate at $ttime</p>";
                                    }
                                }
                            }
                            echo "</div>";

                            // thumbs-up button
                            echo "<form class=\"buttonform\" style=\"display:inline-block;\" method=\"POST\">";
                            echo "<input id=\"thumpupbtn\" type=\"submit\" name=\"thumbsup$aid\" value=\"Thumbs-up\">";
                            echo "</form><br>";

                            // show "Best Answer" if the answer was selected as the best answer
                            echo "<div class=\"answerdatabox\">";
                            if ($isBestAns){
                                echo "<p style=\"color:darkgoldenrod\"><b>Best Answer</b></p>";
                            }

                            // if best answer button is pressed
                            if(isset($_POST["bestans$aid"])){
                                // check if another answer was selected as the best answer
                                $query = "SELECT aid FROM Answer WHERE qid = ? and isBestAns = 1";
                                if($bstmt = $conn->prepare($query)){
                                    $bstmt->bind_param("i", $qid);
                                    $bstmt->execute();
                                    $bresult = $bstmt->get_result();
                                    $bstmt->close();

                                    // update the tuple for the answer previously selected as the best answer
                                    if($bresult && mysqli_num_rows($bresult) > 0){
                                        while($brow = $bresult->fetch_assoc()){
                                            $baid = $brow["aid"];
                                            $query = "UPDATE Answer SET isBestAns = 0 WHERE aid = ?";
                                            if($bstmt = $conn->prepare($query)) {
                                                $bstmt->bind_param("i", $baid);
                                                $bstmt->execute();
                                                $bstmt->close();
                                            }
                                        }
                                    }
                                }

                                // update the tuple for the answer newly selected as the best answer
                                $query = "UPDATE Answer SET isBestAns = 1 WHERE aid = ?";
                                if($bstmt = $conn->prepare($query)) {
                                    $bstmt->bind_param("i", $aid);
                                    $bstmt->execute();
                                    $bstmt->close();
                                }
                                echo "<div class=\"regularMsg\" style=\"font-size:16px\">You've updated your best answer selection.</div><br>";
                                header("refresh: 3");
                            }

                            // show best answer button if the question was posted by the current user and was not selected as the best answer
                            if ($quid == $uid && !$isBestAns){
                                echo "<form class=\"buttonform\" style=\"display:inline-block;\" method=\"POST\">";
                                echo "<input id=\"bestAnsbtn\" type=\"submit\" name=\"bestans$aid\" value=\"Select as best answer\">";
                                echo "</form><br>";
                            }
                            echo "</div><br><br>";


                            // show edit button if the answer was posted by the current user
                            if ($auid == $uid){
                                echo "<form action=\"answeredit.php?aid=$aid&auid=$uid\" class=\"buttonform\" method=\"POST\">";
                                echo "<input id=\"editbtn\" type=\"submit\" name=\"edit\" value=\"Edit\">";
                                echo "</form><br>";
                            }

                            echo "</div> <br> <br> <br>";

                        }
                    }
                }
            }
            $conn->close();
        }
        ?>
    </div>
</section>

</body>
</html>
