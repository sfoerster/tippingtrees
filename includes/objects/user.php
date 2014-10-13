
<?php



class user
{

    public $user_id;
    public $username;
    public $active;
    public $tos_agreement;
    public $verified;
    public $email;
    public $gravatar;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;
    public $gender;
    public $isadmin;
    public $pass;
    public $salt;
    
    public $loc;
    
    //private $dbc;
    
    //private $crypto;
    
    
    private $fin_id;
    public $token;
    private $registration_time;
    
    
    function user($user_id) // sets user_id
    {
        $dbc = dbconnector::getConnection();
        $this->user_id = mysqli_real_escape_string($dbc,$user_id);
        mysqli_close($dbc);
        
        $this->update();
    }
    
    
    function update()
    {
        // $user_id
        
        $dbc = dbconnector::getConnection();
        $uid = sanitize::db($this->user_id);
        $q = "SELECT * FROM users WHERE user_id='$uid'";
        $r = @mysqli_query($dbc,$q);
        
        if ($r)
        {
            $row = mysqli_fetch_array($r,MYSQLI_ASSOC);
            
            $this->username            = $row['username'];
            $this->active              = $row['active'];
            $this->tos_agreement       = $row['tos_agreement'];
            $this->verified            = $row['verified'];
            $this->email               = $row['email'];
            $this->gravatar            = $row['gravatar'];
            $this->pass                = $row['pass'];
            $this->salt                = $row['salt'];
            $this->first_name          = $row['first_name'];
            $this->middle_name         = $row['middle_name'];
            $this->last_name           = $row['last_name'];
            $this->fin_id              = $row['fin_id'];
            $this->birth_date          = $row['birth_date'];
            $this->gender              = $row['gender'];
            $this->token               = $row['token'];
            $this->registration_time   = $row['registration_time'];
            $this->isadmin             = $row['isadmin'];
            
            //$this->loc = new location($row['location_id']);
            
            //$this->crypto = new crypto($this->user_id);
            
        }
        else
        {
            $this->username            = "";
            $this->active              = "";
            $this->tos_agreement       = "";
            $this->verified            = "";
            $this->email               = "";
            $this->pass                = "";
            $this->salt                = "";
            $this->first_name          = "";
            $this->middle_name         = "";
            $this->last_name           = "";
            $this->fin_id              = "";
            $this->birth_date          = "";
            $this->gender              = "";
            $this->token               = "";
            $this->registration_time   = "";
            $this->isadmin             = "";
            
            $this->loc = "";
        }
        
        mysqli_close($dbc);
        
        
    } // end of constructor

