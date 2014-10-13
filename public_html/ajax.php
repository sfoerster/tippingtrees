<?php

/* Database Configuration. Add your details below */

/*
 
 $dbOptions = array(
	'db_host' => '',
	'db_user' => '',
	'db_pass' => '',
	'db_name' => ''
);

// Database Config End 


error_reporting(E_ALL ^ E_NOTICE);

require "classes/DB.class.php";
require "classes/Chat.class.php";
require "classes/ChatBase.class.php";
require "classes/ChatLine.class.php";
require "classes/ChatUser.class.php";

session_name('webchat');
session_start();
//*/

require('../includes/common/common.php');

// ----------------- CHECK FORM SUBMISSION --------------------
//commonPHP::checkFormSubmission();
// ----------------- END CHECK --------------------------------



if(get_magic_quotes_gpc()){
	
	// If magic quotes is enabled, strip the extra slashes
	array_walk_recursive($_GET,create_function('&$v,$k','$v = stripslashes($v);'));
	array_walk_recursive($_POST,create_function('&$v,$k','$v = stripslashes($v);'));
}

try{
	
	// Connecting to the database
	//DB::init($dbOptions);
	
	// get user info from the open, valid session
	$s = $_SESSION['user'];
	$u = unserialize($s);
	
	$response = array();
	
	// Handling the supported actions:
	
	switch($_GET['action']){
		
		// --------------- CHAT FUNCTIONS ---------------------------------
		
		case 'login':
			//$response = Chat::login($_POST['name'],$_POST['email']);
			//$response = Chat::login($u->username,$u->email,$_POST['msgchat_token']);
			
			$response = Chat::login($_POST['user_id'],
                                                $_POST['enc_mode'],
						$_POST['enc_user_id'],
						$_POST['chat_token'],
						$_POST['enc_chat_token'],
						$_POST['enc_chat_key'],
                                                $_POST['enc_chat_name'],
                                                $_POST['chat_owner'],
                                                $_POST['enc_chat_owner'],
                                                $_POST['enc_chat_invite'],
                                                $_POST['owner_signature'],
                                                $_POST['enc_isactive']);
		break;
            
                case 'addUserToGroup':
                    
                        $response = Chat::addUserToGroup($_POST['user_id'],
                                                         $_POST['enc_mode'],
                                                         $_POST['enc_user_id'],
                                                         $_POST['old_enc_user_id'],
                                                         $_POST['chat_token'],
                                                         $_POST['enc_chat_token'],
                                                         $_POST['enc_chat_key'],
                                                         $_POST['enc_isactive']);
                    
                break;
		
		case 'checkLogged':
			$response = Chat::checkLogged();
		break;
		
		case 'logout':
			$response = Chat::logout($_POST['user_id'],
						 $_POST['enc_chat_token'],
						 $_POST['chat_token'],
						 $_POST['enc_user_id'],
                                                 $_POST['enc_isactive']);
		break;
		
		case 'submitChat':
			$response = Chat::submitChat($_POST['chat_token'],
						     $_POST['chatline_token'],
                                                     $_POST['enc_mode'],
						     $_POST['enc_chat_msg'],
						     $_POST['enc_sender_id'],
						     $_POST['enc_signature']);
		break;
	
		case 'getKeys':
			$response = Chat::getKeys();
		break;
		
		case 'getUsers':
			$response = Chat::getUsers($_POST['chat_tokens']);
		break;
		
		case 'getUserInfo':
			$response = Chat::getUserInfo($_POST['user_ids']);
		break;
            
                case 'getOneUserData':
                        $response = peopleClass::getOneUserData($_POST['user_id']);
                break;
		
		case 'getChats':
			$response = Chat::getChats($_POST['chat_tokens']);
						   //$_POST['lastID']);
		break;
	
		// -------------- GROUPS FUNCTIONS -------------------------
	
		case 'vouche':
			//$response = Chat::login($_POST['name'],$_POST['email']);
			//$response = Chat::login($u->username,$u->email,$_POST['msgchat_token']);
			
			$response = peopleClass::vouche($_POST['user_id'],
						$_POST['signature'],
						$_POST['type']);
		break;
	
		case 'unvouche':
			$response = peopleClass::unvouche($_POST['user_id']); // not used currently
		break;
            
                case 'block':
                        $response = peopleClass::block($_POST['user_id']);
                break;
                case 'unblock':
                        $response = peopleClass::unblock($_POST['user_id']);
                break;
            
                case 'invite':
                        $response = peopleClass::sendInvitation($_POST['email'],
                                                                $_POST['msg'],
                                                                $_POST['inviter_id']);
                break;
            
	
		/*case 'getVouchesMade':
			$response = Groups::getVouchesMade();
		break;
	
		case 'getVouchesReceived':
			$response = Groups::getVouchesReceived();
		break;*/
            
                case 'sendPMsg':
                        $response = pmessage::sendPMsg($_POST['rec_id'],
                                                       $_POST['enc_key'],
                                                       $_POST['self_id'],
                                                       $_POST['self_enc_key'],
                                                       $_POST['mode'],
                                                       $_POST['enc_subject_i'],
                                                       $_POST['enc_body_i'],
                                                       $_POST['enc_sender_i'],
                                                       $_POST['enc_signature_i'],
                                                       $_POST['enc_subject_s'],
                                                       $_POST['enc_body_s'],
                                                       $_POST['enc_receiver_s'],
                                                       $_POST['enc_signature_s'],
                                                       $_POST['sendEmail']);    
                break;
                case 'deletePMsg':
                    $response = pmessage::deletePMsg($_POST['type'],
                                                     $_POST['msg_id']);
                break;
            
                case 'sendNotification':
                    $response = notification::sendNotification($_POST['notification']);    
                        
                break;
            
            // -----------------------------------------------------------------
            
                case 'deleteAcct':
                    $response = peopleClass::resetAccount($_POST['email'],$_POST['token']);
                break;
		
		default:
			throw new Exception('Wrong action');
	}
	
	echo json_encode($response);
}
catch(Exception $e){
	die(json_encode(array('error' => $e->getMessage())));
}

?>