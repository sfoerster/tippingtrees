

<?php

require('../includes/common/common.php');

class ChatInfo extends ChatBase
{

    protected $chat_token = '';
    protected $enc_mode = '';
    protected $chat_owner = '';
    protected $enc_chat_owner = '';
    protected $enc_chat_name = '';
    protected $enc_chat_invite = '';
    protected $owner_signature = '';
    
    
    public function save()
    {
        
        $dbcm = dbconnector::getMsgConnection();
		
        $q = "INSERT INTO chatinfo (chat_token,enc_mode,chat_owner,enc_chat_owner,enc_chat_name,enc_chat_invite,owner_signature)
                VALUES (
                        '".sanitize::db($this->chat_token)."',
			'".sanitize::db($this->enc_mode)."',
                        '".sanitize::db($this->chat_owner)."',
			'".sanitize::db($this->enc_chat_owner)."',
			'".sanitize::db($this->enc_chat_name)."',
			'".sanitize::db($this->enc_chat_invite)."',
			'".sanitize::db($this->owner_signature)."'
        )";
        // no timestamp
        
        if (trim(sanitize::db($this->chat_owner)) == '') 
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




}

?>
