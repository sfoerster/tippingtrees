<?php

/* The Chat class exposes public static methods, used by ajax.php */

require('../includes/common/common.php');

class Chat{
	
	public static function login($user_id,
				     $enc_mode,
				     $enc_user_id,
				     $chat_token,
				     $enc_chat_token,
				     $enc_chat_key,
				     $enc_chat_name,
				     $chat_owner,
				     $enc_chat_owner,
				     $enc_chat_invite,
				     $owner_signature,
				     $enc_isactive){
		
		if (!commonPHP::isloggedin()) {
			exit();
		}
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		$name = $u->username;
		$email = $u->email;
		$gravatar = Chat::gravatarFromEmail($email);
		
		// check to see how many groups are currently owned by the user
		$dbcm = dbconnector::getMsgConnection();
		$uid = $u->user_id;
		$q = "SELECT * FROM chatinfo WHERE chat_owner='$uid'";
		$r = mysqli_query($dbcm,$q);
		if ($r) {
			$numrows = mysqli_num_rows($r);
			$maxrows = ConfigSettings::maxGroups();
			if ($numrows >= $maxrows) {
				
				mysqli_close($dbcm);
				
				return array('status' => 0,
					     'error' => 'You can only own ' . $maxrows . ' groups.');
			}
		}
		
		mysqli_close($dbcm);
		
		
		/*if(!$name || !$email){
			throw new Exception('Fill in all the required fields.');
		}
		
		if(!filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL)){
			throw new Exception('Your email is invalid.');
		}*/
		
		// Preparing the gravatar hash:
		//$gravatar = sha1(strtolower(trim($email)));
		
		
		$cname = new ChatInfo(array(
			'chat_token'		=> $chat_token,
			'enc_mode'		=> $enc_mode,
			'chat_owner'	        => $chat_owner,
			'enc_chat_owner'	=> $enc_chat_owner,
			'enc_chat_name'		=> $enc_chat_name,
			'enc_chat_invite'	=> $enc_chat_invite,
			'owner_signature'	=> HASH("sha512",$owner_signature)
		));
		
		$cname->save();
		
		/*
		$user = new ChatUser(array(
			'name'		=> $name,
			'gravatar'	=> $gravatar
		));
		//*/
		
		$cuser = new ChatUser(array(
			'user_id' 		=> $user_id,
			'enc_mode'		=> $enc_mode,
			'enc_chat_token'	=> $enc_chat_token,
			'enc_chat_key'		=> $enc_chat_key
		));
		
		$cuser->save();
		
		// The save method returns a MySQLi object
		/*if($cuser->save()->affected_rows != 1){
			//throw new Exception('This nick is in use.');
		}*/
		
		$ctoken = new ChatToken(array(
			'chat_token'		=> $chat_token,
			'enc_mode'		=> $enc_mode,
			'enc_user_id'		=> $enc_user_id,
			'enc_isactive'		=> $enc_isactive
		));
		
		$ctoken->save();
		
		/*
		$_SESSION['user']	= array(
			'name'		=> $name,
			'gravatar'	=> $gravatar
		);
		//*/
		
		return array(
			'status'	=> 1,
			'name'		=> $name,
			'gravatar'	=> $gravatar //Chat::gravatarFromHash($gravatar)
		);
	}
	
	public static function addUserToGroup($user_id,
					      $enc_mode,
					      $enc_user_id,
					      $old_enc_user_id,
					      $chat_token,
					      $enc_chat_token,
					      $enc_chat_key,
					      $enc_isactive) {
		
		if (!commonPHP::isloggedin()) {
			exit();
		}
		
		if (peopleClass::isblocked($user_id))
		{
			exit();
		}
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		$name = $u->username;
		$email = $u->email;
		
		$gravatar = Chat::gravatarFromEmail($email);
		
		$cuser = new ChatUser(array(
			'user_id' 		=> $user_id,
			'enc_mode'		=> $enc_mode,
			'enc_chat_token'	=> $enc_chat_token,
			'enc_chat_key'		=> $enc_chat_key
		));
		
		$cuser->save();
		
		if ($old_enc_user_id != '') {
			$clean_old_enc_user_id = sanitize::db($old_enc_user_id);
			$clean_chat_token = sanitize::db($chat_token);
			
			$dbcm = dbconnector::getMsgConnection();
			
			$qoui = "DELETE FROM chattoken WHERE chat_token='$clean_chat_token' AND enc_user_id='$clean_old_enc_user_id'";
			$roui = mysqli_query($dbcm,$qoui);
			
			mysqli_close($dbcm);
		}
		
		$ctoken = new ChatToken(array(
			'chat_token'		=> $chat_token,
			'enc_mode'		=> $enc_mode,
			'enc_user_id'		=> $enc_user_id,
			'enc_isactive'		=> $enc_isactive
		));
		
		$ctoken->save();
		
		return array(
			'status'	=> 1,
			'name'		=> $name,
			'gravatar'	=> $gravatar //Chat::gravatarFromHash($gravatar)
		);
		
	}
	
