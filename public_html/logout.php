<?php # Script 12.11 - logout.php #2
// This page lets the user logout.
// This version uses sessions.

require('../includes/common/common.php');
//session_start(); // Access the existing session.

// If no session variable exists, redirect the user:
if (!isset($_SESSION['agent'])) {

	// Need the functions:
	//require ('includes/login_functions.inc.php');
	redirect_user('index.php');	
	
} else { // Cancel the session:

	$_SESSION = array(); // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.
	
	setcookie ('mykeyn', '', time()-3600, '/', '', 0, 0);
	setcookie ('mykeye', '', time()-3600, '/', '', 0, 0);
	setcookie ('encpass', '', time()-3600, '/', '', 0, 0);
	setcookie ('encmykeyd', '', time()-3600, '/', '', 0, 0);

}

redirect_user('index.php');
exit();

// ------------------- WILL NEVER REACH AFTER THIS POINT -------------------------

// Set the page title and include the HTML header:
$page_title = 'Logged Out of Tipping Trees!';
$import_WDC = 1;
include ('../includes/scripts/header.php');

?>


<script type="text/javascript">

	window.onload(function () {
		
		
		ProcessCookie('erase','mykeyd');
		PrcoessCookie('erase','mykeyp');
		ProcessCookie('erase','mykeyq');
		
		
	});
	
	
</script>


<?php

// Print a customized message:
echo "<h1>Logged Out!</h1>
<p>You are now logged out!</p>";

include ('../includes/scripts/footer.php');
?>