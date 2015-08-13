<?php # game.php
// This page allows players to enter the result of a game.

// Always need the configuration file:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'New Game';
include ('includes/header.html');

// If the user is not logged in:
if (!isset($_SESSION['userId'])) {
    echo '<h1>New Game</h1><p class="caution">You must <a href="login.php">log in</a> to enter the result of games you have played.</p>';
    mysqli_close($dbc);
    include ('includes/footer.html');
    exit();
} else { // Logged in.

    // Include the heading here:
    echo '<h1>New Game</h1><p id="message">Enter the result of your game below.</p>';

    // Check for a form submission:
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
                $result = mysqli_query($dbc, "SELECT email FROM users WHERE userId='".$p2."'");
                $oppEmail = mysqli_fetch_array($result, MYSQLI_NUM);
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

                    if (@mysqli_query($dbc, $q1)) {
                        if (@mysqli_query($dbc, $q2)) {
                            if (@mysqli_query($dbc, $q3)) {
                                
                                $message = 'Hi,\r\nYou '.$oppResult.' against '.$_SESSION["firstName"].' '
                                        .$_SESSION["lastName"].'.\r\n This match was logged by '
                                        .$_SESSION["firstName"].' '.$_SESSION["lastName"].' at '.$time
                                        .' PST.\r\nThanks,\r\nGR Ping Pong';
                                $message = wordwrap($message, 70, "\r\n");
                                mail($oppEmail[0], 'New Ping Pong Match Logged', $message, 'From: email@grpingpong.com');
                                
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
    echo '<form action="game.php" method="post" id="gameForm">'
            . $_SESSION["firstName"] . ' ' . $_SESSION["lastName"] . ' ' .
            '<select name="result" id="result">
                <option value="">--Result--</option>
                <option value="1">Won</option>
                <option value="2">Lost</option>
            </select>
            &nbsp;<span>Against</span>&nbsp;
            <select name="opponent" id="opponent">
                <option value="">--Opponent--</option>';
                
                $query1 = "SELECT userId, firstName, lastName FROM users WHERE userId != '".$_SESSION['userId']."' ORDER BY firstName";
                $result1 = mysqli_query ($dbc, $query1) or trigger_error("Query: $query1\n<br />MySQL Error: " . mysqli_error($dbc));
                while(list($userId, $firstName, $lastName) = mysqli_fetch_array($result1, MYSQLI_NUM)) {
                    $name = $firstName . ' ' . $lastName;
                    echo "<option value=\"".$userId."\">".$name."</option>\n";
                }
                
            echo '</select>
            <br>
            <input class="button" type="submit" value="Submit">
    </form>';

    include ('includes/footer.html');
} ?>