	public static function checkLogged(){
		$response = array('logged' => false);
			
		if($_SESSION['user']['name']){
			$response['logged'] = true;
			$response['loggedAs'] = array(
				'name'		=> $_SESSION['user']['name'],
				'gravatar'	=> Chat::gravatarFromHash($_SESSION['user']['gravatar'])
			);
		}
		
		return $response;
	}
	
	public static function logout($user_id,$enc_chat_token,$chat_token,$enc_user_id,$enc_isactive){
		
		if (!commonPHP::isloggedin()) {
			exit();
		}
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		if ($user_id != $u->user_id) {
			exit();
		}
		
		//DB::query("DELETE FROM webchat_users WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
		
		//$_SESSION = array();
		//unset($_SESSION);
		$clean_enc_isactive   = sanitize::db($enc_isactive);
		$clean_chat_token     = sanitize::db($chat_token);
		$clean_enc_user_id    = sanitize::db($enc_user_id);
		$clean_enc_chat_token = sanitize::db($enc_chat_token);
		
		$dbcm = dbconnector::getMsgConnection();
		
		// find chat owner
		$qo = "SELECT chat_owner FROM chatinfo WHERE chat_token='$clean_chat_token'";
		$ro = mysqli_query($dbcm,$qo);
		
		if ($ro) {
			$row = mysqli_fetch_assoc($ro);
			$chat_owner = $row['chat_owner'];
		
		
			if ($user_id != $chat_owner) {
			
				// not chat owner
				
				// chattoken
				$q = "UPDATE chattoken SET enc_isactive='$enc_isactive' WHERE chat_token='$clean_chat_token' AND enc_user_id='$clean_enc_user_id'";
				$r = mysqli_query($dbcm,$q);
				
				
			} else {
				
				// chat owner
				
				// chatline
				$q3 = "DELETE FROM chatline WHERE chat_token='$clean_chat_token'";
				$r3 = mysqli_query($dbcm,$q3);
				
				// chattoken
				$q4 = "DELETE FROM chattoken WHERE chat_token='$clean_chat_token'";
				$r4 = mysqli_query($dbcm,$q4);
				
				// chatinfo
				$q5 = "DELETE FROM chatinfo WHERE chat_token='$clean_chat_token'";
				$r5 = mysqli_query($dbcm,$q5);
				
				
			}
			
			// chatuser
			$q2 = "DELETE FROM chatuser WHERE recipient_id='$user_id' AND enc_chat_token='$clean_enc_chat_token' LIMIT 1";
			$r2 = mysqli_query($dbcm,$q2);
			
			// chatuser will still have the encrypted keys for all other users
		
		}
		mysqli_close($dbcm);

		return array('status' => 1);
	}
	
	public static function submitChat($chat_token,$chatline_token,$enc_mode,$enc_chat_msg,$enc_sender_id,$enc_signature){
		//if(!$_SESSION['user']){
		if (!commonPHP::isloggedin()) {
			throw new Exception('You are not logged in');
		}
		
		/*if(!$chatText){
			throw new Exception('You haven\' entered a chat message.');
		}*/
		
		// check that chat_token still exists in chat_info before inserting chat_line
		$dbcm = dbconnector::getMsgConnection();
		
		$clean_chat_token = sanitize::db($chat_token);
		$qc = "SELECT * FROM chatinfo WHERE chat_token='$clean_chat_token'";
		
		$insertID = '';
		if($rc = mysqli_query($dbcm,$qc)) {
			
			$row_cnt = mysqli_num_rows($rc);
			
			if ($row_cnt < 1) {
				
				mysqli_close($dbcm);
				return array('status' => 0,
					     'error' => 'group does not exist');
			}
			
			$chat = new ChatLine(array(
				'chat_token'		=> $chat_token,
				'chatline_token'	=> $chatline_token,
				'enc_mode'		=> $enc_mode,
				'enc_chat_msg'		=> $enc_chat_msg,
				'enc_sender_id'		=> $enc_sender_id,
				'enc_signature'		=> $enc_signature
			));
		
			// The save method returns a MySQLi object
			$insertID = $chat->save();
			
			//$dbcm = dbconnector::getMsgConnection();
			$clean_chat_token = sanitize::db($chat_token);
			$clean_chatline_token = sanitize::db($chatline_token);
			$q = "SELECT post_time FROM chatline WHERE chat_token='$clean_chat_token' AND chatline_token='$clean_chatline_token'";
			$r = mysqli_query($dbcm,$q);
			$row = mysqli_fetch_assoc($r);
			$post_time = $row['post_time'];
			$time = array('hours'	=> gmdate('H',strtotime($row['post_time'])),
				      'minutes'	=> gmdate('i',strtotime($row['post_time']))
				);
			
			//mysqli_close($dbcm);
			
			DBmaintenance::maxChatLines($chat_token);
			
		}
		
		
		mysqli_close($dbcm);
	

	
		// after submission and deletions, return chat_id of chat just inserted
		return array(
			'status'	=> 1,
			'insertID'	=> $insertID,
			'error'		=> '',
			'time'		=> $time,
			'post_time' 	=> $post_time
		);
	}
	
