<?php
ob_start();
session_start();
include("connectdb.php");
include("functions.php");
$userdata = check_login($conn);
$uid=$userdata["uid"];
$username = $userdata["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Expertise Homepage</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<body>

<?php include("navigation.php");?>

<section>
    <div class="sec-container-dark">
    <?php
    echo "<h1 style=\"color: white\">Hi, $username</h1>";

    $upoints = $userdata["upoints"];
    $ustatus = $userdata["ustatus"];

    echo "<h3 style=\"color: white\">Points: $upoints</h3>";
    echo "<h3 style=\"color: white\">Status: $ustatus</h3>";
    ?>
    </div><br>

    <div class="sec-container-light">
    <?php
    echo "<h1 style=\"color: #666666;\">Your Recent Question(s):</h1>";
    $query = "SELECT qid, qtitle, qdate, qtime FROM Question WHERE quid = ? ORDER BY qdate DESC, qtime DESC LIMIT 3";
    if($stmt = $conn->prepare($query)){
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($qid, $qtitle, $qdate, $qtime);
        while($stmt->fetch()){
            echo "<div class=\"questionbox\">";
            echo "<a href=\"questiondetail.php?qid=$qid\">$qtitle</a> <br>";
            echo "<div class=\"questiondatabox\">";
            echo "<p>Posted $qdate at $qtime</p>";
            echo "</div>";

            // show edit button
            echo "<form action=\"questionedit.php?qid=$qid&quid=$uid\" class=\"buttonform\" method=\"POST\">";
            echo "<input id=\"editbtn\" type=\"submit\" name=\"edit\" value=\"Edit\">";
            echo "</form>";

            echo "</div> <br> <br>";
        }
        $stmt->close();
    }
    ?>
    </div><br>

    <div class="sec-container-light">
        <?php
        echo "<h1 style=\"color: #666666;\">Your Recent Answer(s):</h1>";
        $query = "SELECT qid, qtitle, aid, abody, adate, atime, count(tuid) as thumbsups, isBestAns
              FROM Question NATURAL JOIN Answer NATURAL LEFT OUTER JOIN Thumbsup
              WHERE auid = ?
              GROUP BY qid, qtitle, aid, abody, adate, atime
              ORDER BY adate DESC, atime DESC 
              LIMIT 3";
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
                // show number of thumpups
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



        $conn->close();
        ?>
    </div>
</section>


</body>
</html>

