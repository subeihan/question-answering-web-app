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
        if(isset($_GET["uid"])){
            if ($_GET["uid"] == $uid){//get answers posted by the current user
                // users will not be able to see the page belonging to another user by changing the ?uid= parameter
                echo "<div style=\"font-size:30px; color: #666666;\">My Answer(s)</div> <br> <br>";
                $query = "SELECT qid, qtitle, aid, abody, adate, atime, count(tuid) as thumbsups, isBestAns
                      FROM Question NATURAL JOIN Answer NATURAL LEFT OUTER JOIN Thumbsup
                      WHERE auid = ?
                      GROUP BY qid, qtitle, aid, abody, adate, atime
                      ORDER BY adate DESC, atime DESC";
                if($stmt = $conn->prepare($query)){
                    $stmt->bind_param("i", $uid);
                    $stmt->execute();
                    $stmt->bind_result($qid, $qtitle, $aid, $abody, $adate, $atime, $thumbsups, $isBestAns);
                    while($stmt->fetch()){
                        echo "<div class=\"answerbox\">";
                        echo "<a href=\"questiondetail.php?qid=$qid\">Question: $qtitle</a> <br> <br>";
                        echo "<p>Answer: $abody</p>";
                        echo "<div class=\"answerdatabox\">";
                        echo "<p>Answered $adate at $atime</p>";
                        // show "Best Answer" if the answer was selected as the best answer
                        if ($isBestAns){
                            echo "<p style=\"color:darkgoldenrod\"><b>Best Answer </b></p>";
                        }
                        // show number of thumbs-ups
                        echo "<p style=\"color:darkblue\"><b>Thumbs-ups $thumbsups</b></p>";
                        echo "</div>";

                        // show edit button
                        echo "<form action=\"answeredit.php?aid=$aid&auid=$uid\" class=\"buttonform\" method=\"POST\">";
                        echo "<input id=\"editbtn\" type=\"submit\" name=\"edit\" value=\"Edit\">";
                        echo "</form>";

                        echo "</div> <br> <br> <br>";

                    }
                    $stmt->close();
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
