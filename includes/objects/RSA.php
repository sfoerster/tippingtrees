
<?php

require('../includes/common/common.php');

class RSA
{

    public $user_id;
    
    public $pubkey;
    public $privkey;
    
    public $n;
    public $encrypted_d;
    public $e;
    
    public $encrypted_p;
    public $encrypted_q;
    
    function RSA($user_id)
    {
        $this->user_id = $user_id;
        
        $this->getKey();        
    }
    
    function getKey()
    {
        
        $dbcc = dbconnector::getCryptoConnection();
        
        $uid = sanitize::db($this->user_id);
        
        $q = "SELECT * FROM RSAkeys WHERE user_id='$uid' AND revoked='0' AND start_time<=UTC_TIMESTAMP() ORDER BY start_time DESC";
        $r = mysqli_query($dbcc,$q);
        
        if ($r) // at least one entry returned; delete if any extra
        {
            $gotkey = 0;
            while ($row = mysqli_fetch_array($r,MYSQLI_ASSOC))
            {
                if ($gotkey == 0) // only retrieves first entry
                {
                    $this->pubkey = $row['pubkey'];
                    $this->pubkey = keyFromDBsafe($this->pubkey);
                    
                    $this->privkey = $row['privkey'];
                    $this->privkey = keyFromDBsafe($this->privkey);
                    
                    $this->n = $row['n'];
                    $this->encrypted_d = $row['encrypted_d'];
                    $this->e = $row['e'];
                    
                    $this->encrypted_p = $row['encrypted_p'];
                    $this->encrypted_q = $row['encrypted_q'];
                    
                    // echo 'Key found';
                }
                else // delete 2-end entries
                {
                    $RSAkey_id = $row['RSAkey_id'];
                    $q1 = "DELETE FROM RSAkeys WHERE RSAkey_id='$RSAkey_id'";
                    $r1 = mysqli_query($dbcc,$q1);
                }
                
                $gotkey = 1;
            }
            
            
        }
        else // no keys currently exist for this user
        {
            $this->pubkey = "";
            $this->privkey = "";
            
            $this->n = "";
            $this->encrypted_d = "";
            $this->e = "";
            
            //echo '<p>' . mysqli_error($dbcc) . '<br /><br />Query: ' . $q . '</p>';
        }
        
        mysqli_close($dbcc);
    }
    
    public function setPrivkey($newprivkey) {
        
        $dbcc = dbconnector::getCryptoConnection();
        
        $privkeydb = keyToDBsafe($newprivkey);
        $privkeydb = mysqli_real_escape_string($dbcc,$privkeydb);
        
        $uid = sanitize::db($this->user_id);
        
        $q = "UPDATE RSAkeys SET privkey='$privkeydb' WHERE user_id='$uid'";
        $r = mysqli_query($dbcc,$q);
        
        $this->getKey();
        
        mysqli_close($dbcc);
    }

    public static function setCookie($user_id) {
        
        $thiskey = new RSA($user_id);
        
        //ManageCookie('set','mykeyn',$thiskey->n,NULL,NULL,NULL);
        //ManageCookie('set','mykeye',$thiskey->e,NULL,NULL,NULL);
        //ManageCookie('set','encrypted_d',$thiskey->encrypted_d,NULL,NULL,NULL);
        //ManageCookie('set','encrypted_p',$thiskey->encrypted_p,NULL,NULL,NULL);
        //ManageCookie('set','encrypted_q',$thiskey->encrypted_q,NULL,NULL,NULL);
        
        //ManageCookie('set','myprivkey',$thiskey->privkey,NULL,NULL,NULL);
        //ManageCookie('set','mypubkey',$thiskey->pubkey,NULL,NULL,NULL);
    
    }

}


?>

