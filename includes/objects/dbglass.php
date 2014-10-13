
<?php

require('../includes/common/common.php');

class dbglass
{
    static function getChats($maxlines=3)
    {
        return array('chatuser' => dbglass::getChatUsers($maxlines),
                     'chattoken' => dbglass::getChatTokens($maxlines),
                     'chatinfo' => dbglass::getChatInfos($maxlines),
                     'chatline' => dbglass::getChatLines($maxlines));
    }

    static function getChatUsers($maxlines=10)
    {
        $dbcm = dbconnector::getMsgConnection();
        
        $q = "SELECT * FROM chatuser LIMIT " . sanitize::db($maxlines);
        $r = mysqli_query($dbcm,$q);
        
        $chatusers = array();
        while ($row = mysqli_fetch_assoc($r)) {
            $chatusers[] = $row;
        }
        
        mysqli_close($dbcm);
        
        return $chatusers;
    }
    
    static function getChatTokens($maxlines=10)
    {
        $dbcm = dbconnector::getMsgConnection();
        
        $q = "SELECT * FROM chattoken LIMIT " . sanitize::db($maxlines);
        $r = mysqli_query($dbcm,$q);
        
        $chattokens = array();
        while ($row = mysqli_fetch_assoc($r)) {
            $chattokens[] = $row;
        }
        
        mysqli_close($dbcm);
        
        return $chattokens;
    }
    
    static function getChatInfos($maxlines=10)
    {
        $dbcm = dbconnector::getMsgConnection();
        
        $q = "SELECT * FROM chatinfo LIMIT " . sanitize::db($maxlines);
        $r = mysqli_query($dbcm,$q);
        
        $chatinfos = array();
        while ($row = mysqli_fetch_assoc($r)) {
            $chatinfos[] = $row;
        }
        
        mysqli_close($dbcm);
        
        return $chatinfos;
    }
    
    static function getChatLines($maxlines=10)
    {
        $dbcm = dbconnector::getMsgConnection();
        
        $q = "SELECT * FROM chatline LIMIT " . sanitize::db($maxlines);
        $r = mysqli_query($dbcm,$q);
        
        $chatlines = array();
        while ($row = mysqli_fetch_assoc($r)) {
            $chatlines[] = $row;
        }
        
        mysqli_close($dbcm);
        
        return $chatlines;
    }

}
