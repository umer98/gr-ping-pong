<?php # view.php
// This page views the details of a particular user and shows the result of the past five games for the user.

// Always need the configuration file first:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'View User';
include ('includes/header.html');

// Validate the userId:
$userId = false;
if (isset($_GET['userId']) && filter_var($_GET['userId'], FILTER_VALIDATE_INT, array('min_range' => 1))) {
	$userId = $_GET['userId'];
}

// If an invalid userId, show and error and terminate the script:
if (!$userId) {
	echo '<p class="error">This page has been accessed in error!</p>';
        mysqli_close($dbc);
	include ('includes/footer.html');
	exit();
}

$nameQuery = "SELECT firstName, lastName FROM users WHERE userId = '".$userId."'";
$nameResult = mysqli_query ($dbc, $nameQuery) or trigger_error("Query: $nameQuery\n<br />MySQL Error: " . mysqli_error($dbc));

list ($firstName, $lastName) = mysqli_fetch_array($nameResult, MYSQLI_NUM);
$name = $firstName.' '.$lastName;
// Print the initial information:
echo "<h1 id=\"userHeading\">$name</h1>";

// Display the last 5 games:
// Make the query:
$q = "SELECT winner, loser FROM games WHERE (winner = '".$userId."' OR loser = '".$userId."') ORDER BY timePlayed DESC LIMIT 5";
$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

// Print the header:
echo "<table>
        <caption>Result of the last 5 games</caption>
	<thead><tr><th>Opponent</th><th>Result</th></tr></thead>
	<tbody id=\"tableBody\">";

// Loop through the results:
while (list ($winner, $loser) = mysqli_fetch_array($r, MYSQLI_NUM)) {
        if ($userId == $winner) {
            $nameQ = "SELECT firstName, lastName FROM users WHERE userId = '".$loser."'";
            $nameR = mysqli_query ($dbc, $nameQ) or trigger_error("Query: $nameQ\n<br />MySQL Error: " . mysqli_error($dbc));

            list ($firstN, $lastN) = mysqli_fetch_array($nameR, MYSQLI_NUM);
            $n = $firstN.' '.$lastN;
            
            $resultText = 'Win';
            
            // Print the row:
            echo "<tr><td>$n</td><td>$resultText</td></tr>\n";
        }
        else {
            $nameQ = "SELECT firstName, lastName FROM users WHERE userId = '".$winner."'";
            $nameR = mysqli_query ($dbc, $nameQ) or trigger_error("Query: $nameQ\n<br />MySQL Error: " . mysqli_error($dbc));

            list ($firstN, $lastN) = mysqli_fetch_array($nameR, MYSQLI_NUM);
            $n = $firstN.' '.$lastN;
            
            $resultText = 'Loss';
            
            // Print the row:
            echo "<tr><td>$n</td><td>$resultText</td></tr>\n";
        }
		
}

// Clean up:
mysqli_free_result($nameResult);
mysqli_free_result($r);
mysqli_close($dbc);

// Complete the table:
echo '</tbody>
</table>';

// Include the footer:
include ('includes/footer.html'); 
?>