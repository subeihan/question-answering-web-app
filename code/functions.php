<?php
    function check_login($conn) {
        if(isset($_SESSION['uid'])){
            $uid = $_SESSION['uid'];
            $query = "SELECT uid, username, email, upassword, city, state, country, IFNULL(uprofile, '') AS uprofile, upoints, ustatus
                      FROM Users WHERE uid = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("i", $uid);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                if($result && mysqli_num_rows($result) > 0){
                    $userdata = $result->fetch_array(MYSQLI_ASSOC);
                    return $userdata;
                }
            }
        }

        //redirect to login page
        header("Location: login.php");
        exit;
    }



