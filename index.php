<?php # index.php
// This is the main page for the site.
// This page shows the leaderboard

// Always need the configuration file first:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'Leaderboard';
include ('includes/header.html');

// Show the leaderboard:
echo '<h1>Leaderboard</h1>
	<p id="message">Players are ordered by their win-loss ratio.</p>';

// Show the form:
?>
<form action="index.php" method="post" name="leaderForm" id="leaderForm">
    <select id="leaderboard">
        <option value="1">All time leaderboard</option>
        <option value="2">This weeks leaderboard</option>
        <option value="3">Last weeks leaderboard</option>
    </select>
    <noscript><input type="submit" value="Submit"></noscript>
</form>
<script src="js/index.js"></script>
<table id="leaderTable"><thead><tr><th>Name</th><th>Ratio</th><th>Wins</th><th>Losses</th></tr></thead>
<tbody>
<?php

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Need the database connection:
    require(MYSQL);


    // Make the query:
    $q = "SELECT userID, firstName, lastName, ratio, wins, losses FROM users ORDER BY ratio DESC";
    $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    // List each item:
    while (list($userId, $firstName, $lastName, $ratio, $win, $loss) = mysqli_fetch_array($r, MYSQLI_NUM)) {
            $name = $firstName . ' ' . $lastName;
            echo "<tr><td><a href=\"view.php?userId=$userId\">$name</a></td><td>$ratio</td><td>$win</td><td>$loss</td></tr>\n";
    }

    // Clean up:
    mysqli_free_result($r);
    mysqli_close($dbc);
}
?>
</tbody></table>
<?php include ('includes/footer.html'); ?>