    function setValue($name, $value)
    {
        $dbc = dbconnector::getConnection();
        
        switch($name)
        {
            case "username" :
                // in case of no change
                if ($value == $this->username)
                {
                    return array(true,"");
                }
                
                $su = sanitize::general($value);
                if ($su != $value)
                {
                    $error = 'Invalid username';
                    return array(false,$error);
                }
                
                // Ensure that the username is unique
                $q = "SELECT user_id FROM users WHERE UPPER(username)=UPPER('$su')";
                $r = @mysqli_query($dbc, $q);
                
                if (mysqli_num_rows($r) > 0)
                {
                    $error = 'Username is not available. Please choose another.';
                    return array(false,$error);
                }
                else // username does not already exist
                {
                    $q = "UPDATE users SET username='$su' WHERE user_id='$this->user_id'";
                    $r = @mysqli_query($dbc,$q);
                    if ($r)
                    {
                        //this = new user($dbc,$this->user_id);
                        //$this->username = $su;
                        $this->update();
                        return array(true,"");
                    }
                    else // update query failed
                    {
                        $error = "Unknown system error.";
                        return array(false,$error);
                    }
                }
                break; // end set username
            
            case "email" :
                // in case of no change
                if ($value == $this->email)
                {
                    return array(true,"");
                }
                
                // do not allow email changes
                $error = "Cannot change email associated with this account.";
                return array(false,$error);
                
                $se = sanitize::general($value);
                if ($se != $value)
                {
                    $error = 'Invalid email';
                    return array(false,$error);
                }
                
                // Ensure that the username is unique
                $q = "SELECT user_id FROM users WHERE email='$se'";
                $r = @mysqli_query($dbc, $q);
                
                if (mysqli_num_rows($r) > 0)
                {
                    $error = 'Email is not available. Please choose another.';
                    return array(false,$error);
                }
                else // email does not already exist
                {
                    $q = "UPDATE users SET email='$se', verified=0 WHERE user_id='$this->user_id'";
                    $r = @mysqli_query($dbc,$q);
                    if ($r)
                    {
                        //this = new user($dbc,$this->user_id); // not sure this will work
                        //$this->email = $se;
                        //$this->verified = 0;
                        $this->update();
                        send_ver_email($dbc, $this->email);
                        return array(true,"");
                    }
                    else // update query failed
                    {
                        $error = "Unknown system error.";
                        return array(false,$error);
                    }
                }
                
                break; // end set email
            
            case "first_name" :
                // in case of no change
                if ($value == $this->first_name)
                {
                    return array(true,"");
                }
                
                // do not allow first_name changes
                //$error = "Cannot change first name associated with this account.";
                //return array(false,$error);
                
                $s = sanitize::general($value);
                if ($s != $value)
                {
                    $error = 'Invalid first name';
                    return array(false,$error);
                }
                
                /*// Ensure that the username is unique
                $q = "SELECT user_id FROM users WHERE first_name='$s'";
                $r = @mysqli_query($dbc, $q);
                
                if (mysqli_num_rows($r) > 0)
                {
                    $error = 'Email is not available. Please choose another.';
                    return array(false,$error);
                }
                else // email does not already exist
                {*/
                    $q = "UPDATE users SET first_name='$s' WHERE user_id='$this->user_id'";
                    $r = @mysqli_query($dbc,$q);
                    if ($r)
                    {
                        //this = new user($dbc,$this->user_id); // not sure this will work
                        //$this->email = $se;
                        //$this->verified = 0;
                        $this->update();
                        //send_ver_email($dbc, $this->email);
                        return array(true,"");
                    }
                    else // update query failed
                    {
                        $error = "Unknown system error.";
                        return array(false,$error);
                    }
                //}
                
                break; // end set first_name
            
            case "last_name" :
                // in case of no change
                if ($value == $this->last_name)
                {
                    return array(true,"");
                }
                
                // do not allow first_name changes
                //$error = "Cannot change first name associated with this account.";
                //return array(false,$error);
                
                $s = sanitize::general($value);
                if ($s != $value)
                {
                    $error = 'Invalid last name';
                    return array(false,$error);
                }
                
                /*// Ensure that the username is unique
                $q = "SELECT user_id FROM users WHERE first_name='$s'";
                $r = @mysqli_query($dbc, $q);
                
                if (mysqli_num_rows($r) > 0)
                {
                    $error = 'Email is not available. Please choose another.';
                    return array(false,$error);
                }
                else // email does not already exist
                {*/
                    $q = "UPDATE users SET last_name='$s' WHERE user_id='$this->user_id'";
                    $r = @mysqli_query($dbc,$q);
                    if ($r)
                    {
                        //this = new user($dbc,$this->user_id); // not sure this will work
                        //$this->email = $se;
                        //$this->verified = 0;
                        $this->update();
                        //send_ver_email($dbc, $this->email);
                        return array(true,"");
                    }
                    else // update query failed
                    {
                        $error = "Unknown system error.";
                        return array(false,$error);
                    }
                //}
                
                break; // end set last_name
            
            default :
                $error = "Unrecognized option: "; //. $name;
                return array(false,$error);
                break;
            
        }
        
        mysqli_close($dbc);
        
    } // end setValue
    
