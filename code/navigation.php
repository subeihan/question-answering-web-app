<header style="display:flex">
    <div style="text-align: left; margin-left:20px; margin-right:auto; display：inline-block;">
        <h2>Expertise</h2>
    </div>
    <div class="header-dropdown">
        <button class="header-dropbtn"><?php echo "$username"?></button>
        <div class="header-dropdown-content">
            <a href="profileedit.php?uid=<?php echo "$uid"?>">My Account</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<nav>
    <div class="nav-item">
        <button class="nav-button">
            <a href="index.php">Home</a>
        </button>
    </div>
    <div class="nav-item">
        <button class="nav-button">Browse Topics</button>
        <div class="nav-dropdown-content">
            <?php
            // show query results of top-level topics from backend database
            if ($stmt = $conn->prepare("SELECT * FROM Category")) {
                $stmt->execute();
                $stmt->bind_result($cid, $cname);
                while($stmt->fetch()) {
                    echo "<a href=\"gettopic.php?cid=$cid&cname=$cname\">$cname</a>";
                }
            }
            ?>
        </div>
    </div>
    <div class="nav-item">
        <button class="nav-button">My Question and Answer</button>
        <div class="nav-dropdown-content">
            <?php
            echo "<a href=\"getquestion.php?uid=$uid\">My Question</a>";
            echo "<a href=\"getanswer.php?uid=$uid\">My Answer</a>";
            ?>
        </div>
    </div>
    <div class="nav-item">
        <button class="nav-button">
            <a href="newquestion.php">New Question</a>
        </button>
    </div>
    <div class="nav-item">
        <form action="getquestion.php" method="GET">
            <input id="searchbox" type="text" name="keywords" placeholder="Search for Questions">
        </form>
    </div>
</nav>