

<?php

require('../includes/common/common.php');

class profileClass
{
    
    static function displayTitleBar()
    {
        // Open, valid session
        $s = $_SESSION['user'];
        $u = unserialize($s);
        //$u = new user($dbc,$_SESSION['user_id']);
        
        $rsakey = new RSA($u->user_id);
        
        /*echo "<span title=\"" . $rsakey->pubkey . "\">\n";
        echo "<h1>" . $u->last_name . ", " . $u->first_name . "</h1>\n";
        echo "<h2>" . $u->email . " (" . $u->username . ")</h2>\n";
        //echo "<div class=\"htmlkey\">" . nl2br($rsakey->pubkey) . "</div>\n";
        echo "</span>\n"; */
        
        return array('user_id' => $u->user_id,
                     'username' => $u->username,
                     'first_name' => $u->first_name,
                     'last_name' => $u->last_name,
                     'email' => $u->email,
                     'pubkey' => $rsakey->pubkey);
    }
    
    static function getPersonalKeys()
    {
        if (commonPHP::isloggedin()) {
            // Open, valid session
            $s = $_SESSION['user'];
            $u = unserialize($s);
            //$u = new user($dbc,$_SESSION['user_id']);
            
            $rsakey = new RSA($u->user_id);
            
            return array('pubkey' => $rsakey->pubkey,
                         'privkey' => $rsakey->privkey,
                         'status' => 1);
        
        } else {
            return array('status' => 0);
        }
    }
    
    static function displaySelfProfile()
    {
        
        if (commonPHP::isloggedin()) {
            
            // Open, valid session
            $s = $_SESSION['user'];
            $u = unserialize($s);
            //$u = new user($dbc,$_SESSION['user_id']);
            
            $rsakey = new RSA($u->user_id);
            
            ?>
                    <form name="modform">
                        <table align="center" cellspacing="20">
                            <tr>
                            
                                <!-- show user info in left cell -->
                                <td valign="top" align="right" width="350px">
                                    <h1>Personal Information:</h1>
                                    <table>
                                    <tr><td align="right">Username:</td><td align="left"><input type="text" class="rounded" name="username" value="<?php echo $u->username; ?>" /></td></tr>
                                    <tr><td align="right">Email:</td><td align="left"><?php echo $u->email; ?></td></tr>
                                    <tr><td align="right">First name:</td><td align="left"><input type="text" class="rounded" name="first_name" value="<?php echo $u->first_name; ?>" /></td></tr>
                                    <tr><td align="right">Last name:</td><td align="left"><input type="text" class="rounded" name="last_name" value="<?php echo $u->last_name; ?>" /></td></tr>
                                    <?php /* <tr><td align="right">Gender:</td><td align="left"><?php echo $u->gender; ?></td></tr>
                                    <tr><td align="right">Birthdate:</td><td align="left"><?php echo $u->birth_date; ?></td></tr> */ ?>
                                    </table>
                                    
                                    <p><h2>Change Password:<input type="checkbox" id="changePassBox" name="changePassBox" value="on" onclick="profileTogglePasswordDiv();" /></h2></p>
                                    
                                    <div id="profileNewPassword" class="hidden">
                                        <table>
                                        
                                        <tr><td align="right">New Password:</td><td align="left"><input type="password" class="rounded" name="new_pass1wd" /><input type="hidden" name="new_pass1" /></td></tr>
                                        
                                        <tr><td align="right">Confirm New Password:</td><td align="left"><input type="password" class="rounded" name="new_pass2wd" /><input type="hidden" name="new_pass2" /></td></tr>
                                        </table>
                                    </div>
                                    
                                    <p><h1>Confirm Identity:</h1></p>
                                    
                                    <table>
                                    <tr><td align="right">Current Password:</td><td align="left"><input type="password" class="rounded" name="passwd" /><input type="hidden" name="pass" /></td></tr>
                                    
                                    
                                    <input type="hidden" name="new_privkey" />
                                    
                                    </table>
                                
                                
                                </td>
                                
                                <!-- show location info in right cell -->
                                <?php /*<td valign="top" align="right">
                                    <h2>Location Information:</h2>
                                    <table>
                                    <tr><td align="left">Current Street:</td><td align="left"><?php echo $u->loc->street; ?></td></tr>
                                    <tr><td align="left">Current City:</td><td align="left"><?php echo $u->loc->city; ?></td></tr>
                                    <tr><td align="left">Current Subarea:</td><td align="left"><?php echo $u->loc->subarea; ?></td></tr>
                                    <tr><td align="left">Current Area:</td><td align="left"><?php echo $u->loc->area; ?></td></tr>
                                    <tr><td align="left">Current Country:</td><td align="left"><?php echo $u->loc->country; ?></td></tr>
                                    <tr><td align="left">Current Zip Code:</td><td align="left"><?php echo $u->loc->zip; ?></td></tr>
                                    <tr><td align="left">Current Latitude:</td><td align="left"><?php echo $u->loc->latitude; ?></td></tr>
                                    <tr><td align="left">Current Longitude:</td><td align="left"><?php echo $u->loc->longitude; ?></td></tr>
                                    <tr><td align="left">Current Accuracy:</td><td align="left"><?php echo $u->loc->accuracy; ?>m</td></tr>
                                    </table>
                                    
                                    <p><h2>Change Address/Location:</h2></p>
                                    <p>New Address/Location: <input type="text" name="new_loc" /></p>
                                    <p>Remove Location Information: <input type="checkbox" name="remove_location" /></p>
                                
                                
                                
                                
                                
                                </td> 
                            
                            </tr>
                            <tr> */ ?>
                                <td valign="top">
                                    <h1>My Developer API Token:</h1>
                                    <p>In order to integrate Tipping Trees' services with your own website, you may use your account API token to access Tipping Trees' data:</p>
                                    <p>API Token: <?php echo $u->token; ?></p>
                                    <h1>My Security Credentials:</h1>
                                    <p>Public keys may be shared openly with anyone. They are used to encrypt messages for you. Others use your public key to verify that a message is from you.</p>
                                    <a class="button" onclick="toggleHTMLkey('pubkeytext');"> <?php //document.getElementById('pubkeytext').className='visible htmlkey'; ?>
                                        My Public Key
                                    </a>
                                    
                                    <div id="pubkeytext" class="hidden htmlkey"><p><?php echo nl2br($rsakey->pubkey,"<br />\n"); ?></p></div>
                                    
                                    <div class="clear"></div>
                                    
                                    <p>Private keys must be kept secret, they are used to decrypt your messages. Your private key is only 
                                    accessible by logging in to your account with your password. It is not recoverable any other way. 
                                    Even Tipping Trees cannot recover your private key as we do not have your password. 
                                    </p>
                                    <a class="button" onclick="toggleHTMLkey('privkeytext');">
                                        My Encrypted Private Key
                                    </a>
                                    <div id="privkeytext" class="hidden htmlkey"><p><?php echo nl2br($rsakey->privkey,"<br />\n"); ?></p></div>
                                    <?php /*<table cellpadding="20">
                                        <tr valign="top">
                                            <td>
                                                <div id="pubkeytext"><?php echo $rsakey->pubkey; ?></div>
                                            </td>
                                            <td>
                                                <div id="privkeytext"><?php echo $rsakey->privkey; ?></div>
                                            </td>
                                        </tr>
                                    </table> */ ?>
                                </td> 
                            </tr>
                        </table>
                        <input type="button" class="button" value="Save" onclick="finishModify();" />
                    </form>
                    
                    <?php /*<script type="text/javascript">
                        
                        function dispKeys() {
                            
                            var pubkeypem = '<?php echo echoKey(nl2br($rsakey->pubkey)); ?>';
                            var privkeypem = '<?php echo echoKey(nl2br($rsakey->privkey)); ?>'; // echoKey($rsakey->privkey)
                            
                            //document.getElementById("pubkeytext").innerHTML = "<PRE>" + pubkeypem + "</PRE>";
                            //document.getElementById("privkeytext").innerHTML = "<PRE>" + privkeypem + "</PRE>";
                            
                        }
                        
                        window.onload(dispKeys());
                        
                    </script> */ ?>
                    
            <?php
            
        }
        
    }
    
