<?php # login.php
// This page handles the login form via Ajax!

// Always need the configuration file:
require('../includes/config.inc.php');

// Need to start the session:
session_start();

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Array for storing errors:
	$errors = array();
	
	// Validate the email:
	if (isset($_POST['email']) && !empty($_POST['email'])) {
		$u = mysqli_real_escape_string ($dbc, $_POST['email']);
	} else {
		$errors[] = 'You forgot to enter your email!';
	}
	
	// Validate the password:
	if (isset($_POST['userpass']) && !empty($_POST['userpass'])) {
		$p = mysqli_real_escape_string ($dbc, $_POST['userpass']);
	} else {
		$errors[] = 'You forgot to enter your password!';
	}
	
	if (empty($errors)) { // No errors!
		
		// Query the database:
		$q = "SELECT userId, email, firstName, lastName FROM users WHERE (email='$u' AND userpass=SHA1('$p'))";		
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		
		if (@mysqli_num_rows($r) == 1) { // A match was made.
			
			// Store the data in the session:
			$_SESSION = mysqli_fetch_array ($r, MYSQLI_ASSOC); 
			
			// Perform clean up:
			mysqli_free_result($r);
			mysqli_close($dbc);
			
			// Return the status:
			echo 'VALID';
			
			// Quit the script:
			exit(); 
			
		}

	}// End of $errors IF.

	mysqli_close($dbc);
	
} // End of form submission check.

echo 'INVALID';