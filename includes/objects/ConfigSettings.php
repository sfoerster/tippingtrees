
<?php

require('../includes/common/common.php');

class ConfigSettings
{

    static function getMaxChatLines()
    {
        return 25;
    }
    
    static function maxGroups()
    {
        return 2;
    }
    
    static function maxInboxMsgs()
    {
        return 10;
    }
    
    static function maxSentMsgs()
    {
        return 5;
    }
    
    static function maxNotifications()
    {
        return 20;
    }

}


?>
