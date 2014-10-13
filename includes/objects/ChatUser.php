<?php

require('../includes/common/common.php');

class ChatUser extends ChatBase
{
	
	//protected $name = '', $gravatar = '';
	protected $user_id = '';
	protected $enc_mode = '';
	protected $enc_chat_key = '';
	protected $enc_chat_token = '';
	
	public function save()
	{
		
		/*
		 DB::query("
			INSERT INTO webchat_users (name, gravatar)
			VALUES (
				'".DB::esc($this->name)."',
				'".DB::esc($this->gravatar)."'
		)");
		
		return DB::getMySQLiObject();
		//*/
		
		$dbcm = dbconnector::getMsgConnection();
		
		$q = "INSERT INTO chatuser (recipient_id,enc_mode,enc_chat_key,enc_chat_token)
			VALUES (
				'".sanitize::db($this->user_id)."',
				'".sanitize::db($this->enc_mode)."',
				'".sanitize::db($this->enc_chat_key)."',
				'".sanitize::db($this->enc_chat_token)."'
		)";
		// no timestamp
		
		if ((trim(sanitize::db($this->enc_chat_token)) == '') || (trim(sanitize::db($this->enc_chat_key)) == ''))
		{
			$r = '';
			
		} else {
			
			$r = mysqli_query($dbcm,$q);
		
			if ($r) {
				//echo "Success";
			} else {
				//echo '<p>' . mysqli_error($dbcm) . '<br /><br />Query: ' . $q . '</p>';
			}
		}
		
		
		
		mysqli_close($dbcm);
		
		return $r;
	}
	
	public function update()
	{
		/*
		DB::query("
			INSERT INTO webchat_users (name, gravatar)
			VALUES (
				'".DB::esc($this->name)."',
				'".DB::esc($this->gravatar)."'
			) ON DUPLICATE KEY UPDATE last_activity = NOW()");
			//*/
			
		$this->save();
	}
}

?>