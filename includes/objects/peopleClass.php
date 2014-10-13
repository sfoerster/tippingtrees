
<?php

require('../includes/common/common.php');

class peopleClass
{

    static function displaySearch()
    {
        ?>
        
        <script type="text/javascript">
            
                function showLivePeopleResult(str)
                {
                    if (str.length==0)
                    { 
                        document.getElementById("livesearch").innerHTML="";
                        document.getElementById("livesearch").style.border="0px";
                        return;
                    }
                    
                    if (window.XMLHttpRequest)
                    {// code for IE7+, Firefox, Chrome, Opera, Safari
                      xmlhttp=new XMLHttpRequest();
                    }
                    else
                    {// code for IE6, IE5
                      //xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                      alert('A modern browser such as Google Chrome or Mozilla Firefox is needed for this site.');
                    }
                    
                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200)
                        {
                            //console.log('Response: '+$.trim(xmlhttp.responseText));
                            var jsonobj = JSON.parse(xmlhttp.responseText);
                            //console.log(jsonobj);
                            document.getElementById("livesearch").innerHTML = "";
                            for (var n=0; n<jsonobj.length; n++) {
                                
                                var disptext = document.createElement("div");
                                
                                var alink = document.createElement("input");
                                alink.type = "button"
                                alink.className = "button button-small";
                                alink.value = "Connect";
                                alink.data_user_id = jsonobj[n].user['user_id'];
                                alink.data_email   = jsonobj[n].user['email'];
                                alink.data_ukey    = jsonobj[n].key;
                                alink.addEventListener('click',function() {
                                    ttvouche(this.data_user_id,this.data_email,this.data_ukey);
                                },false);
                                
                                disptext.innerHTML = jsonobj[n].user['username'] + ", " + jsonobj[n].user['email'];
                                disptext.appendChild(alink);
                                document.getElementById("livesearch").appendChild(disptext);
                                //disptext += "<a href=\"\" class=\"button button-small\" />Block</a><br />\n";
                            }
                            //document.getElementById("livesearch").innerHTML=disptext;
                            document.getElementById("livesearch").style.border="1px solid #A5ACB2";
                        }
                    }
                    
                    xmlhttp.open("POST","ajaxHTML.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    
                    var argstr = "page=searchPeople&sesskey=" + encodeURIComponent(window.sesskey) + "&searchQuery=" + str;
                    xmlhttp.send(argstr);
                }
                
        </script>
        
        <form name="peopleSearch" id="peopleSearch">
            <h1>Search for Contacts:</h1><br />
            <input type="search" class="rounded" name="peopleSearchText" size="75" maxlength="100" onkeyup="showLivePeopleResult(this.value)" />
            <div id="livesearch"></div>
            <?php // <input type="button" class="button" value="Search" name="peopleSearchBtn" /> ?>
        </form>
        
