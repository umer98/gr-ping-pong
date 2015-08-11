<?php //register.php

session_start();
//Redirect if not logged in
if (isset($_SESSION['userId'])) {
    header("Location: index.php");
}

// Always need the configuration file:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'Register';
include ('includes/header.html');

// Include the heading here:
echo '<h1>Register</h1><p id="message">Registered users can enter the result of games that they have played.</p>
';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
        
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
        
	// Validate the username:
	if (isset($_POST['username']) && !empty($_POST['username'])) {
                $u = mysqli_real_escape_string ($dbc, trim(strip_tags($_POST['username'])));
                $search = mysqli_query($dbc, "SELECT username FROM users WHERE username='".$u."'");
                if (@mysqli_num_rows($search) != 0) {
                    $errors[] = 'Username already exists!';
                }
	}  else {
		$errors[] = 'You forgot to enter your username!';
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
		$q = "INSERT INTO users (firstName, lastName, username, userpass, ratio, wins, losses) VALUES ('$fName', '$lName','$u', '$p', '$ratio', '$win', '$loss')";
		
                // Execute the query:
                if (@mysqli_query($dbc, $q)) {
                        print '<p class="good">You are now registered!</p>';
                        
                        // Clean up:
			mysqli_close($dbc);
                        
                        // Include the footer:
			include ('includes/footer.html');
			
			// Quit the script:
			exit(); 
                } else {
                        $errors[] = "Query: $q\n<br />MySQL Error: " . mysqli_error($dbc) ;
                }
        }
        
        // Close the database connection:
	mysqli_close($dbc);
        
}

// Display any errors:
if (isset($errors) && is_array($errors)) {
	echo '<h2>Error!</h2><p>The following error(s) occurred:<ul>';
	foreach ($errors as $error) {
		echo "<li class=\"error\">$error</li>";
	}
	echo '</ul></p>';
	
}

?>

<form action="register.php" method="post" id="registerForm">
    <label>First Name</label>
    <input name="firstname" id="firstName" type="text" required>
    <label>Last Name</label>
    <input name="lastname" id="lastName" type="text" required>
    <label>Username</label>
    <input name="username" id="username" type="text" required>
    <label>Password</label>
    <input name="userpass" id="userpass" type="password" required>
    <label>Confirm Password:</label>
    <input name="userpass2" id="userpass2" type="password" required>
    <br>
    <input class="button" type="submit" value="Register">
</form>

<script src="js/register.js"></script>

<?php include ('includes/footer.html'); ?>