    function resetPassword($oldpass,$newpass1,$newpass2)
    {
        $dbc = dbconnector::getConnection();
        
        $ps = $oldpass . $this->salt;
        $hps = HASH("sha512",$ps);
        if ($hps == $this->pass)
        {
            if ($newpass1 != $newpass2)
            {
                $error = "The confirmation password does not match the new password.";
                return array(false,$error);
            }
            else
            {
                $salt = HASH("sha512",openssl_random_pseudo_bytes(64));
                $psNew = $newpass1 . $salt;
                $hpsNew = HASH("sha512",$psNew);
                
                $q = "UPDATE users SET pass='$hpsNew', salt='$salt' WHERE user_id='$this->user_id'";
                    $r = @mysqli_query($dbc,$q);
                    if ($r)
                    {
                        //this = new user($dbc,$this->user_id); // not sure this will work
                        //$this->email = $se;
                        //$this->verified = 0;
                        $this->update();
                        //send_ver_email($dbc, $this->email);
                        return array(true,"");
                    }
                    else // update query failed
                    {
                        $error = "Unknown system error.";
                        return array(false,$error);
                    }
            }
        }
        else
        {
            $error = "Current password does not match.";
            return array(false,$error);
        }
        
        mysqli_close($dbc);
        
        
    } // end password reset
    
    function setLocation($locinfo)
    {
        $dbc = dbconnector::getConnection();
        
        $s = sanitize::general($locinfo);
        if ($s != $locinfo)
        {
            $error = "Invalid location information.";
            return array(false,$error);
        }
        else
        {
            $this->loc = location::createNew($s,$this->user_id,'u');
            $location_id = $this->loc->location_id;
            $q = "UPDATE users SET location_id='$location_id' WHERE user_id='$this->user_id'";
                $r = @mysqli_query($dbc,$q);
                if ($r)
                {
                    //this = new user($dbc,$this->user_id); // not sure this will work
                    //$this->email = $se;
                    //$this->verified = 0;
                    $this->update();
                    //send_ver_email($dbc, $this->email);
                    return array(true,"");
                }
                else // update query failed
                {
                    $error = "Unknown system error.";
                    return array(false,$error);
                }
        }
        
        mysqli_close($dbc);
        
    } // end setLocation
    
    function rmLocation()
    {
        $dbc = dbconnector::getConnection();
        
        $location_id = 0;
        $q = "UPDATE users SET location_id='$location_id' WHERE user_id='$this->user_id'";
        $r = @mysqli_query($dbc,$q);
        if ($r)
        {
            //this = new user($dbc,$this->user_id); // not sure this will work
            //$this->email = $se;
            //$this->verified = 0;
            $this->update();
            //send_ver_email($dbc, $this->email);
            return array(true,"");
        }
        else // update query failed
        {
            $error = "Unknown system error.";
            return array(false,$error);
        }
        
        mysqli_close($dbc);
        
    }
    
    
    
    
    //function nearby($radius,$numtoreturn=10,$start=0,$type='t')
    //{

        
        //$center_lat = $loc->latitude; // user latitude
        //$center_lon = $loc->longitude; // user longitude
        
        /*$q = "SELECT tips.tip_id, tips.subject AS name, ( 3959 * acos( cos( radians($center_lat) ) * cos( radians( locations.latitude ) ) * 
                cos( radians( locations.longitude ) - radians($center_lon) ) + sin( radians($center_lat) ) * 
                sin( radians( locations.latitude ) ) ) ) AS distance 
                FROM tips,locations WHERE tips.location_id=locations.location_id HAVING distance < $radius ORDER BY distance";*/
        
        //$q = "SELECT tips.tip_id AS name, locations.street, locations.longitude, locations.latitude, locations.type FROM tips,locations WHERE tips.location_id=locations.location_id ORDER BY post_time LIMIT 0, 10";
        
        //$q = "SELECT * FROM locations LIMIT 0, 10";
        
        //$r = mysqli_query($dbc,$q);
        


    //}
    


}




?>
