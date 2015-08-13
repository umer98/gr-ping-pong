<?php # register.php
// This page handles the login form via Ajax!

// Always need the configuration file:
require('../includes/config.inc.php');

// Need to start the session:
session_start();

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Array for storing errors:
	$errors = array();
	
        // Validate the first name:
	if (isset($_POST['firstname']) && !empty($_POST['firstname'])) {
		$fName = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['firstname'])));
	} else {
		$errors[] = 'You forgot to enter your first name!';
	}
        
        // Validate the last name:
        if (isset($_POST['lastname']) && !empty($_POST['lastname'])) {
                $lName = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['lastname'])));
        } else {
                $errors[] = 'You forgot to enter your last name!';
        }
        
	// Validate the email:
	if (isset($_POST['email']) && !empty($_POST['email'])) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $u = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['email'])));
                $search = mysqli_query($dbc, "SELECT email FROM users WHERE email='".$u."'");
                if (@mysqli_num_rows($search) != 0) {
                    $errors[] = 'An account with that email already exists!';

                    //Return the status
                    echo 'EXISTS';

                    // Quit the script:
                    exit();
                }
            } else {
                $errors[] = 'The email entered is not valid!';
            }
	}  else {
		$errors[] = 'You forgot to enter your email!';
	}
	
        // Validate the password:
	if (isset($_POST['userpass']) && !empty($_POST['userpass'])) {
		// Validate the confirm password:
                if (isset($_POST['userpass2']) && ($_POST['userpass'] == $_POST['userpass2'])) {
                        $p = mysqli_real_escape_string ($dbc, sha1(trim(strip_tags($_POST['userpass']))));
                } else {
                        $errors[] = 'Your password did not match your confirmed password!';
                }
	} else {
		$errors[] = 'You forgot to enter your password!';
        }
	
	if (empty($errors)) { // No errors!
                $ratio = 0;
                $win = 0;
                $loss = 0;
		// Define the Query:
		$q = "INSERT INTO users (firstName, lastName, email, userpass, ratio, wins, losses) VALUES ('$fName', '$lName','$u', '$p', '$ratio', '$win', '$loss')";
		
                // Execute the query:
                if (@mysqli_query($dbc, $q)) {
			// Clean up:
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