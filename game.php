<?php # game.php
// This page allows players to enter the result of a game.

// Always need the configuration file:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'New Game';
include ('includes/header.html');

// Include the heading here:
echo '<h1>New Game</h1><p id="message">Enter the result of your game below.</p>
';

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
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
                
                if (@mysqli_query($dbc, $q1)) {
                    if (@mysqli_query($dbc, $q2)) {
                        if (@mysqli_query($dbc, $q3)) {
                            
                            mysqli_close($dbc);

                            // Display the status:
                            echo '<p class="good">Game has been logged.</p>';

                            // Include the footer:
                            include ('includes/footer.html');

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

	} // End of $errors IF.

	// Close the database connection:
	mysqli_close($dbc);
	
} // End of form submission check.

// Display any errors:
if (isset($errors) && is_array($errors)) {
	echo '<p>The following error(s) occurred:<ul>';
	foreach ($errors as $error) {
		echo "<li class=\"error\">$error</li>";
	}
	echo '</ul></p>';
	
}

// Show the form:
?>
<form action="game.php" method="post" id="gameForm">
        <select name="player1" id="player1">
            <option value="">--Winning Player--</option>
            <?php
            $query1 = "SELECT userId, firstName, lastName FROM users ORDER BY firstName";
            $result1 = mysqli_query ($dbc, $query1) or trigger_error("Query: $query1\n<br />MySQL Error: " . mysqli_error($dbc));
            while(list($userId, $firstName, $lastName) = mysqli_fetch_array($result1, MYSQLI_NUM)) {
                $name = $firstName . ' ' . $lastName;
                echo "<option value=\"".$userId."\">".$name."</option>\n";
            }
            ?>
        </select><span>&nbsp;&nbsp;defeated&nbsp;</span>
        <select name="player2" id="player2">
            <option value="">--Losing Player--</option>
            <?php
            $query2 = "SELECT userId, firstName, lastName FROM users ORDER BY firstName";
            $result2 = mysqli_query ($dbc, $query2) or trigger_error("Query: $query2\n<br />MySQL Error: " . mysqli_error($dbc));
            while(list($userId, $firstName, $lastName) = mysqli_fetch_array($result2, MYSQLI_NUM)) {
                $name = $firstName . ' ' . $lastName;
                echo "<option value=\"".$userId."\">".$name."</option>\n  ";
            }
            ?>
        </select>
	<br>
	<input class="button" type="submit" value="Submit">
</form>

<script src="js/game.js"></script>

<?php include ('includes/footer.html'); ?>