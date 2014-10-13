<?php # Script 9.5 - register.php #2
// This script performs an INSERT query to add a record to the users table.

require('../includes/common/common.php');
//session_start(); // Start the session.

//$page_title = 'Register for Tipping Trees';
//include ('../includes/scripts/header.php');

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//require ('mysqli_connect.php'); // Connect to the db.
	
	$dbc = dbconnector::getConnection();
		
	$errors = array(); // Initialize an error array.
	
	if (isset($_SESSION['agent']) && ($_SESSION['agent'] == HASH("sha512",$_SERVER['HTTP_USER_AGENT'])) ) {

		$errors[] = "You are already logged in as {$_SESSION['first_name']} {$_SESSION['last_name']}, ({$_SESSION['username']}).
		You cannot register while logged in.";

	}
	
	// Check for a first name:
	if (empty($_POST['first_name'])) {
		$errors[] = 'You forgot to enter your first name.';
	} else {
		$fn = sanitize::general($_POST['first_name']);
		if ($fn != $_POST['first_name']) {
			$errors[] = 'Invalid first name given.';
		}
	}
	
	// Check for a last name:
	if (empty($_POST['last_name'])) {
		$errors[] = 'You forgot to enter your last name.';
	} else {
		$ln = sanitize::general($_POST['last_name']);
		if ($ln != $_POST['last_name']) {
			$errors[] = 'Invalid last name given.';
		}
	}
	
	
	// Check for an user name:
	if (empty($_POST['username'])) {
		$errors[] = 'You forgot to enter your username.';
	} else {
		$u = sanitize::general( $_POST['username']);
		if ($u != $_POST['username']) {
			$errors[] = 'Invalid username given.';
		}
	}
	
	// Check for an email:
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email.';
	} else {
		$e = sanitize::general( $_POST['email']);
		if ($e != $_POST['email']) {
			$errors[] = 'Invalid email given.';
		}
	}
	
	// Check for token:
	if (empty($_POST['token'])) {
		$errors[] = 'Token is missing.';
	} else {
		$t = sanitize::general( $_POST['token']);
		if ($t != $_POST['token']) {
			$errors[] = 'Invalid token given.';
		}
	}
	
	// Check for a password and match against the confirmed password:
	if (!empty($_POST['pass'])) {
		//if ($_POST['pass1'] != $_POST['pass2']) {
		//	$errors[] = 'Your password did not match the confirmed password.';
		//} else {
		
			// don't sanitize passwords
			$p = mysqli_real_escape_string($dbc, trim($_POST['pass']));
		//}
	} else {
		$errors[] = 'You forgot to enter your password.';
	}
	
	
	if (!empty($_POST['pubkey'])) {
		
		$pubkey = $_POST['pubkey'];
		
		$pubkeydb = keyToDBsafe($pubkey);
		$pubkeydb = mysqli_real_escape_string($dbc,$pubkeydb);
		$pubkeyss = keyFromDBsafe($pubkeydb);
		
		
		if ($pubkey != $pubkeyss) {
			// log the error
			$errors[] = 'Public key is not db ready.';
		}
		
	} else {
		$errors[] = 'There is no public key.';
	}
	
	if (!empty($_POST['privkey'])) {
		
		$privkey = $_POST['privkey'];
		
		$privkeydb = keyToDBsafe($privkey);
		$privkeydb = mysqli_real_escape_string($dbc,$privkeydb);
		$privkeyss = keyFromDBsafe($privkeydb);
		
		if ($privkey != $privkeyss) {
			// log the error
			$errors[] = 'Private key is not db ready.';
		}

		
	} else {
		$errors[] = 'There is no private key.';
	}
	
	
	if (empty($errors)) {
		
		// Ensure that the username is unique
		$q = "SELECT user_id FROM users WHERE username='$u'";
		$r = @mysqli_query($dbc, $q);
		
		if (mysqli_num_rows($r) > 0) {
			
			$errors[] = 'Username is not available. Please choose another.';
		}
		
		
		// Ensure that the email is unique
		$q = "SELECT user_id FROM users WHERE email = '$e'";
		$r = @mysqli_query($dbc, $q);
		
		if (mysqli_num_rows($r) > 0) {
			$errors[] = 'This email is already registered.';
		}
	}
	
	if (empty($errors)) {
		
		// check that the token in the invitation database matches
		
		$q = "SELECT * FROM invitations WHERE email='$e' AND token='$t'";
		$r = @mysqli_query($dbc,$q);
		
		if (mysqli_num_rows($r) == 0) {
			$errors[] = 'Token does not match email. Email: ' . $e . ' token: ' . $t;
		} else {
			$row = mysqli_fetch_assoc($r);
			$inviter_id = $row['inviter_id'];
			$inviter = new user($inviter_id);
			$inviter_email = $inviter->email;
		}
	}
	
	if (empty($errors)) { // If everything's OK.
		
		
		
	
		// Register the user in the database...
		
		// Make the query:
		$salt = HASH("sha512",openssl_random_pseudo_bytes(512));
		$ps = $p . $salt;

		$token = HASH("sha512",openssl_random_pseudo_bytes(512));
		$ps = HASH("sha512",$ps);
		$q = "INSERT INTO users (first_name, last_name, username, email, pass, salt, active, tos_agreement, token, verified, registration_time) VALUES ('$fn', '$ln', '$u', '$e', '$ps', '$salt', 1, 1, '$t', 1, UTC_TIMESTAMP() )";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		if ($r) { // If it ran OK.
		
			
			// Verification
			//require('./includes/verification_functions.php');
			
			$q2 = "SELECT * FROM users WHERE email='$e'";
			$r2 = mysqli_query($dbc,$q2);
			$row = mysqli_fetch_assoc($r2);
			
			$user_id = $row['user_id'];
			
			$dbcc = dbconnector::getCryptoConnection();
			$q3 = "INSERT INTO RSAkeys (user_id, privkey, pubkey, start_time, revoked) VALUES ('$user_id','$privkeydb','$pubkeydb',UTC_TIMESTAMP(),0)";
			$r3 = mysqli_query($dbcc,$q3);
			
			if ($r3) {
				// success
			} else {
				// log failure
			}
			
			// delete invitation table entry
			$q4 = "DELETE FROM invitations WHERE email='$e' AND token='$t' LIMIT 1";
			$r4 = mysqli_query($dbc,$q4);
			
			//RSA::setCookie($u->user_id);
			
			mysqli_close($dbcc);
			mysqli_close($dbc);
			
			// log the user in
			list ($check, $data) = loggingin::check_login($e, $p);
			
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
				//$adr = "index.php#people-peoplePublic-" . urlencode(json_encode(array('email' => $_POST['inviter_email'])));
				$adr = "index.php#people-peoplePublic-" . urlencode(json_encode(array('email' => $inviter_email)));
				redirect_user($adr);
				//redirect_user('index.php');
					
			}
		
		} else { // If it did not run OK.
			
			// Public message:
			echo '<h1>System Error</h1>
			<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 
			
			// Debugging message:
			echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
						
		} // End of if ($r) IF.
		
		//mysqli_close($dbc); // Close the database connection.

		// Include the footer and quit the script:
		//include ('../includes/scripts/footer.php'); 
		exit();
		
	} else { // Report the errors.
	
		//echo '<h1>Error!</h1>
		//<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			//echo " - $msg<br />\n";
		}
		//echo '</p><p>Please try again.</p><p><br /></p>';
		
	} // End of if (empty($errors)) IF.
	
	mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.

redirect_user('index.php');
?>
