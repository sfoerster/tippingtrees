
<?php

require('../includes/common/common.php');

//commonPHP::isloggedin()
if (true) {
    
    //commonPHP::checkFormSubmission();

    
    // Open, valid session
    //$s = $_SESSION['user'];
    //$u = unserialize($s);
    
    $errors = array();

    // Check for a page:
    if (empty($_POST['page'])) {
            $errors[] = 'You forgot to enter the page.';
    } else {
            $page = sanitize::db($_POST['page']);
    }

    // end error check
    
    if (empty($errors)) { 
        
        switch ($page) {
	    case 'getUserInfoFromEmail':
                $out = peopleClass::getUserDataFromEmail($_POST['email']);
		echo json_encode($out);
            break;
	    case 'getdbChat':
		$out = dbglass::getChats();
		echo json_encode($out);
	    break;
	    case 'getRoadmap':
		$out = Branch::getRoadmap();
		echo json_encode($out);
	    break;
	    case 'getHomeContent':
		$out = Home::getHomeContent();
		echo json_encode($out);
	    break;
	    case 'getPersonalKeys':
		$out = profileClass::getPersonalKeys();
		echo json_encode($out);
	    break;
	    case 'deleteAcct':
		$out = peopleClass::sendDeleteRequest($_POST['email'],
                                                      $_POST['captcha'],
						      $_POST['token']);
		echo json_encode($out);
	    break;
	    case 'regInvite':
		$out = registerClass::inviteRequest($_POST['email']);
		echo json_encode($out);
	    break;
            default:
                // do nothing
                //echo "error";
            break;
        }
	
        
	//echo json_encode($out); // send this back to the receiving script
	//echo $out;
        
        
        
    } else { // send errors back to HTML form
        
        /*foreach ($errors as $msg) { // Print each error.
	    echo " - $msg\n";
	}*/
        
        //logger::ajaxErrors($errors);
        
    }
    
    
}






?>
