<?php // Script 9.8 - logout.php
/* This is the logout page. It destroys the session information. */

session_start();
//Redirect if not logged in
if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
}

// Always need the configuration file:
require('includes/config.inc.php');

// Set the page title and include the HTML header:
$page_title = 'Logout';
include ('includes/header.html');

// Reset the session array:
$_SESSION = array();

// Destroy the session data on the server:
session_destroy();

?>
<p>You are now logged out.</p>
<p>Have an awesome day.</p>

<?php include('includes/footer.html'); ?>