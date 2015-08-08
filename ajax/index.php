<?php # index.php
// This is the main page for the site.
// This page shows the leaderboard

// Always need the configuration file first:
require('../includes/config.inc.php');

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Array for storing errors:
    $errors = array();
    
    //Variable for storing data
    $content = array();

    // Validate the leaderboard value:
    if (!isset($_POST['leaderboard']) && empty($_POST['leaderboard'])) {
            $errors[] = 'There was an error with your leaderboard selection!';
    }
    
    if (empty($errors)) { // No errors!
        
        if($_POST['leaderboard'] == 2 || $_POST['leaderboard'] == 3) {
            $weekStart = $_POST['weekstart'];
            $weekEnd = $_POST['weekend'];
            
            $q = "SELECT * FROM (select winner, count(winner) as w FROM games WHERE timePlayed 
                BETWEEN '$weekStart' AND '$weekEnd' GROUP BY winner) AS W LEFT OUTER 
                JOIN (select loser, count(loser) as l FROM games WHERE timePlayed 
                BETWEEN '$weekStart' AND  '$weekEnd' GROUP BY loser) AS L 
                on W.winner=L.loser UNION SELECT * FROM (select winner, count(winner) as w FROM games 
                WHERE timePlayed BETWEEN '$weekStart' AND  '$weekEnd' GROUP BY winner) 
                AS W RIGHT OUTER JOIN (select loser, count(loser) as l FROM games WHERE timePlayed 
                BETWEEN '$weekStart' AND  '$weekEnd' GROUP BY loser) AS L 
                on W.winner=L.loser ORDER BY (IFNULL(w, 0)/(IFNULL(w, 0)+IFNULL(l, 0))) DESC";
            /*
            $q = "SELECT * FROM (select winner, count(winner) as w FROM games WHERE timePlayed "
                    . " BETWEEN '$weekStart' AND  '$weekEnd' GROUP BY winner) AS W "
                    . " JOIN (select loser, count(loser) as l FROM games WHERE timePlayed "
                    . " BETWEEN '$weekStart' AND  '$weekEnd' GROUP BY loser) AS L "
                    . " on W.winner=L.loser ORDER By(W.w/(W.w+L.l)) DESC";
              
            */
            $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
            while (list($userId, $wins, $userId2, $losses) = mysqli_fetch_array($r, MYSQLI_NUM)) {
                if($userId == null) {$userId = $userId2;}
                if($wins == null) {$wins = 0;}
                if($losses == null) {$losses = 0;}
                $q2 = "SELECT firstName, lastName FROM users WHERE userID='$userId'";
                $r2 = mysqli_query ($dbc, $q2) or trigger_error("Query: $q2\n<br />MySQL Error: " . mysqli_error($dbc));
                list($firstName, $lastName) = mysqli_fetch_array($r2, MYSQLI_NUM);
                $name = $firstName . ' ' . $lastName;
                $ratio = $wins / ($wins + $losses);
                $content[] = array("userId"=>$userId, "name"=>$name, "ratio"=>$ratio, "wins"=>$wins, "losses"=>$losses);

                mysqli_free_result($r2);
            }
        } else {
            // Define the Query:
            $q = "SELECT userID, firstName, lastName, ratio, wins, losses FROM users ORDER BY ratio DESC";
            $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
            // List each item:
            while (list($userId, $firstName, $lastName, $ratio, $wins, $losses) = mysqli_fetch_array($r, MYSQLI_NUM)) {
                $name = $firstName . ' ' . $lastName;
                // Return the data:
                $content[] = array("userId"=>$userId, "name"=>$name, "ratio"=>$ratio, "wins"=>$wins, "losses"=>$losses);
            }
        }
        // Clean up:
        mysqli_free_result($r);
        mysqli_close($dbc);

        echo json_encode($content);
        
        // Quit the script:
        exit();
    }// End of $errors IF.

    mysqli_close($dbc);

} // End of form submission check.
echo 'INVALID';