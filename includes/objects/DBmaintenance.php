
<?php

require('../includes/common/common.php');

class DBmaintenance
{

    static function maxChatLines($chat_token)
    {
        // if over quote, delete oldest previous chats
        $chatlimit = ConfigSettings::getMaxChatLines();
        
        $dbcm = dbconnector::getMsgConnection();
        $clean_chat_token = sanitize::db($chat_token);
        $q = "SELECT * FROM chatline WHERE chat_token='$clean_chat_token' ORDER BY post_time ASC";
        $r = mysqli_query($dbcm,$q);
        $rowcount=mysqli_num_rows($r);
        //echo 'rowcount: ' . $rowcount;
        if ($rowcount > $chatlimit) {
                
                $overage = $rowcount - $chatlimit;
                $n = 1;
                while($n <= $overage) {
                        $row=mysqli_fetch_assoc($r);
                        $clean_chatline_token = sanitize::db($row['chatline_token']);
                        //echo 'chatline_token: ' . $clean_chatline_token;
                        $q2 = "DELETE FROM chatline WHERE chatline_token='$clean_chatline_token' LIMIT 1";
                        $r2 = mysqli_query($dbcm,$q2);
                        
                        $n = $n+1;
                }
                
        }
        
        mysqli_close($dbcm);
    }
    
    static function maxPMsgInbox($rec_id)
    {
        $clean_rec_id = sanitize::db($rec_id);
        
        $dbcm = dbconnector::getMsgConnection();
        $q = "SELECT * FROM pmessage_inbox WHERE rec_id='$clean_rec_id' ORDER BY pmessage_inbox_id ASC";
        $r = mysqli_query($dbcm,$q);
        $rowcount=mysqli_num_rows($r);
        
        $inboxLimit = ConfigSettings::maxInboxMsgs();
        
        if ($rowcount > $inboxLimit)
        {
                $overage = $rowcount - $inboxLimit;
                $n = 1;
                while($n <= $overage) {
                        $row=mysqli_fetch_assoc($r);
                        $clean_pmessage_inbox_id = sanitize::db($row['pmessage_inbox_id']);
                        //echo 'chatline_token: ' . $clean_chatline_token;
                        $q2 = "DELETE FROM pmessage_inbox WHERE pmessage_inbox_id='$clean_pmessage_inbox_id' LIMIT 1";
                        $r2 = mysqli_query($dbcm,$q2);
                        
                        $n = $n+1;
                }
        }
        
        mysqli_close($dbcm);        
        
    }
    
    static function maxPMsgSent()
    {
        $s = $_SESSION['user'];
        $u = unserialize($s);
        $user_id = $u->user_id;
        
        $dbcm = dbconnector::getMsgConnection();
        $q = "SELECT * FROM pmessage_sent WHERE rec_id='$user_id' ORDER BY pmessage_sent_id ASC";
        $r = mysqli_query($dbcm,$q);
        $rowcount=mysqli_num_rows($r);
        
        $sentLimit = ConfigSettings::maxSentMsgs();
        
        if ($rowcount > $sentLimit)
        {
                $overage = $rowcount - $sentLimit;
                $n = 1;
                while($n <= $overage) {
                        $row=mysqli_fetch_assoc($r);
                        $clean_pmessage_sent_id = sanitize::db($row['pmessage_sent_id']);
                        //echo 'chatline_token: ' . $clean_chatline_token;
                        $q2 = "DELETE FROM pmessage_sent WHERE pmessage_sent_id='$clean_pmessage_sent_id' LIMIT 1";
                        $r2 = mysqli_query($dbcm,$q2);
                        
                        $n = $n+1;
                }
        }
        
        mysqli_close($dbcm);
        
    }
    
    static function maxNotifications($rec_id)
    {
        
        $dbcm = dbconnector::getMsgConnection();
        $q = "SELECT * FROM notification WHERE user_id='$rec_id' ORDER BY notification_id ASC";
        $r = mysqli_query($dbcm,$q);
        $rowcount=mysqli_num_rows($r);
        
        $sentLimit = ConfigSettings::maxNotifications();
        
        if ($rowcount > $sentLimit)
        {
                $overage = $rowcount - $sentLimit;
                $n = 1;
                while($n <= $overage) {
                        $row=mysqli_fetch_assoc($r);
                        $clean_id = sanitize::db($row['notification_id']);
                        //echo 'chatline_token: ' . $clean_chatline_token;
                        $q2 = "DELETE FROM notification WHERE notification_id='$clean_id' LIMIT 1";
                        $r2 = mysqli_query($dbcm,$q2);
                        
                        $n = $n+1;
                }
        }
        
        mysqli_close($dbcm);
        
    }


}


?>
