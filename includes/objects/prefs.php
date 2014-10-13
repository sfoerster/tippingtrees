
<?php

require('../includes/common/common.php');

class prefs
{
    static function getSymCipher($user_id='0')
    {
        return 'AES-CTR-128';
    }
}

?>
