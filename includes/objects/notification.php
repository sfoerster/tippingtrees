
<?php

require('../includes/common/common.php');

class notification
{

    static function sendNotification($notification)
    {
        $notification = json_decode($notification);
        
        $dbcm = dbconnector::getMsgConnection();
        
        $user_id        = sanitize::db($notification->user_id);
        $enc_key        = sanitize::db($notification->enc_key);
        $enc_sender_id  = sanitize::db($notification->enc_sender_id);
        $enc_content    = sanitize::db($notification->enc_content);
        $enc_link       = sanitize::db($notification->enc_link);
        $enc_signature  = sanitize::db($notification->enc_signature);
        $enc_post_time  = sanitize::db($notification->enc_post_time);
        $enc_mode       = sanitize::db($notification->enc_mode);
        
        // check that user_id is valid
        $dbc = dbconnector::getConnection();
        $qc = "SELECT * FROM users WHERE user_id='$user_id'";
        $rc = mysqli_query($dbc,$qc);
        if (mysqli_num_rows($rc) == 0) {
            mysqli_close($dbc);
            mysqli_close($dbcm);
            
            return array('status' => false);
        }
        
        $q = "INSERT INTO notification (user_id,enc_key,enc_sender_id,enc_content,enc_link,enc_signature,enc_post_time,enc_mode) VALUES ('$user_id','$enc_key','$enc_sender_id','$enc_content','$enc_link','$enc_signature','$enc_post_time','$enc_mode')";
        $r = mysqli_query($dbcm,$q);
        
        DBmaintenance::maxNotifications($user_id);
        
        mysqli_close($dbc);
        mysqli_close($dbcm);
        
        return array('status' => $r);
    }
    
    static function pending()
    {
        $s = $_SESSION['user'];
        $u = unserialize($s);
        //$u = new user($dbc,$_SESSION['user_id']);
            
        $rsakey = new RSA($u->user_id);
        
        $msgs = array();
        
        $dbcm = dbconnector::getMsgConnection();
        
        $clean_user_id = sanitize::db($u->user_id);
        $q = "SELECT * FROM notification WHERE user_id='$clean_user_id' ORDER BY notification_id DESC";
        $r = mysqli_query($dbcm,$q);
        
        if ($r) {
            
            while($row=mysqli_fetch_assoc($r)) {
                
                $msgs[] = $row;
            }
        }
        
        
        mysqli_close($dbcm);
        
        return $msgs;
    }



}

?>
