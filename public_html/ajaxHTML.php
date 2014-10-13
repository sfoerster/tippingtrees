
<?php

require('../includes/common/common.php');

//commonPHP::isloggedin()
if (true) {
    
    commonPHP::checkFormSubmission();

    
    // Open, valid session
    $s = $_SESSION['user'];
    $u = unserialize($s);
    
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
            /*
	    case "demo":
                $rsc = new rightSideClass();
                //$rsc->echoPreContent();
                $rsc->echoPreMainWrapper();
                commonHTML::echoDemo();
                //$rsc->echoPostContent();
                $rsc->echoPostMainWrapper();
                break;
            case "home":
                $hsc = new indexClass();
                //$hsc->echoWrapper();
                $hsc->echoMainWrapper();
                break;
            case "findpeople":
                commonHTML::echoFindPeople();
		//commonHTML::echoVouches();
                break;
            case "blogentry":
                if (commonPHP::checkisadmin()) {
                    $rsc = new rightSideClass();
                    $rsc->echoPreMainWrapper();
                    commonHTML::echoBlogEntry();
                    $rsc->echoPostMainWrapper();
                }
                break;
            //*/
	    case "searchPeople":
		$out = peopleClass::searchPeople($_POST['searchQuery']);
		echo json_encode($out);
	    break;
	    case "getVouches":
		$out = peopleClass::getVouches();
		echo json_encode($out);
	    break;
	    case 'displayTitleBar':
		$out = profileClass::displayTitleBar();
		echo json_encode($out);
	    break;
	    case 'displaySelfProfile':
		profileClass::displaySelfProfile();
	    break;
	    case 'profileUpdate':
		$out = profileClass::update($_POST['username'],
					    $_POST['first_name'],
					    $_POST['last_name'],
					    $_POST['changePassBox'],
					    $_POST['pass'],
					    $_POST['new_pass1'],
					    $_POST['new_pass2'],
					    $_POST['new_privkey']);
                echo json_encode($out);
	    break;
	    case 'getPMsgInbox':
		$out = pmessage::read();
		echo json_encode($out);
	    break;
	    case 'getPMsgSent':
		$out = pmessage::sent();
		echo json_encode($out);
	    break;
	    case 'getNotifications':
		$out = notification::pending();
		echo json_encode($out);
	    break;
	    case 'getUserInfo':
		$out = peopleClass::getUserInfo($_POST['user_ids']);
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
