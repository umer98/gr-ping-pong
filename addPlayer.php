<?php # addPlayer.php
// This is the main page for the site.
// This page provides the ability to add a player

// Always need the configuration file first:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'Add Player';
include ('includes/header.html');

// Include the heading here:
echo '<h1>Add Player</h1><p id="message">Add yourself to the list of ping pong players here.</p>';

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Need the database:
	require (MYSQL);
        
        // Array for storing errors:
	$errors = array();
        
        // Validate the first name:
	if (isset($_POST['firstname']) && !empty($_POST['firstname'])) {
		$fName = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['firstname'])));
                // Validate the last name:
                if (isset($_POST['lastname']) && !empty($_POST['lastname'])) {
                        $lName = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['lastname'])));
                        $search = mysqli_query($dbc, "SELECT * FROM users WHERE (firstName='$fName' AND lastName='$lName')");

                        if (@mysqli_num_rows($search) != 0) {
                            $errors[] = 'That name already exists! If somehow you have the exact same name as someone else at GR, enter your nickname.';
                        }
                } else {
                        $errors[] = 'You forgot to enter your last name!';
                }
	} else {
		$errors[] = 'You forgot to enter your first name!';
	}
        
        if (empty($errors)) { // No errors!
		$ratio = 0;
                $win = 0;
                $loss = 0;
		// Define the Query:
		$q = "INSERT INTO users (firstName, lastName, ratio, wins, losses) VALUES ('$fName', '$lName', '$ratio', '$win', '$loss')";
		
		// Execute the query:
                if (@mysqli_query($dbc, $q)) {
                        print '<p class="good">You have been added!</p>';
                        
                        // Clean up:
			mysqli_close($dbc);
                        
                        // Include the footer:
			include ('includes/footer.html');
			
			// Quit the script:
			exit(); 
                } else {
                        $errors[] = "Query: $q\n<br />MySQL Error: " . mysqli_error($dbc) ;
                }

	} // End of $errors IF.

	// Close the database connection:
	mysqli_close($dbc);
        
}

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
<form action="addPlayer.php" method="post" id="addPlayer">
	<label>First Name</label>
        <input name="firstname" id="firstName" type="text" required>
	<label>Last Name</label>
        <input name="lastname" id="lastName" type="text" required>
	<br>
	<input class="button" type="submit" value="Add">
</form>

<//script src="js/addPlayer.js"></script>

<?php include ('includes/footer.html'); ?>