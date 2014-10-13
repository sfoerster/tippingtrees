

<?php

require('../includes/common/common.php');

class ChatToken extends ChatBase
{

    protected $chat_token = '';
    protected $enc_user_id = '';
    protected $enc_mode = '';
    protected $enc_isactive = '';
    
    public function save()
    {
        
        $dbcm = dbconnector::getMsgConnection();
		
        $q = "INSERT INTO chattoken (chat_token,enc_mode,enc_user_id,enc_isactive)
                VALUES (
                        '".sanitize::db($this->chat_token)."',
			'".sanitize::db($this->enc_mode)."',
                        '".sanitize::db($this->enc_user_id)."',
			'".sanitize::db($this->enc_isactive)."'
        )";
        // no timestamp
        
        if (trim(sanitize::db($this->enc_user_id)) == '') 
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
