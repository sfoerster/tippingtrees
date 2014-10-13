

<?php

class verification
{
	
    function send_ver_email($email) {
        
	$dbc = dbconnector::getConnection();
	
	$email = sanitize::db($email);
	
        $q = "SELECT username, first_name, last_name, token FROM users WHERE email='$email'";
        $r = @mysqli_query($dbc, $q);
        
        if (mysqli_num_rows($r) == 1) {
            
            // Fetch the record:
	    $row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
            
            $username = $row['username'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $token = $row['token'];
            $svr = $_SERVER['SERVER_NAME'];
            
            // compose verification token link
            $link = "https://$svr/verify.php?email=$email&token=$token";
            
            // compose email
            $subject = "Verify Your Account with Tipping Trees!";
            $body = "Thank you for registering with Tipping Trees!\n\nFirst name: '$first_name' \nLast name: '$last_name' \nUsername: '$username' \n\nYou're almost ready to begin. Please click the following link, or copy and paste into your browser: '$link'";
            $body = wordwrap($body, 70);
            
            mail($email, $subject, $body, "From:noreply@tippingtrees.com");
            
        } else {
            
            echo "Error: Verification email could not be sent.";
            
        }
	
	mysqli_close($dbc);
        
    } // end send_ver_email function
    
    
    

    public static function check_verification($email='',$token='')
    {
        // return list ($success,$data)
        
        $dbc = dbconnector::getConnection();
        
        $errors = array(); // Initialize error array.

	// Validate the email:
	if (empty($email)) {
		$errors[] = 'No email.';
	} else {
		$e = mysqli_real_escape_string($dbc, trim($email));
	}

	// Validate the token:
	if (empty($token)) {
		$errors[] = 'No verification token.';
	} else {
		$t = mysqli_real_escape_string($dbc, trim($token));
	}

	if (empty($errors)) { // If everything's OK.
		
		// Retrieve user's unique password salt
		$q1 = "SELECT * FROM users WHERE email='$e' AND token='$t'";
		$r1 = @mysqli_query($dbc, $q1);
		
		
		// Check the result:
		if (mysqli_num_rows($r1) == 1) {

		
			// Fetch the record:
			$row = mysqli_fetch_array ($r1, MYSQLI_ASSOC);
			
			// Insert verified value into table
			$user_id = $row['user_id'];
			$q2 = "UPDATE users SET verified=1 WHERE user_id='$user_id'";
			$r2 = @mysqli_query($dbc, $q2);
			
			if (mysqli_affected_rows($dbc) == 1) {
			    // no errors, update successful
			    
			    
			    // Return true and the record:
			    return array(true, $row); // true will cause verify.php to login to the account.
			    // Must only happen once, when the account becomes verified the first time.
			    
			} elseif ($row['verified'] == 1) {
			    
			    $errors[] = 'This account has already been verified.';

			} else {
			    $errors[] = 'Account could not be verified due to a system error.';
			}
			
	
			
			
		} else { // Not a match!
			$errors[] = 'The email and token entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	mysqli_close($dbc);
	
	// Return false and the errors:
	return array(false, $errors);

    }


}



?>
