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
        if(isset($_GET["tid"]) && isset($_GET["tname"])){//get question by topic for topic browsing
            $in_tid = $_GET["tid"];
            $in_tname = $_GET["tname"];
            echo "<div style=\"font-size:30px; color: #666666;\">Topic: $in_tname</div> <br> <br>";
            $query = "SELECT qid, qtitle, qdate, qtime, quid, username FROM Question, Users WHERE tid = ? AND quid = uid ORDER BY qdate DESC, qtime DESC";
            if($stmt = $conn->prepare($query)){
                $stmt->bind_param("i", $in_tid);
                $stmt->execute();
                $stmt->bind_result($qid, $qtitle, $qdate, $qtime, $quid, $qusername);
                while($stmt->fetch()){
                    echo "<div>";
                    echo "<a href=\"questiondetail.php?qid=$qid\">$qtitle</a> <br>";
                    echo "<div class=\"questiondatabox\">";
                    echo "<p tyle=\"color:darkblue\">Posted $qdate at $qtime by $qusername</p>";
                    echo "</div>";
                    echo "</div> <br> <br>";
                }
                $stmt->close();
            }
        }
        else if(isset($_GET["uid"])){//get question by the current user for my question query
            // users will not be able to see the page belong to another user by changing the ?uid= parameter
            if ($_GET["uid"] == $uid){
                echo "<div style=\"font-size:30px; color: #666666;\">My Question(s)</div> <br> <br>";
                $query = "SELECT qid, qtitle, qdate, qtime FROM Question WHERE quid = ? ORDER BY qdate DESC, qtime DESC";
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
            }
            else{
                echo "<div style=\"font-size:30px; color: #666666;\">You are not allowed to access this page</div> <br> <br>";
            }
        }
        else if(isset($_GET["keywords"])){// get question by keyword searching
            $in_keywords = $_GET["keywords"];
            echo "<h1>Search Result for Keyword(s): $in_keywords</h1>";
            echo "<h3>Sort by relevance</h3><br>";
            $query = "SELECT qid, qtitle, qdate, qtime, quid, username, 
                            MATCH(qtitle) AGAINST(? IN NATURAL LANGUAGE MODE) * 1.2 + MATCH(qbody) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                      FROM Question, Users 
                      WHERE (MATCH(qtitle) AGAINST(? IN NATURAL LANGUAGE MODE) OR MATCH(qbody) AGAINST(? IN NATURAL LANGUAGE MODE))
                            AND quid = uid
                      ORDER BY relevance DESC";
            if($stmt = $conn->prepare($query)){
                $stmt->bind_param("ssss",  $in_keywords, $in_keywords, $in_keywords, $in_keywords);
                $stmt->execute();
                $stmt->bind_result($qid, $qtitle, $qdate, $qtime, $quid, $qusername, $relevance);
                while($stmt->fetch()){
                    echo "<div>";
                    echo "<a href=\"questiondetail.php?qid=$qid\">$qtitle</a> <br>";
                    echo "<p style=\"color:darkblue\">Posted $qdate at $qtime by $qusername</p>";
                    echo "</div> <br> <br>";
                }
                $stmt->close();
            }

        }
        $conn->close();
        ?>
    </div>
</section>

</body>
</html>
