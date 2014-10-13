<?php

/* Chat line is used for the chat entries */

require('../includes/common/common.php');

class ChatLine extends ChatBase
{
	
	protected $chat_token = '';
	protected $chatline_token = '';
	protected $enc_mode = '';
	protected $enc_chat_msg = '';
	protected $enc_sender_id = '';
	protected $enc_signature = '';
	
	public function save()
	{
		$dbcm = dbconnector::getMsgConnection();
		
		$q = "INSERT INTO chatline (chat_token,chatline_token,enc_mode,enc_chat_msg,enc_sender_id,enc_signature,post_time)
			VALUES (
				'".sanitize::db($this->chat_token)."',
				'".sanitize::db($this->chatline_token)."',
				'".sanitize::db($this->enc_mode)."',
				'".sanitize::db($this->enc_chat_msg)."',
				'".sanitize::db($this->enc_sender_id)."',
				'".sanitize::db($this->enc_signature)."',
				UTC_TIMESTAMP()
		)";
		// no timestamp
		
		$lastId = '';
		
		if ((trim(sanitize::db($this->chat_token)) == '') || (trim(sanitize::db($this->chatline_token)) == '') || (trim(sanitize::db($this->enc_chat_msg)) == '') || (trim(sanitize::db($this->enc_sender_id)) == '') || (trim(sanitize::db($this->enc_signature)) == '') )
		{
			$r = '';
			
		} else {
			
			$r = mysqli_query($dbcm,$q);
		
			if ($r) {
				//echo "Success";
				
				$lastId = $dbcm->insert_id;
			} else {
				//echo '<p>' . mysqli_error($dbcm) . '<br /><br />Query: ' . $q . '</p>';
			}
			
		}
		
		
		
		
		mysqli_close($dbcm);
		
		return $lastId;
	}
}

?>