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
    <title>Expertise</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet", href="style.css">
</head>

<body>
<?php include("navigation.php");?>

<section>
    <div class="largebox">

        <!--insert tuple for the new question-->
        <?php
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["submit"])){
            if(isset($_POST["topicid"]) && isset($_POST["qtitle"]) && isset($_POST["qbody"])){
                $in_quid = $userdata["uid"];
                $in_tid = $_POST["topicid"];
                $in_qtitle= $_POST["qtitle"];
                $in_qbody= $_POST["qbody"];

                $query = "INSERT INTO Question(quid, tid, qtitle, qbody) VALUES(?, ?, ?, ?)";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("iiss", $in_quid, $in_tid, $in_qtitle, $in_qbody);
                    $stmt->execute();
                    $stmt->close();
                    $conn->close();
                    echo "<div class=\"regularMsg\">You have successfully posted your question.</div>";
                    header("refresh: 3");
                }
            }
        }
        ?>

        <!-- form for new question-->
        <form method="POST">
            <div style="font-size:30px; color: #666666;">Question</div> <br> <br>
            <?php
            echo "<div class=\"label\">Select a Topic(*):</div>";
            echo "<select name='topicid' required>";
            echo "<option value=\"\" disabled selected>Please select a topic</option> <br>";
            if ($categorystmt = $conn->prepare("SELECT * FROM Category")) {
                $categorystmt->execute();
                $categories = $categorystmt->get_result();
                $categorystmt->close();

                while($categoryrow = $categories->fetch_assoc()) {
                    $cid = $categoryrow["cid"];
                    $cname = $categoryrow["cname"];
                    echo "<option class=\"optdivider\" value=\"\" disabled>Category - $cname</option> <br>";
                    if ($topicstmt = $conn->prepare("SELECT tid, tname FROM Topic WHERE cid = ?")) {
                        $topicstmt->bind_param("i", $cid);
                        $topicstmt->execute();
                        $topicstmt->bind_result($tid, $tname);
                        while ($topicstmt->fetch()) {
                            echo "<option value=$tid>$tname</option> <br>";
                        }
                        $topicstmt->close();
                    }
                }
            }
            echo "</select> <br> <br>";
            ?>
            <div class="label">Question Title(*):</div>
            <input class= "textbox" type= "text" name = "qtitle" required> <br> <br>
            <div class="label">Question Details(*):</div>
            <textarea class="multilinetextbox" name="qbody" required></textarea> <br> <br>
            <div class="label">* required filed</div> <br> <br>

            <input class="button" type="submit" name="submit" value="Post" /> <br> <br>
        </form>


    </div>


</section>

</body>
</html>