	public static function getKeys() {
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		// get all user keys, tokens
		$dbcm = dbconnector::getMsgConnection();
		$uid = sanitize::db($u->user_id);
		$q = "SELECT * FROM chatuser WHERE recipient_id='$uid' ORDER BY chatuser_id ASC";
		$result = mysqli_query($dbcm,$q);
		
		$ckeys = array();
		if ($result) {
			//echo "Success";
			
			
			while ($ckey = mysqli_fetch_assoc($result)) {
			//while($key = $result->fetch_object()){
				
				$ckeys[] = $ckey;
			}
			
		} else {
			//echo '<p>' . mysqli_error($dbcm) . '<br /><br />Query: ' . $q . '</p>';
			
			//logger::
		}
		
		
		
		mysqli_close($dbcm);
	
		return array('keys' => $ckeys,
			     'name' => $u->username,
			     'gravatar' => Chat::gravatarFromHash(Chat::gravatarFromEmail($u->email),30));
	}
	
	public static function getUsers($chat_tokens){
		
		
		
		// updates last_activity time
		/*if($_SESSION['user']['name']){
			$user = new ChatUser(array('name' => $_SESSION['user']['name']));
			$user->update();
		}*/
		
		// Deleting chats older than 5 minutes and users inactive for 30 seconds
		//DB::query("DELETE FROM webchat_lines WHERE ts < SUBTIME(NOW(),'0:5:0')");
		//DB::query("DELETE FROM webchat_users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");
		
		
		
		//$result = DB::query('SELECT * FROM webchat_users ORDER BY name ASC LIMIT 18');
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		$dbcm = dbconnector::getMsgConnection();
		
		$chat_token_array = json_decode($chat_tokens,true);
		
		$users = array();
		$emptychattokens = array();
		foreach($chat_token_array['chat_tokens'] as $chat_token) {
			
			$clean_chat_token = sanitize::db($chat_token);
		
			// get all user keys, tokens
			
			$q = "SELECT * FROM chattoken WHERE chat_token='$clean_chat_token'";
			$result = mysqli_query($dbcm,$q);
			
			if (mysqli_num_rows($result) == 0) {
				$emptychattokens[] = $clean_chat_token;
			} else {
			
			
			//if ($result) {
				//echo "Success";
				
				$n=0;
				while ($user = mysqli_fetch_assoc($result)) {
				//while($key = $result->fetch_object()){
					
					$users[$chat_token][$n] = $user;
					$n=$n+1;
				}
				
			//} else {
				//echo '<p>' . mysqli_error($dbcm) . '<br /><br />Query: ' . $q . '</p>';
				
				//logger::
			}
			
		}
		

		
		
		
		mysqli_close($dbcm);
	
		return array('users' => $users,
			     'emptychattokens' => $emptychattokens);
	

		
		/*
		$users = array();
		while($user = $result->fetch_object()){
			$user->gravatar = Chat::gravatarFromHash($user->gravatar,30);
			$users[] = $user;
		}
	
		return array(
			'users' => $users,
			'total' => DB::query('SELECT COUNT(*) as cnt FROM webchat_users')->fetch_object()->cnt
		);
		//*/
	}
	
