<?php # Script 12.8 - login.php #3
// This page processes the login form submission.
// The script now uses sessions.

require('../includes/common/common.php');

//commonPHP::checkFormSubmission();

// Check if the form has been submitted:
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Need two helper files:
	//require ('mysqli_connect.php');
		
	// Check the login:
	list ($check, $data) = loggingin::check_login($_POST['email'], $_POST['pass']);
	
	if ($check) { // OK!
		
		// Set the session data:
                ini_set('session.use_only_cookies',true);
                ini_set('session.use_trans_sid',false);
		session_start();

                
                $u = new user($data['user_id']);
                $_SESSION['user'] = serialize($u);
                //$_SESSION['user'] = $data['user_id'];
                
                //RSA::setCookie($u->user_id);
                
                $_SESSION['agent'] = HASH("sha512",$_SERVER['HTTP_USER_AGENT']);
                $_SESSION['sesskey'] = $_POST['sesskey'];
		
		// Redirect:
		redirect_user('index.php');
			
	} else { // Unsuccessful!

		// Assign $data to $errors for login_page.inc.php:
		$errors = $data;

	}
		
	//mysqli_close($dbc); // Close the database connection.
        
// End of the main submit conditional.
//}

// Create the page:
//include ('../includes/scripts/login_page.inc.php');
redirect_user();
?>