        <?php
    }
    
    static function searchPeople($searchQuery)
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $clean_user_id = sanitize::db($u->user_id);
        
        $clean_searchQuery = sanitize::db($searchQuery);
        
        $dbcc = dbconnector::getCryptoConnection();
        $dbc = dbconnector::getConnection();
        $q = "SELECT * FROM users WHERE email LIKE '%" . $clean_searchQuery . "%' AND user_id<>'$clean_user_id' LIMIT 10";
        $r = mysqli_query($dbc,$q);
        
        $people = array();
        if ($r) {
            
            while($row=mysqli_fetch_assoc($r)) {
                
                $user_id = $row['user_id'];
                
                $qk = "SELECT pubkey FROM RSAkeys WHERE user_id='$user_id'";
                $rk = mysqli_query($dbcc,$qk);
                
                if ($rk) {
                    
                    $keyinfo = mysqli_fetch_assoc($rk);
                    $keyexpinfo = keyFromDBsafe($keyinfo['pubkey']);
                    
                    $people[] = array('user' => $row,
                                      'key' => $keyexpinfo);
                    
                }
                
                
                //echo $row['username'] . ", " . $row['email'] . "<a href=\"\" class=\"button button-small\" />Request Signature</a><a href=\"\" class=\"button button-small\" />Block</a><br />\n";
            }
            
        }

        
        mysqli_close($dbc);
        mysqli_close($dbcc);
        
        return $people;
    }
    
    static function getVouches($uid='0')
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        
        
        $dbc  = dbconnector::getConnection();
        $dbcc = dbconnector::getCryptoConnection();
        
	if ($uid == '0') {
	    $user_id = sanitize::db($u->user_id);
	} else {
	    $user_id = sanitize::db($uid);
	}
	
	$rsakey = new RSA($user_id);
        
        $qu = "SELECT * FROM signatures WHERE user_id='$user_id'";
        $ru = mysqli_query($dbcc,$qu);
        
        $voucheme = array();
        if ($ru) {
            
            while($row=mysqli_fetch_assoc($ru)) {
                $other_id = $row['signer_id'];
                
                $quk = "SELECT pubkey FROM RSAkeys WHERE user_id='$other_id'";
                $ruk = mysqli_query($dbcc,$quk);
                
                if ($ruk) {
                    $keyme = mysqli_fetch_assoc($ruk);
                    $keyexpme = keyFromDBsafe($keyme['pubkey']);
                    
                    $qui = "SELECT user_id,username,email,last_name,first_name FROM users WHERE user_id='$other_id'";
                    $rui = mysqli_query($dbc,$qui);
                    
                    if ($rui) {
                        
                        $infome = mysqli_fetch_assoc($rui);
                        
                        $voucheme[] = array('signature' => $row,
                                            'key' => $keyexpme,
                                            'user' => $infome);
                        
                    }
                    
                }
                
            }
            
           
        }
        
        $qs = "SELECT * FROM signatures WHERE signer_id='$user_id'";
        $rs = mysqli_query($dbcc,$qs);
        
        $voucheothers = array();
        if ($rs) {
            
            while($row2 = mysqli_fetch_assoc($rs)) {
                $other_id = $row2['user_id'];
                
                $qsk = "SELECT pubkey FROM RSAkeys WHERE user_id='$other_id'";
                $rsk = mysqli_query($dbcc,$qsk);
                
                if ($rsk) {
                    $keyother = mysqli_fetch_assoc($rsk);
                    $keyexpother = keyFromDBsafe($keyother['pubkey']);
                    
                    $qother = "SELECT user_id,username,email,last_name,first_name FROM users WHERE user_id='$other_id'";
                    $rother = mysqli_query($dbc,$qother);
                    
                    if ($rother) {
                    
                        $otherUser = mysqli_fetch_assoc($rother);
                        
                        $voucheothers[] = array('signature' => $row2,
                                                'key' => $keyexpother,
                                                'user' => $otherUser);
                        
                    }
                }
                
            }
            
        }
        
        mysqli_close($dbcc);
        mysqli_close($dbc);
	
	$userObj = new user($user_id);
	usort($voucheme,"peopleClass::cmp_names");
        usort($voucheothers,"peopleClass::cmp_names");
        return array('myemail' => $userObj->email,
                     'mykey' => $rsakey->pubkey,
                     'voucheme' => $voucheme,
                     'voucheothers' => $voucheothers,
		     'blockme' => peopleClass::getBlockMe($user_id),
		     'blockothers' => peopleClass::getBlockOthers($user_id));
    
    }
    
    static function getBlockMe($uid='0')
    {
	$s = $_SESSION['user'];
	$u = unserialize($s);
        
	if ($uid == '0') {
	    $user_id = sanitize::db($u->user_id);
	} else {
	    $user_id = sanitize::db($uid);
	}
	
	$dbc = dbconnector::getConnection();
	
	$qs = "SELECT * FROM blocks WHERE blocked_id='$user_id'";
        $rs = mysqli_query($dbc,$qs);
        
        $blockme = array();
        if ($rs) {
            
            while($row2 = mysqli_fetch_assoc($rs)) {
                $other_id = $row2['blocker_id'];
                
                //$qsk = "SELECT pubkey FROM RSAkeys WHERE user_id='$other_id'";
                //$rsk = mysqli_query($dbcc,$qsk);
                //
                //if ($rsk) {
                //    $keyother = mysqli_fetch_assoc($rsk);
                //    $keyexpother = keyFromDBsafe($keyother['pubkey']);
                    
                    $qother = "SELECT user_id,username,email,last_name,first_name FROM users WHERE user_id='$other_id'";
                    $rother = mysqli_query($dbc,$qother);
                    
                    if ($rother) {
                    
                        $otherUser = mysqli_fetch_assoc($rother);
                        
                        //$blockothers[] = array('signature' => $row2,
                        //                        'key' => $keyexpother,
                        //                        'user' => $otherUser);
			
			$blockme[] = array('user' => $otherUser);
                        
                    }
                //}
                
            }
            
        }
	
	mysqli_close($dbc);
	
	return $blockme;
    }
    
    static function getBlockOthers($uid='0')
    {
	$s = $_SESSION['user'];
	$u = unserialize($s);
        
	if ($uid == '0') {
	    $user_id = sanitize::db($u->user_id);
	} else {
	    $user_id = sanitize::db($uid);
	}
	
	$dbc = dbconnector::getConnection();
	
	$qs = "SELECT * FROM blocks WHERE blocker_id='$user_id'";
        $rs = mysqli_query($dbc,$qs);
        
        $blockothers = array();
        if ($rs) {
            
            while($row2 = mysqli_fetch_assoc($rs)) {
                $other_id = $row2['blocked_id'];
                
                //$qsk = "SELECT pubkey FROM RSAkeys WHERE user_id='$other_id'";
                //$rsk = mysqli_query($dbcc,$qsk);
                //
                //if ($rsk) {
                //    $keyother = mysqli_fetch_assoc($rsk);
                //    $keyexpother = keyFromDBsafe($keyother['pubkey']);
                    
                    $qother = "SELECT user_id,username,email,last_name,first_name FROM users WHERE user_id='$other_id'";
                    $rother = mysqli_query($dbc,$qother);
                    
                    if ($rother) {
                    
                        $otherUser = mysqli_fetch_assoc($rother);
                        
                        //$blockothers[] = array('signature' => $row2,
                        //                        'key' => $keyexpother,
                        //                        'user' => $otherUser);
			
			$blockothers[] = array('user' => $otherUser);
                        
                    }
                //}
                
            }
            
        }
	
	mysqli_close($dbc);
	
	return $blockothers;
    }
    
    static function isblocked($user_id)
    {
	$s = $_SESSION['user'];
	$u = unserialize($s);
	
	$dbc = dbconnector::getConnection();
	
	$clean_my_id    = sanitize::db($u->user_id);
	$clean_other_id = sanitize::db($user_id);
	$q = "SELECT * FROM blocks WHERE blocker_id='$clean_other_id' AND blocked_id='$clean_my_id'";
	$r = mysqli_query($dbc,$q);
	
	$isblocked = false;
	if (mysqli_num_rows($r) > 0)
	{
	    $isblocked = true;
	}
	
	mysqli_close($dbc);
	
	return $isblocked;
    }
    
    static function getNumSecondVouches($uid='0')
    {
	//$s = $_SESSION['user'];
	//$u = unserialize($s);
	
	$dbcc = dbconnector::getCryptoConnection();
        
	//if ($uid == '0') {
	//    $user_id = sanitize::db($u->user_id);
	//} else {
	    $user_id = sanitize::db($uid);
	//}
	
	$q = "SELECT DISTINCT signer_id FROM signatures WHERE user_id in (SELECT DISTINCT signer_id FROM signatures WHERE user_id='$user_id') AND signer_id<>'$user_id'";
	$r = mysqli_query($dbcc,$q);
	
	$numSecSign = mysqli_num_rows($r);
	
	//$signer_ids = array();
	//while($row = mysqli_fetch_assoc($r))
	//{
	//    $signer_ids[] = $row['signer_id'];
	//}
	
	mysqli_close($dbcc);
	
	return $numSecSign; //$signer_ids;
    }
    
    static function vouche($user_id,$signature,$type)
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $dbcc = dbconnector::getCryptoConnection();
        
        $clean_user_id = sanitize::db($user_id);
        $clean_signature = sanitize::db($signature);
        $clean_type  = sanitize::db($type);
        
        $signer_id = $u->user_id;
        
        $qcheck = "SELECT * FROM signatures WHERE user_id='$clean_user_id' AND signer_id='$signer_id'";
        $rcheck = mysqli_query($dbcc,$qcheck);
        
        $retvalue = false;
        if ($rcheck) {
            $rowcount=mysqli_num_rows($rcheck);
            if ($rowcount==0) {
                // signature will be unique, go ahead and add:
                $q = "INSERT INTO signatures (user_id,signer_id,signature,type,sign_time) VALUES ('$clean_user_id','$signer_id','$clean_signature','$clean_type',UTC_TIMESTAMP())";
                $r = mysqli_query($dbcc,$q);
                
                $retvalue = $r;
            } 
        }
        
        
        
        mysqli_close($dbcc);
        
        return $retvalue;
    }
    
    static function unvouche($user_id)
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $dbcc = dbconnector::getCryptoConnection();
        
        $signer_id = $u->user_id;
        
        $clean_user_id = sanitize::db($user_id);
        
        $q = "DELETE FROM signatures WHERE user_id='$clean_user_id' AND signer_id='$signer_id' LIMIT 1";
        $r = mysqli_query($dbcc,$q);
        
        mysqli_close($dbcc);
        
        return true;
    }
    
    static function block($user_id)
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $dbc = dbconnector::getConnection();
        
        $clean_user_id = sanitize::db($user_id);
        //$clean_signature = sanitize::db($signature);
        //$clean_type  = sanitize::db($type);
        
        $blocker_id = $u->user_id;
        
        $qcheck = "SELECT * FROM blocks WHERE blocked_id='$clean_user_id' AND blocker_id='$blocker_id'";
        $rcheck = mysqli_query($dbc,$qcheck);
        
        $retvalue = false;
        if ($rcheck) {
            $rowcount=mysqli_num_rows($rcheck);
            if ($rowcount==0) {
                // signature will be unique, go ahead and add:
                $q = "INSERT INTO blocks (blocker_id,blocked_id) VALUES ('$blocker_id','$clean_user_id')";
                $r = mysqli_query($dbc,$q);
                
                $retvalue = $r;
            } 
        }
        
        mysqli_close($dbc);
        
        return $retvalue;
    }
    
    static function unblock($user_id)
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $dbc = dbconnector::getConnection();
        
        $blocker_id = $u->user_id;
        
        $clean_user_id = sanitize::db($user_id);
        
        $q = "DELETE FROM blocks WHERE blocked_id='$clean_user_id' AND blocker_id='$blocker_id' LIMIT 1";
        $r = mysqli_query($dbc,$q);
        
        mysqli_close($dbc);
        
        return true;
    }
    
    static function getUserInfo($user_ids)
    {
        $data = json_decode($user_ids,true);
	
	$user_ids = $data['needed_user_ids'];
        
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        $rsakey = new RSA($u->user_id);
        
        $dbc  = dbconnector::getConnection();
        $dbcc = dbconnector::getCryptoConnection();
	
	if (array_key_exists('user_mapping',$data)) {
	    
	    $maps = $data['user_mapping'];
	    $table = $data['table'];
	    foreach($maps as $map) {
		    
		$user_id = sanitize::db($map['user_id']);
		$enc_user_id = sanitize::db($map['enc_user_id']);
		
		// check if user exists
		$q = "SELECT * FROM users WHERE user_id='$user_id'";
		$r = mysqli_query($dbc,$q);
		if (mysqli_num_rows($r) == 0) {
		    // delete entry in chattoken
		    $dbcm = dbconnector::getMsgConnection();
		    switch($table) {
			case 'notification':
			    //$clean_user_id = sanitize::db($u->user_id);
			    //$q = "DELETE FROM notification WHERE user_id='$clean_user_id' AND enc_sender_id='$enc_user_id' LIMIT 1";
			    //$r = mysqli_query($dbcm,$q);
			break;
			case 'pmessage_view':
			break;
			case 'pmessage_sent':
			break;
			default:
		    }
		    
		    mysqli_close($dbcm);
		}
	    }
	}
        
        $userinfo = array();
        foreach($user_ids as $user_id) {
            
            $thisuser = new user($user_id);
            $thiskey  = new RSA($user_id);
            
            $userinfo[] = array('user_id' => $user_id,
                                'user' => $thisuser,
                                'pubkey' => $thiskey->pubkey);
        }
        
        
        mysqli_close($dbcc);
        mysqli_close($dbc);
        
        return $userinfo;
    }
    
    static function getOneUserData($user_id)
    {
	$user = new user($user_id);
	$rsakey = new RSA($user_id);
	
	return array('user_id' => $user->user_id,
		     'username' => $user->username,
		     'email' => $user->email,
		     'first_name' => $user->first_name,
		     'last_name' => $user->last_name,
		     'pubkey' => $rsakey->pubkey);
    }
    
    static function sendInvitation($email,$msg,$inviter_id=76,$subject='')
    {
	if (commonPHP::isloggedin()) {
	    
	
	    $s = $_SESSION['user'];
	    $u = unserialize($s);
	    
	    $invEmail = $u->email;

	    // subject
	    if ($subject == '') {
		$subject = $u->last_name . ", " . $u->first_name . " (" . $u->email . ") has sent you an invitation to Tipping Trees";
	    }
	    
	    
	    // body
	    $body = "From: " . $u->last_name . ", " . $u->first_name . " (" . $u->email . ")";
	
	} else {
	    
	    $invEmail = '';
	    
	    // subject
	    if ($subject == '') {
		$subject = "You are invited to (re)join Tipping Trees!";
	    }
	    
	    // body
	    $body = '';
	    
	}
	    
	    $clean_inviter_id = sanitize::db($inviter_id);
	    $clean_email = sanitize::db($email);
	    $clean_msg = sanitize::db($msg);
	    
	    if(!filter_var($clean_email, FILTER_VALIDATE_EMAIL)) {
		return array('status' => 0,
                         'error' => 'Invalid email address');
	    }
	    
	    $token = HASH("sha256",openssl_random_pseudo_bytes(512));
	    
	    $dbc = dbconnector::getConnection();
	    
	    // see if email is already registered
	    $qr = "SELECT * FROM users WHERE email='$clean_email'";
	    $rr = mysqli_query($dbc,$qr);
	    if ($rr) {
    		$numentries = mysqli_num_rows($rr);
    		if ($numentries > 0) {
    		    return array('status' => 0,
    				 'error' => 'This email is already registered with Tipping Trees. Try searching in the People section.');
    		}
	    }
	    
	    // see if an invitation already exists for this email
	    $qs = "SELECT * FROM invitations WHERE email='$clean_email'";
	    $rs = mysqli_query($dbc,$qs);
	    
	    $numrows = 0;
	    if ($rs) {
		  $numrows = mysqli_num_rows($rs);
	    }
	    
	    if ($numrows == 0) {
    		$q = "INSERT INTO invitations (inviter_id,email,token,post_time) VALUES ('$clean_inviter_id','$clean_email','$token',UTC_TIMESTAMP())";
    		$r = mysqli_query($dbc,$q);
	    } else {
    		$row = mysqli_fetch_assoc($rs); // for token
    		$token = $row['token'];
    		$q = "UPDATE invitations SET inviter_id='$clean_inviter_id', post_time=UTC_TIMESTAMP() WHERE email='$clean_email'";
    		$r = mysqli_query($dbc,$q);
	    }
	    
	    mysqli_close($dbc);
	    
	    // send email to invitee
	    
	    // headers
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	    $headers .= 'From: Tipping Trees <noreply@tippingtrees.com>' . "\r\n";
	    
	    // subject
	    //$subject = $u->last_name . ", " . $u->first_name . " (" . $u->email . ") has sent you an invitation to Tipping Trees!";
	    
	     // compose link
	    $svr = $_SERVER['SERVER_NAME'];
	    $email_link = "https://$svr/index.php#register-registerFromInvitation-" . urlencode(json_encode(array('email' => $clean_email,
											       'inviter_email' => 'service@tippingtrees.com',
											       'token' => $token)));
	    $email_link = "<a href=\"" . $email_link . "\">" . $email_link . "</a>";
	    
	    // body
	    //$body = "From: " . $u->last_name . ", " . $u->first_name . " (" . $u->email . ")";
	    $body = $body . "<br /><br />\n\n"; //. "------------- BEGIN TIPPING TREES MESSAGE --------------<br />\n";
	    $body = $body . wordwrap(sanitize::general($msg),70,"<br>\n") . "<br />\n";
	    $body = $body . "<br /><br />\n\n"; //------------- END TIPPING TREES MESSAGE ----------------
	    
	    $body = $body . "Email: " . $clean_email . "<br />\n";
	    $body = $body . "Security Token: " . $token . "<br /><br />\n\n";
	    
	    $body = $body . "Email is case sensitive.<br /><br />\n\n";
	    
	    $body = $body . "To register, click or copy and paste this link into your browser:<br />\n";
	    $body = $body . $email_link;
	    
	    // prep email
	    $headers = array ('From' => 'Tipping Trees <service@tippingtrees.com>',
		'To' => $clean_email,
		'Subject' => $subject);
	    
	    $mime = new Mail_mime();
	    $mime->setHTMLBody($body);
	    $body = $mime->get();
	    $headers = $mime->headers($headers);
	    
	    $host = "ssl://server.tippingtrees.com";
	    $port = "465";
	    
	    $sender = EmailSender::getService();
	    $username = $sender['username'];
	    $password = $sender['password'];
	    
	    $mail = new Mail;
	    
	    $smtp = $mail->factory('smtp',
	      array ('host' => $host,
		'port' => $port,
		'auth' => true,
		'username' => $username,
		'password' => $password));
	    
	    $mail = $smtp->send($clean_email, $headers, $body);
	    
	    if (PEAR::isError($mail)) {
		//echo("<p>" . $mail->getMessage() . "</p>");
		return array('status' => 0,
			     'error' => 'Invitation did not send');
	    } else {
		//echo("<p>Message successfully sent!</p>");
		return array('status' => 1,
			 'msg' => 'Invitation sent to ' . $clean_email);
	    }
	    
	    
	    //mail($clean_email, $subject, $body, $headers);
	    
	    
	//}
    }
    
    static function inviteForm()
    {
	if (commonPHP::isloggedin()) {
	    
	    $s = $_SESSION['user'];
	    $u = unserialize($s);
	    
	    ?>
	    
	    <script type="text/javascript">
		function sendInvitation()
		{
		    var userVars = document.inviteForm;
		    
		    var argstr = 'inviter_id='+'<?php echo $u->user_id; ?>';
		    argstr += '&email='+encodeURIComponent(userVars.inviteEmail.value);
		    argstr += '&msg='+encodeURIComponent(userVars.inviteText.value);
		    $.ttPOST('invite',argstr,function(r){
            
			//console.log(this);
			var trimmed = $.trim(this);
			//alert(trimmed);
			var data = jQuery.parseJSON(trimmed);
			
			if (data.status == 0) {
			    statusMessage(data.error);
			} else {
			    statusMessage(data.msg);
			}
			
			userVars.inviteEmail.value = "";
			//userVars.inviteText.value = "";
			    
			
			
		    });
		}
	    </script>
	    
	    <form id="inviteForm" name="inviteForm">
		
		<h1>Send an Invitation to Tipping Trees:</h1>
		<p>Email Address:</p>
		<p><input type="text" size="50" maxlength="150" name="inviteEmail" id="inviteEmail" class="rounded" /></p>
		<p>Message:</p>
		<p><textarea name="inviteText" id="inviteText" class="rounded" cols="70" rows="5" maxlength="500"><?php echo $u->first_name . " " . $u->last_name . " would like you to join the free & open encrypted social network: Tipping Trees"; ?></textarea></p>
		<p><input type="button" class="button" value="Send" onClick="sendInvitation();" /></p>
		
	    </form>
	    
	    <?php
	
	}
    }
    
    static function sendDeleteRequest($email,$captcha,$captchaToken)
    {    
	if (CheckCaptcha($captcha, $captchaToken, '!*a&K8N(3bdD', '.fsf!+%F1?4p')) {
	    
	
	    //$s = $_SESSION['user'];
	    //$u = unserialize($s);
	    
	    //$clean_inviter_id = sanitize::db($inviter_id);
	    $clean_email = sanitize::db($email);
	    //$clean_msg = sanitize::db($msg);
	    
	    $token = HASH("sha256",openssl_random_pseudo_bytes(512));
	    
	    $dbc = dbconnector::getConnection();
	    
	    // see if email is already registered
	    $qr = "SELECT * FROM users WHERE email='$clean_email'";
	    $rr = mysqli_query($dbc,$qr);
	    if ($rr) {
		$numentries = mysqli_num_rows($rr);
		if ($numentries == 0) {
		    return array('status' => 0,
				 'error' => 'This email is not registered with Tipping Trees. There is nothing to delete.');
		}
	    }
	    
	    // see if a delete request already exists for this email
	    $qs = "SELECT * FROM deletions WHERE email='$clean_email'";
	    $rs = mysqli_query($dbc,$qs);
	    
	    $numrows = 0;
	    if ($rs) {
		$numrows = mysqli_num_rows($rs);
	    }
	    
	    if ($numrows == 0) {
		$q = "INSERT INTO deletions (email,token,post_time) VALUES ('$clean_email','$token',UTC_TIMESTAMP())";
		$r = mysqli_query($dbc,$q);
	    } else {
		$row = mysqli_fetch_assoc($rs); // for token
		if (strtotime($row['post_time']) > strtotime("-1 day")) {
		    return array('status' => 0,
				 'error' => 'A request was last sent less than one day ago. Cannot send another request at this time.');
		} else {
		    $token = $row['token'];
		    $q = "UPDATE deletions SET post_time=UTC_TIMESTAMP() WHERE email='$clean_email'";
		    $r = mysqli_query($dbc,$q);
		}
	    }
	    
	    mysqli_close($dbc);
	    
	    // send email to invitee
	    
	    // headers
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	    $headers .= 'From: Tipping Trees <noreply@tippingtrees.com>' . "\r\n";
	    
	    // subject
	    $subject = "Reset your Tipping Trees Account";
	    
	     // compose link
	    $svr = $_SERVER['SERVER_NAME'];
	    $email_link = "https://$svr/index.php#register-resetAccount-" . urlencode(json_encode(array('email' => $clean_email,
											       'token' => $token)));
	    $email_link = "<a href=\"" . $email_link . "\">" . $email_link . "</a>";
	    
	    // body
	    //$body = "From: " . $u->last_name . ", " . $u->first_name . " (" . $u->email . ")";
	    $msg = "Warning! Resetting your account will delete all keys, contacts, signatures, messages, groups, and everything associated with the account. This cannot be undone. Typically the only reason to reset an account is if the password is lost. If you did not make this request, do not reset your account.";
	    $body = "<br /><br />\n\n" . "------------- BEGIN TIPPING TREES MESSAGE --------------<br />\n";
	    $body = $body . wordwrap(sanitize::general($msg),70,"<br>\n") . "<br />\n";
	    $body = $body . "------------- END TIPPING TREES MESSAGE ----------------<br /><br />\n\n";
	    
	    $body = $body . "Email: " . $clean_email . "<br />\n";
	    $body = $body . "Security Token: " . $token . "<br /><br />\n\n";
	    
	    $body = $body . "Email is case sensitive.<br /><br />\n\n";
	    
	    $body = $body . "To delete or reset your account, click or copy and paste this link into your browser:<br />\n";
	    $body = $body . $email_link;
	    
	    // prep email
	    try {
		
	    
	    $headers = array ('From' => 'Tipping Trees <service@tippingtrees.com>',
		'To' => $clean_email,
		'Subject' => $subject);
	    
	    $mime = new Mail_mime();
	    $mime->setHTMLBody($body);
	    $body = $mime->get();
	    $headers = $mime->headers($headers);
	    
	    $host = "ssl://server.tippingtrees.com";
	    $port = "465";
	    
	    $sender = EmailSender::getService();
	    $username = $sender['username'];
	    $password = $sender['password'];
	    
	    $mail = new Mail;
	    
	    $smtp = $mail->factory('smtp',
	      array ('host' => $host,
		'port' => $port,
		'auth' => true,
		'username' => $username,
		'password' => $password));
	    
	    $mail = $smtp->send($clean_email, $headers, $body);
	    
	    if (PEAR::isError($mail)) {
		//echo("<p>" . $mail->getMessage() . "</p>");
		return array('status' => 0,
			     'error' => 'Account reset request did not send');
	    } else {
		//echo("<p>Message successfully sent!</p>");
		return array('status' => 1,
			 'msg' => 'Account reset request sent to ' . $clean_email);
	    }
	    
	    } catch(Exception $err) {
		return array('status' => 0,
			     'error' => $err->getMessage());
	    }
	    
	    //mail($clean_email, $subject, $body, $headers);
	    
	    //return array('status' => 1,
	    //	 'msg' => 'Account reset request sent to ' . $clean_email);
	} 
    }
    
    static function resetForm()
    {
	//if (!commonPHP::isloggedin()) {
	    
	    //$s = $_SESSION['user'];
	    //$u = unserialize($s);
	    
	    ?>
	    
	    <script type="text/javascript">
		function sendDeleteRequest()
		{
		    var userVars = document.resetForm;
		    
		    <?php /* var argstr = 'inviter_id='+'<?php echo $u->user_id; ?>'; */ ?>
		    var argstr = 'page=deleteAcct';
		    argstr += '&email='+encodeURIComponent(userVars.deleteEmail.value);
		    argstr += '&captcha='+encodeURIComponent(userVars.deleteCaptcha.value);
		    argstr += '&token='+encodeURIComponent(userVars.token.value);
		    //$.ttPOST('delete',argstr,function(r){
		    PostAjaxRequest(function() {
            
			//console.log(this);
			var trimmed = $.trim(this);
			//alert(trimmed);
			var data = jQuery.parseJSON(trimmed);
			
			if (data.status == 0) {
			    statusMessage(data.error);
			} else {
			    statusMessage(data.msg);
			}
			
			userVars.deleteEmail.value = "";
			userVars.deleteCaptcha.value = "";
			//userVars.inviteText.value = "";
			    
			
		    },'ajaxPublic.php',argstr);
		    //});
		}
	    </script>
	    
	    <form id="resetForm" name="resetForm">
		
		<h1>Send a request to reset your Tipping Trees Account:</h1>
		<p>Tipping Trees is designed to give you complete control of your information. Your password is used in two ways:</p>
		<ol>
		    <li>Your password is hashed with SHA-512 in your browser using JavaScript before being sent to the Tipping Trees server via SSL/TLS to verify your identity. Once your identity is verified, your encrypted RSA credentials are sent to your browser. When the Tipping Trees server receives your hashed password, the server salts it (adding random characters generated by the OpenSSL pseudo-random number generator), and hashes that with SHA-512 on the server.</li>
		    <li>Your encrypted password is saved in a cookie in your browser (encrypted with a unique 512-bit session key). When the Tipping Trees server has verified your identity and sent your browser your encrypted RSA private key, your password decrypts your RSA private key locally in your browser.</li>
		</ol>
		<p>The server could be reset to save a new salted and hashed password. Your RSA private key cannot be reset. The only course of action possible when a password is lost is to completely delete the existing account (with all existing encryption keys, signatures, messages, and everything associated with it) and create a new one. This is a design feature guaranteeing that you have complete control of your information and do not rely on trust of third parties, Tipping Trees included.</p>
		
		<h1 style="color: #AA0310">Warning!</h1>
		<h2>You are sending a request link to your email account to completely delete and reset your account. A deleted account cannot be recovered.</h2>
		<p>Email Address:</p>
		<p><input type="text" size="50" maxlength="150" name="deleteEmail" id="deleteEmail" class="rounded" /></p>
		<p>Please enter the word shown below:</p>
		<?php
		
		$result = CreateCaptcha(26, 8, 'captcha.ttf', '../includes/libraries/captcha/', 'img/captcha/', '!*a&K8N(3bdD', '.fsf!+%F1?4p');
		echo "<p><img src=\"$result[2]\" /></p>";
		echo "<input type=\"hidden\" name=\"token\" value=\"$result[1]\" />";
		
		?>
		<p><input type="text" size="50" maxlength="150" class="rounded" name="deleteCaptcha" id="deleteCaptcha" /></p>
		<p><input type="button" class="button" value="Send" onClick="sendDeleteRequest();" /></p>
		
	    </form>
	    
	    <?php
	
	//}
    }
    
    static function resetAccount($email,$token)
    {
	$dbc = dbconnector::getConnection();
	
	// check to make sure email and token match in the deletions table
	$e = sanitize::db($email);
	$t = sanitize::db($token);
	
	$errors = array();
	
	$q = "SELECT * FROM deletions WHERE email='$e' AND token='$t'";
	$r = @mysqli_query($dbc,$q);
	
	if (mysqli_num_rows($r) == 0) {
	    $errors[] = 'Token does not match email. Email: ' . $e . ' token: ' . $t;
	}
	
	$q1 = "SELECT * FROM users WHERE email='$e'";
	$r1 = mysqli_query($dbc,$q1);
	
	if (mysqli_num_rows($r1) == 0) {
	    $errors[] = 'Email is not in database.';
	} else {
	    $row = mysqli_fetch_assoc($r1);
	    $user_id = $row['user_id'];
	    
	    $user_id = sanitize::db($user_id);
	}
	
	
	if (empty($errors)) {
	    // db connections
	    $dbcc = dbconnector::getCryptoConnection();
	    $dbcm = dbconnector::getMsgConnection();
	    
	    $user_id = sanitize::db($user_id); // just to make very sure
	    
	    // delete RSA key pair
	    $q = "DELETE FROM RSAkeys WHERE user_id='$user_id' LIMIT 1";
	    $r = mysqli_query($dbcc,$q);
	    
	    // delete received signatures
	    $q = "DELETE FROM signatures WHERE user_id='$user_id'";
	    $r = mysqli_query($dbcc,$q);
	    
	    // delete made signatures
	    $q = "DELETE FROM signatures WHERE signer_id='$user_id'";
	    $r = mysqli_query($dbcc,$q);
	    
	    // delete made blocks
	    $q = "DELETE FROM blocks WHERE blocker_id='$user_id'";
	    $r = mysqli_query($dbc,$q);
	    
	    // delete received blocks
	    $q = "DELETE FROM blocks WHERE blocked_id='$user_id'";
	    $r = mysqli_query($dbc,$q);
	    
	    // chatinfo
	    // chat_tokens owned by user in chatinfo
	    $chat_tokens = array();
	    $q = "SELECT * FROM chatinfo WHERE chat_owner='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    while($row=mysqli_fetch_assoc($r)) {
		$chat_tokens[] = sanitize::db($row['chat_token']);
	    }
	    
	    
	    foreach($chat_tokens as $chat_token) {
		// delete chatlines by chat_token
		$q = "DELETE FROM chatline WHERE chat_token='$chat_token'";
		$r = mysqli_query($dbcm,$q);
		
		// delete chattoken by chat_token
		$q = "DELETE FROM chattoken WHERE chat_token='$chat_token'";
		$r = mysqli_query($dbcm,$q);
		
	    }
	    
	    // delete chatuser by recipient_id
	    $q = "DELETE FROM chatuser WHERE recipient_id='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    
	    // delete chatinfo by chat_owner
	    $q = "DELETE FROM chatinfo WHERE chat_owner='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    
	    // delete from notifications by user_id
	    $q = "DELETE FROM notification WHERE user_id='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    
	    // delete from pmessage_inbox by rec_id
	    $q = "DELETE FROM pmessage_inbox WHERE rec_id='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    
	    // delete from pmessage_sent by rec_id
	    $q = "DELETE FROM pmesssage_sent WHERE rec_id='$user_id'";
	    $r = mysqli_query($dbcm,$q);
	    
	    // delete from deletions by email
	    $q = "DELETE FROM deletions WHERE email='$e' LIMIT 1";
	    $r = mysqli_query($dbc,$q);
	    
	    
	    // delete user entry from table
	    $q = "DELETE FROM users WHERE user_id='$user_id' LIMIT 1";
	    $r = mysqli_query($dbc,$q);
	    
	    // send invitation to rejoin
	    $msg = 'Rejoin Tipping Trees';
	    peopleClass::sendInvitation($e,$msg);
	    
	    mysqli_close($dbcc);
	    mysqli_close($dbcm);
	    
	    return array('status' => 1);
	    
	} else {
	    return array('status' => 0,
	    		 'errors' => 'redacted'); //$errors$errors
	}
	
	mysqli_close($dbc);
    }
    
    //************* NO LOGIN REQUIRED FOR THIS FUNCTION *********************
    static function getUserDataFromEmail($email)
    {
	$clean_email = sanitize::db($email);
	
	$dbc = dbconnector::getConnection();
	$q = "SELECT user_id FROM users WHERE email='$clean_email'";
	$r = mysqli_query($dbc,$q);
	mysqli_close($dbc);
	
	
	$out = array();
	if ($r)
	{
	    $userdata = mysqli_fetch_assoc($r);
	    $user_id = $userdata['user_id'];
	    
	    //return array('user_id' => $user_id);
	    
	    $out = peopleClass::getOneUserData($user_id);
	    //$out['pubkey'] = nl2br($out['pubkey'],"<br />\n");
	    
	    $out['mode'] = prefs::getSymCipher($user_id);
	    $out['svrname'] = $_SERVER['SERVER_NAME'];
	    
	    
	    $dbcc = dbconnector::getCryptoConnection();
	    $q2 = "SELECT * FROM signatures WHERE user_id='$user_id'";
	    $r2 = mysqli_query($dbcc,$q2);
	    
	    if ($r2)
	    {
		$sigdata = mysqli_num_rows($r2);
		
		$out['numsig'] = $sigdata;
		$out['numSecSign'] = peopleClass::getNumSecondVouches($user_id);
	    }
	
	    //mysqli_close($dbcc);
	
	
	
	
	    // if the browser user is logged in, display more information and options
	    if (commonPHP::isloggedin()) {
		$out['isloggedin'] = 1;
		
		$s = $_SESSION['user'];
		$u = unserialize($s);
		
		$selfkey = new RSA($u->user_id);
		$out['selfinfo'] = array('user_id' => $u->user_id,
					 'email' => $u->email,
					 'pubkey' => $selfkey->pubkey);
		
		$signer_id=$u->user_id;
		
		// check if you have already signed this person's key
		//$dbcc = dbconnector::getCryptoConnection();
		$q3 = "SELECT * FROM signatures WHERE user_id='$user_id' AND signer_id='$signer_id'";
		$r3 = mysqli_query($dbcc,$q3);
		$signed = 0;
		if ($r3) {
		    $signed = mysqli_num_rows($r3);
		}
		
		if ($user_id == $signer_id) {
		    $out['self'] = 1;
		} else {
		    $out['self'] = 0;
		}
		
		
		$out['signed'] = $signed;
		
		//if ($signed == 0) {
		    
		$rsakey = new RSA($user_id);
		
		$out['userinfo'] = array('user_id' => $user_id,
					  'email' => $clean_email,
					  'pubkey' => $rsakey->pubkey);
		//}
		
		$out['vouchinfo'] = peopleClass::getVouches($user_id);
		
	    } else {
		$out['isloggedin'] = 0;
	    }
	    
	    
	    mysqli_close($dbcc);
	    
	}
	
	return $out;
    }
    

    static function displayRequests()
    {
        ?>
        
        
        <?php
    }
    
    static function cmp_names($a,$b)
    {
	return strtolower($a['user']['last_name'] . $a['user']['first_name']) > strtolower($b['user']['last_name'] . $b['user']['first_name']);
    }


}



?>