	public static function getUserInfo($user_ids) {
		
		$user_id_array = json_decode($user_ids,true);
		//echo $user_id_array['user_ids'][0];
		
		$dbc = dbconnector::getConnection();
		$dbcc = dbconnector::getCryptoConnection();
		
		//echo "test getUserInfo\n";
		
		// clean bad entries in chattoken
		foreach($user_id_array['user_mapping'] as $chat_token => $maps) {
			foreach($maps as $map) {
				
				$user_id = sanitize::db($map['user_id']);
				$enc_user_id = sanitize::db($map['enc_user_id']);
				
				// check if user exists
				$q = "SELECT * FROM users WHERE user_id='$user_id'";
				$r = mysqli_query($dbc,$q);
				if (mysqli_num_rows($r) == 0) {
					// delete entry in chattoken
					$dbcm = dbconnector::getMsgConnection();
					$clean_chat_token = sanitize::db($chat_token);
					$q = "DELETE FROM chattoken WHERE chat_token='$clean_chat_token' AND enc_user_id='$enc_user_id' LIMIT 1";
					$r = mysqli_query($dbcm,$q);
					mysqli_close($dbcm);
				}
			}	
		}
		
		$userinfo = array();
		foreach($user_id_array['user_ids'] as $chat_token => $value) {
			
			//echo "Chat token: $chat_token";
			
			$n = 0;
			foreach($value as $user_id) {
				
				//echo "user id: " . $user_id . "\n";
			
				$clean_user_id = sanitize::db($user_id);
				$q = "SELECT * FROM users WHERE user_id='$clean_user_id'";
				$r = mysqli_query($dbc,$q);
				
				if ($r) {
					$row = mysqli_fetch_assoc($r);
					
					$q2 = "SELECT * FROM RSAkeys WHERE user_id='$clean_user_id'";
					$r2 = mysqli_query($dbcc,$q2);
					
					if ($r2) {
						
						$row2 = mysqli_fetch_assoc($r2);
				
						$userinfo[$chat_token][$n] = array('name' => $row['username'],
								  'gravatar' => Chat::gravatarFromHash(Chat::gravatarFromEmail($row['email']),30),
								  'email' => $row['email'],
								  'user_id' => $row['user_id'],
								  'pubkey' => keyFromDBsafe($row2['pubkey']));
						
						$n = $n+1;
					}
				}
			
			}
		}
		
		mysqli_close($dbcc);
		mysqli_close($dbc);
		
		
		return $userinfo;		
	}
	
	public static function getChats($chat_tokens){ //,$lastID
		
		//$lastID = (int)$lastID;
		//$lastID = sanitize::db($lastID);
		
		$chat_token_array = json_decode($chat_tokens,true);
		
		$dbcm = dbconnector::getMsgConnection();
		
		$chats = array();
		$infos = array();
		foreach($chat_token_array['chat_tokens'] as $chat_token) {
			
			$clean_chat_token = sanitize::db($chat_token);
			
			//$q = "SELECT * FROM chatline WHERE chatline_id>'.$lastID.' AND chat_token='$clean_chat_token' ORDER BY chatline_id ASC";
			$q = "SELECT * FROM chatline WHERE chat_token='$clean_chat_token' ORDER BY post_time ASC";
			$result = mysqli_query($dbcm,$q);
			
			
			if ($result) {
				//echo "Success";
				
				$n=0;
				while ($chat = mysqli_fetch_assoc($result)) {
				//while($key = $result->fetch_object()){
				
					$chat['time'] = array(
						'hours'		=> gmdate('H',strtotime($chat['post_time'])),
						'minutes'	=> gmdate('i',strtotime($chat['post_time']))
					);
					
					//$chat['gravatar'] = Chat::gravatarFromHash($chat->gravatar);
					
					$chats[$chat_token][$n] = $chat;
					$n=$n+1;
				}
				
			} else {
				//echo '<p>' . mysqli_error($dbcm) . '<br /><br />Query: ' . $q . '</p>';
				
				//logger::
			}
			
			$q2 = "SELECT * FROM chatinfo WHERE chat_token='$clean_chat_token'";
			$r2 = mysqli_query($dbcm,$q2);
			
			if($r2) {
				while ($info=mysqli_fetch_assoc($r2)) {
					$chat_owner_id = $info['chat_owner'];
					$chat_owner = new user($chat_owner_id);
					
					$info['chat_owner_info'] = array('user_id' => $chat_owner->user_id,
									 'username' => $chat_owner->username,
									 'email' => $chat_owner->email,
									 'first_name' => $chat_owner->first_name,
									 'last_name' => $chat_owner->last_name);
					$infos[$chat_token] = $info;
				}
			}
			
		}
		
		
		
		mysqli_close($dbcm);
	
		return array('chats' => $chats,
			     'infos' => $infos);
		
	}
	
	public static function gravatarFromHash($hash, $size=23){
		return 'https://www.gravatar.com/avatar/'.$hash.'?size='.$size.'&amp;default='.
				urlencode('https://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size='.$size);
	}
	
	public static function gravatarFromEmail($email)
	{
		$gravatar = sha1(strtolower(trim($email)));
		
		return $gravatar;
	}
}


?>