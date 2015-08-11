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
	
	// Validate player 1:
        if (isset($_POST['player1']) && !empty($_POST['player1'])) {
                $p1 = mysqli_real_escape_string ($dbc, $_POST['player1']);
                // Validate player 2:
                if (isset($_POST['player2']) && !empty($_POST['player2'])) {
                        $p2 = mysqli_real_escape_string ($dbc, $_POST['player2']);
                        // Validate player 2:
                        if ($_POST['player1'] != $_POST['player2']) {
                                $p2 = mysqli_real_escape_string ($dbc, $_POST['player2']);
                        } else {
                                $errors[] = "A player can't play themself!";
                        }
                } else {
                        $errors[] = 'You forgot to select the loser!';
                }
        } else {
                $errors[] = 'You forgot to select the winner!';
        }
	
	if (empty($errors)) { // No errors!
                $time = date("Y-m-d H:i:s");
                // Query the database:
                $q1 = "UPDATE users SET wins = wins + 1, ratio = 1 - (losses/(wins + losses)) WHERE userId = '".$p1."'";
                $q2 = "UPDATE users SET losses = losses + 1, ratio = 1 - (losses/(wins + losses)) WHERE userId = '".$p2."'";
                $q3 = "INSERT INTO games (winner, loser, timePlayed) VALUES ('$p1', '$p2', '$time')";

		// Execute the query:
                if (@mysqli_query($dbc, $q1)) {
                    if (@mysqli_query($dbc, $q2)) {
                        if (@mysqli_query($dbc, $q3)) {

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