    static function update($username,$first_name,$last_name,$changePass,$pass,$new_pass1,$new_pass2,$new_privkey)
    {
        if (commonPHP::isloggedin() && commonPHP::ispass($pass)) {
            
            $s = $_SESSION['user'];
            $u = unserialize($s);
            
            $errors = array();
            
            // username
            if (empty($username))
            {
                $errors[] = 'No username given';
            } else {
                //$u->username = $_POST['username'];
                list ($success,$data) = $u->setValue('username',$username);
                if ($success == false) {
                    //$errors = array_merge($errors,$data); // only a single error would be returned
                    $errors[] = $data;
                }
            }
            
                // first name
            if (empty($first_name))
            {
                $errors[] = 'No first name given';
            } else {
                list ($success,$data) = $u->setValue('first_name',$first_name);
                if ($success == false) {
                    //$errors = array_merge($errors,$data); // only a single error would be returned
                    $errors[] = $data;
                }
            }
            
            // last name
            if (empty($last_name))
            {
                $errors[] = 'No last name given';
            } else {
                list ($success,$data) = $u->setValue('last_name',$last_name);
                if ($success == false) {
                    //$errors = array_merge($errors,$data); // only a single error would be returned
                    $errors[] = $data;
                }
            }
            
            $pwupdated = false;
            $newprivkey = "";
            // password reset
            if ($changePass == 'on') {
                
            
                if (!empty($pass) && !empty($new_pass1) && !empty($new_pass2))
                {
                    list ($success,$data) = $u->resetPassword($pass,$new_pass1,$new_pass2); // not sanitized here
                 
                    $pwupdated = $success;
                    
                    if ($success == false) {
                        //$errors = array_merge($errors,$data); // only a single error would be returned
                        $errors[] = $data;
                    } else {
                        // successfully changed password on server, must change private key
                        $rsakey = new RSA($u->user_id);
                        $rsakey->setPrivkey($new_privkey); // sanitized in the class
                        // no need to URL decode here, automatically taken care of
                        
                        $newprivkey = $rsakey->privkey;
                    }
                }
            
            }
            
            // update session user object
            $u->update();
            $_SESSION['user'] = serialize($u);
            
            return array('errors' => $errors,
                         'pwupdated' => $pwupdated,
                         'newprivkey' => $newprivkey);
            
        } /* else {
            
            $s = $_SESSION['user'];
            $u = unserialize($s);
            
            $ps = $pass . $u->salt;
            $hps = HASH("sha512",$ps);
           // $hps == $u->pass
            
            return array('errors' => array('not logged in or incorrect password'),
                         'pwdupdated' => false,
                         'pass' => $pass,
                         'hps' => $hps,
                         'upass' => $u->pass);
        } */
        

        
    }
    
    
}



?>
