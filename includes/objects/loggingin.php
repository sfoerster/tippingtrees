

<?php

class loggingin
{

    public static function check_login($email = '', $pass = '')
    {
	// return list ($success, $data/$errors)
	
        $dbc = dbconnector::getConnection();

	$errors = array(); // Initialize error array.

	// Validate the email address:
	if (empty($email)) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
		$e = mysqli_real_escape_string($dbc, trim($email));
	}

	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'You forgot to enter your password.';
	} else {
		$p = mysqli_real_escape_string($dbc, trim($pass));
	}

	if (empty($errors)) { // If everything's OK.
		
		// Retrieve user's unique password salt
		$q1 = "SELECT salt FROM users WHERE email='$e'";
		$r1 = @mysqli_query($dbc, $q1);
		$salt = mysqli_fetch_array($r1,MYSQLI_ASSOC);
		$s = $salt['salt'];

		// Retrieve the user_id and first_name for that email/password combination:
		//echo "password = $p\n";
		//echo "salt     = $s\n";
		$ps = $p . $s;
		//echo "concat pw = $ps\n";
		//$ps = SHA1($ps);
		//$pws = SHA1($ps);
		//echo "salted hash = $ps";
		$ps = HASH("sha512",$ps);
		$q2 = "SELECT user_id, username, email, first_name, last_name, fin_id, verified, active FROM users WHERE email='$e' AND pass='$ps'"; // AND pass=SHA1('$p')";
		//echo $q2;
		$r2 = @mysqli_query ($dbc, $q2); // Run the query.
		
		//echo "Query result = " . $r2;
		
		/*if (mysqli_num_rows($r2) == 0) {
			echo "No records returned.";
		} else {
			echo "Number of records: " . mysqli_num_rows($r2);
			$tmp2 = mysqli_fetch_array($r2,MYSQLI_ASSOC);
			//echo "user_id = $tmp2['user_id']";
			//echo "first_name = $tmp2['first_name']";
		}*/
		
		// Check the result:
		if (mysqli_num_rows($r2) == 1) {

			// Fetch the record:
			$row = mysqli_fetch_array ($r2, MYSQLI_ASSOC);
			
			if ($row['verified'] != 1) { // account is verified
				
				//echo "Name = " . $row['first_name'];
				//$errors[] = "I'm still getting an error";
		
				// account is not verified, cannot login
				$errors[] = 'Your account is not verified. Check your email for a link to verify your account.';
			}
			
			if ($row['active'] != 1) {
				$errors[] = 'Your account is not active.';
			}
			
			if (empty($errors)) {
				return array(true, $row);
			} else {
				return array(false,$errors);
			}

			
		} else { // Not a match!
			$errors[] = 'The email address and password entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

    } // End of check_login() function.


}


?>

