<?php # game.php
// This page handles the login form via Ajax!

// Always need the configuration file:
require('../includes/config.inc.php');

// Need to start the session:
session_start();

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userId'])) {
	
	// Array for storing errors:
	$errors = array();
	
	// Validate result:
        if (isset($_POST['result']) && !empty($_POST['result'])) {
                $result = mysqli_real_escape_string ($dbc, $_POST['result']);
        } else {
                $errors[] = 'You forgot to select the result!';
        }

        // Validate opponent:
        if (isset($_POST['opponent']) && !empty($_POST['opponent'])) {
            $p2 = mysqli_real_escape_string ($dbc, $_POST['opponent']);
            $oppEmail = mysqli_query($dbc, "SELECT email FROM users WHERE userId='".$p2."'");
        } else {
                $errors[] = 'You forgot to select your opponent!';
        }
	
	if (empty($errors)) { // No errors!
            if($result = 1) {
                $winner = $_SESSION['userId'];
                $loser = $p2;
                $oppResult = 'lost';
            } else {
                $winner = $p2;
                $loser = $_SESSION['userId'];
                $oppResult = 'won';
            }
            $time = date("Y-m-d H:i:s");
            // Query the database:
            $q1 = "UPDATE users SET wins = wins + 1, ratio = 1 - (losses/(wins + losses)) WHERE userId = '".$winner."'";
            $q2 = "UPDATE users SET losses = losses + 1, ratio = 1 - (losses/(wins + losses)) WHERE userId = '".$loser."'";
            $q3 = "INSERT INTO games (winner, loser, timePlayed) VALUES ('$winner', '$loser', '$time')";

            // Execute the query:
            if (@mysqli_query($dbc, $q1)) {
                if (@mysqli_query($dbc, $q2)) {
                    if (@mysqli_query($dbc, $q3)) {
                        $message = 'Hi,\r\nYou '.$oppResult.' against '.$_SESSION["firstName"].' '
                                .$_SESSION["lastName"].'.\r\n This match was logged by '
                                .$_SESSION["firstName"].' '.$_SESSION["lastName"].' at '.$time
                                .' PST.\r\nThanks,\r\nGR Ping Pong';
                        $message = wordwrap($message, 70, "\r\n");
                        mail($oppEmail, 'New Ping Pong Match Logged', $message, 'From: omarviz@gmail.com');
                        
                        mysqli_close($dbc);
                        
                        // Display the status:
                        echo 'VALID';

                        // Quit the script:
                        exit();
                    } else {
                            $errors[] = "Query: $q3\n<br />MySQL Error: " . mysqli_error($dbc) ;
                    }
                } else {
                        $errors[] = "Query: $q2\n<br />MySQL Error: " . mysqli_error($dbc) ;
                }
            } else {
                    $errors[] = "Query: $q1\n<br />MySQL Error: " . mysqli_error($dbc) ;
            }
	}// End of $errors IF.

	mysqli_close($dbc);

} // End of form submission check.
echo 'INVALID';