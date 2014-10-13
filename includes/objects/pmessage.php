
<?php

require('../includes/common/common.php');

class pmessage
{

    static function getMsgForm()
    {
	$s = $_SESSION['user'];
	$u = unserialize($s);
	
        ?>
            <script type="text/javascript">
            
                function showLivePmsgToResult(str)
                {
                    if (str.length==0)
                    { 
                        document.getElementById("pmlivesearch").innerHTML="";
                        document.getElementById("pmlivesearch").style.border="0px";
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
                            document.getElementById("pmlivesearch").innerHTML = "";
                            for (var n=0; n<jsonobj.length; n++) {
                                
                                var disptext = document.createElement("div");
                                
                                var alink = document.createElement("input");
                                alink.type = "button"
                                alink.className = "button button-small";
                                alink.value = "Select As Recipient";
                                alink.data_user_id = jsonobj[n].user['user_id'];
                                alink.data_email   = jsonobj[n].user['email'];
                                alink.data_ukey    = jsonobj[n].key;
                                alink.addEventListener('click',function() {
                                    //PMsgMakeRecipient(this.data_user_id,this.data_email,this.data_ukey);
				    selRecPMsg(this.data_user_id,this.data_email,this.data_ukey);
                                },false);
                                
                                disptext.innerHTML = jsonobj[n].user['username'] + ", " + jsonobj[n].user['email'];
                                disptext.appendChild(alink);
                                
                                document.getElementById("pmlivesearch").appendChild(disptext);
                                //disptext += "<a href=\"\" class=\"button button-small\" />Block</a><br />\n";
                            }
                            //document.getElementById("livesearch").innerHTML=disptext;
                            document.getElementById("pmlivesearch").style.border="1px solid #A5ACB2";
                        }
                    }
                    
                    xmlhttp.open("POST","ajaxHTML.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    
                    var argstr = "page=searchPeople&sesskey=" + encodeURIComponent(window.sesskey) + "&searchQuery=" + str;
                    xmlhttp.send(argstr);
                }
                
                function sendPMsg()
                {
                    var userVars = document.pmform;
                    
                    // make sure fields are not empty
                    if (userVars.pmRecKey.value == "") {
                        statusMessage("No Recipient Selected");
                        userVars.pmPeopleSearchText.focus();
                        return false;
                    }
                    if (userVars.pmsubject.value == "") {
                        statusMessage("Subject Field is Empty");
                        userVars.pmsubject.focus();
                        return false;
                    }
                    if (userVars.pmcontent.value == "") {
                        statusMessage("Body is Empty");
                        userVars.pmcontent.focus();
                        return false;
                    }
                    
                    // send message
                    
                    var rec_rsakey = publicPEMtoRSAKey(userVars.pmRecKey.value);

                    //var mode    = 'AES-CTR-256';
		    //var mode    = 'AES-CTR-128';
		    var mode = '<?php echo prefs::getSymCipher($u->user_id); ?>';
                    
                    // encrypt for the recipient's inbox
                    var pln_key_i = getRandKey(512);
                    var enc_body_i   = ttAESencrypt(pln_key_i,userVars.pmcontent.value,mode);
                    var enc_subj_i   = ttAESencrypt(pln_key_i,userVars.pmsubject.value,mode);
                    
                    var self_id = '<?php $s = $_SESSION['user']; $u = unserialize($s); echo $u->user_id?>';
                    
                    if (true) { //(userVars.signMsg.checked) {
                        var enc_sender_i = ttAESencrypt(pln_key_i,self_id,mode);
                        var signature_i = ttRSAsign(enc_body_i);
                        var enc_signature_i = ttAESencrypt(pln_key_i,signature_i,mode);
                    } else {
                        var enc_sender_i = "";
                        var enc_signature_i = "";
                    }
                    
                    // RSA encrypt the AES key
                    var enc_key = rec_rsakey.encrypt(pln_key_i);
                    
                    // encrypt for the sender's sent box
                    var pln_key_s = getRandKey(512);
                    var enc_body_s   = ttAESencrypt(pln_key_s,userVars.pmcontent.value,mode);
                    var enc_subj_s   = ttAESencrypt(pln_key_s,userVars.pmsubject.value,mode);
                    var enc_receiver_s = ttAESencrypt(pln_key_s,userVars.pmRecUserID.value,mode);
                    if (true) { //(userVars.signMsg.checked) {
                        var signature_s = ttRSAsign(enc_body_s); 
                        var enc_signature_s = ttAESencrypt(pln_key_s,signature_s,mode);
                    } else {
                        var enc_signature_s = "";
                    }
                    
                    var self_enc_key = ttRSAencryptSelf(pln_key_s);
		    
		    var plnsubject = userVars.pmsubject.value; // for notification
                    
                    userVars.pmcontent.value = enc_body_i;
                    userVars.pmsubject.value = enc_subj_i;
		    
		    // send email copy
		    var sendEmail = "";
		    if (userVars.sendEmail.checked) {
			sendEmail = "yes";
		    } else {
			sendEmail = "no";
		    }
                    
                    $.ttPOST('sendPMsg','rec_id='+userVars.pmRecUserID.value+'&enc_key='+enc_key+'&self_id='+self_id+'&self_enc_key='+self_enc_key+'&mode='+mode+'&enc_subject_i='+enc_subj_i+'&enc_body_i='+enc_body_i+'&enc_sender_i='+enc_sender_i+'&enc_signature_i='+enc_signature_i+'&enc_subject_s='+enc_subj_s+'&enc_body_s='+enc_body_s+'&enc_receiver_s='+enc_receiver_s+'&enc_signature_s='+enc_signature_s+'&sendEmail='+sendEmail,function(r){
                            
                        var trimmed = $.trim(this);
			//console.log(trimmed);
                        var data = jQuery.parseJSON(trimmed);
                        //console.log(data);
			
			
			// recipient notification
			var notmsg = '<?php echo $u->username; ?> has sent you a message. Subject: ' + plnsubject;
			var notlnk = 'https://<?php echo $_SERVER['SERVER_NAME']; ?>/index.php#pmessage-pmessageRead';
			
			var rndkey = getRandHexString(512);
			ttRSAencrypt(rndkey,'notoken',userVars.pmRecUserID.value,function(enc_rndkey) {
			    
			    var enc_content   = ttAESencrypt(rndkey,notmsg,mode);
			    var enc_link      = ttAESencrypt(rndkey,notlnk,mode);
			    //var enc_mode      = ttAESencrypt(rndkey,mode,mode);
			    var enc_sender_id = ttAESencrypt(rndkey,'<?php echo $u->user_id; ?>',mode);
			    var signature       = ttRSAsign(notmsg+notlnk);
			    var enc_signature   = ttAESencrypt(rndkey,signature,mode);
		    
			    var notification = {user_id :   userVars.pmRecUserID.value, // recipient
						enc_key :   enc_rndkey,
						enc_sender_id : enc_sender_id,
						enc_content:  enc_content,
						enc_link :   enc_link,
						enc_signature : enc_signature,
						enc_post_time :   'unused',
						enc_mode      : mode}; 		// defined above
						
			    //console.log('notification='+JSON.stringify(notification));
			    $.ttPOST('sendNotification','notification='+JSON.stringify(notification),function(r){
				//var trimmed = $.trim(this);
				//console.log(trimmed);
				//var data = jQuery.parseJSON(trimmed);
				//console.log('output: '+this);
			    }); // end send notification
						
						
			}); // end RSAencrypt
			
			// sender notification
			notmsg = 'You sent a message. Subject: ' + plnsubject;
			notlnk = 'https://<?php echo $_SERVER['SERVER_NAME']; ?>/index.php#pmessage-pmessageSent';
			var rndkey = getRandHexString(512);
			ttRSAencrypt(rndkey,'notoken','<?php echo $u->user_id; ?>',function(enc_rndkey) {
			    
			    var enc_content   = ttAESencrypt(rndkey,notmsg,mode);
			    var enc_link      = ttAESencrypt(rndkey,notlnk,mode);
			    //var enc_mode      = ttAESencrypt(rndkey,mode,mode);
			    var enc_sender_id = ttAESencrypt(rndkey,'<?php echo $u->user_id; ?>',mode);
			    var signature       = ttRSAsign(notmsg+notlnk);
			    var enc_signature   = ttAESencrypt(rndkey,signature,mode);
		    
			    var notification = {user_id :   '<?php echo $u->user_id; ?>', // recipient
						enc_key :   enc_rndkey,
						enc_sender_id : enc_sender_id,
						enc_content:  enc_content,
						enc_link :   enc_link,
						enc_signature : enc_signature,
						enc_post_time :   'unused',
						enc_mode      : mode}; 		// defined above
						
			    //console.log('notification='+JSON.stringify(notification));
			    $.ttPOST('sendNotification','notification='+JSON.stringify(notification),function(r){
				//var trimmed = $.trim(this);
				//console.log(trimmed);
				//var data = jQuery.parseJSON(trimmed);
				//console.log('output: '+this);
			    }); // end send notification
						
						
			}); // end RSAencrypt
			
			userVars.pmsubject.value = "";
                        userVars.pmcontent.value = "";
                        
			clearRecPMsg();
                        
                        document.getElementById("pmPeopleSearchText").value = "";
                        document.getElementById("pmlivesearch").innerHTML="";
                        document.getElementById("pmlivesearch").style.border="0px";
                        
                        displayPMsgRead('pmessageRead');
                        displayPMsgSent('pmessageSent');
                            
                        
                    });
                    
                    return true;
                }
                
        </script>
            
            <form name="pmform" id="pmform" method="post" enctype="multipart/form-data" onsubmit="return false;"> <?php // action="upload.php" ?>
		
		<input type="hidden" name="sesskey" value="<?php echo $_SESSION['sesskey']; ?>" />
                
                <input type="hidden" name="enc_key" value="" />
                
                <h2 class="title">Personal Message</h2>
                <p></p><h3 class="subtitle">To</h3>
                
                    <?php /* <select name="recipientSel" id="recipientSel">
                        <option value="52">LastName, FirstName: example@vouched.com</option>
                    </select> */ ?>
                    
                    <input type="search" class="rounded" name="pmPeopleSearchText" id="pmPeopleSearchText" size="75" maxlength="100" onkeyup="showLivePmsgToResult(this.value)" />
                    <div id="pmlivesearch"></div>
                    
                    <input type="hidden" name="pmRecUserID" id="pmRecUserID" value="" />
                    <input type="hidden" name="pmRecEmail"  id="pmRecEmail"  value="" />
                    <input type="hidden" name="pmRecKey"    id="pmRecKey"    value="" />
                    
                    <div id="pmRecInfo"></div>
                
                <p></p><h3 class="subtitle">Subject</h3>
                    <input type="text" class="text rounded" size="75" maxlength="140" name="pmsubject" />
                <?php /*<p></p><h3 class="subtitle">Attachments</h3>
                
                <input type="file" name="upl" multiple /> */ ?>
                
                <?php /*<div class="upload">
                    <div id="pmattachment" class="drop">
                        Drop Here

                        <a>Browse</a>
                        <input type="file" name="upl" multiple />
		    </div>

                    <ul>
                            <!-- The file uploads will be shown here -->
                    </ul>
                </div> */ ?>
                
                <p></p><h3 class="subtitle">Body</h3>
                    <textarea class="rounded" cols="75" rows="20" maxlength="5000" name="pmcontent"></textarea>
		    <?php //<p></p>Sign this message: ?><input type="hidden" name="signMsg" checked="checked" />
		    <p></p>Send RSA encrypted copy with functional Tipping Trees URL to email:<input type="checkbox" name="sendEmail" />
                    <p></p><input type="button" class="button" name="Submit" value="Send" onclick="sendPMsg();" /> <?php // type="button" onclick="processPMsg(document.forms['pmform'],function() {});" /> ?>
		    <p>All messages are encrypted with an 128-bit AES key that is shared using the recipient's RSA public key. 
			Messages are signed using your RSA private key.</p>
            </form>
            
            <?php /*<script type="text/javascript">
                
                //$(document).ready(function(){
                //    ttDropInit('pmform');
                //});
                
                $(document).ready(function() {
                    
                    
                    $('#pmform').fileupload({
                        
                        autoUpload: false,
                        
                        add: function (e, formdata) {
                            
                            console.log('length: '+formdata.files.length);
                            console.log('name: '  +formdata.files[0].name);
                            
                            //////////////////////////////////////////////
                            // filter out files that are too big
                            //var myfiles = formdata.upl.files;
                            for (var i = 0, file; file = formdata.upl.files[i]; ++i) {
                                if (formdata.upl.files[i].size > 1024*1024) {
                                    alert('This file is '+formatFileSize(formdata.upl.files[i].size)+'. The browser can only reliably process files up to 1MB.');
                                    formdata.upl.files[i] = null;
                                }
                            }
                            
                            // create the symmetric key that will be used to encrypt the rest of the message members
                            var pmkey = getRandKey(512);
                            
                            // get the recipient user id
                            var user_id = formdata.recipientSel.value;
                            //console.log('user_id: '+user_id);
                            
                            
                            // get recipient info via ajax call
                            PostAjaxRequest(function() {
                                                
                                var trimmed = $.trim(this);
                                var data = jQuery.parseJSON(trimmed);
                                
                                // recipient info
                                var pubpem = data.pubkey;
                                var my_rsa = publicPEMtoRSAKey(pubpem);
                                var email = data.email;
                                
                                //callback(my_rsa,email);
                                
                                // ------- ENCRYPT -------------
                                //console.log('pubkey: '+pubpem);
                                //console.log('key: '+pmkey);
                                formdata.enc_key.value   = my_rsa.encrypt(pmkey); // RSA encrypted AES key
                                formdata.pmsubject.value = ttAESencrypt(pmkey,formdata.pmsubject.value,1);
                                formdata.pmcontent.value = ttAESencrypt(pmkey,formdata.pmcontent.value,1);
                                
                                // files
                                window.requestFileSystem  = window.requestFileSystem || window.webkitRequestFileSystem;
                                window.requestFileSystem(window.TEMPORARY, (formdata.upl.files.length+1)*1024*1024, function(fs) {
                                    // Duplicate each file the user selected to the app's fs.
                                    var myfiles = formdata.upl.files;
                                    for (var i = 0, file; file = myfiles[i]; ++i) {
                                
                                      // Capture current iteration's file in local scope for the getFile() callback.
                                      (function(f) {
                                        fs.root.getFile(f.name+'.encrypted', {create: true, exclusive: false}, function(fileEntry) {
                                          fileEntry.createWriter(function(fileWriter) {
                                            var reader = new FileReader();
                                            reader.onload = function(e){
                                                var encrypted = CryptoJS.AES.encrypt(e.target.result, pmkey);
                                                var myblob = new Blob([encrypted], {type: 'application/octet-stream'});
                                                fileWriter.write(myblob); // Note: write() can take a File or Blob object.
                                                
                                                formdata.upl.files[i] = fs.root.getFile(f.name+'.encrypted', {create: false, exclusive: true}, function(fileEntryTemp) {
                                                    formdata.upl.files[i] = fileEntryTemp;
                                                }, function() {
                                                    formdata.upl.files[i] = null; // if error (not encrypted), it's not touching the server
                                                });
                                            }
                                            reader.readAsDataURL(f);
                                            
                                          });
                                        });
                                      })(file);
                                
                                    }
                                    
                                    formdata.submit();
                                    
                                  }, function() { alert('A modern web browser such as Google Chrome is needed to access all HTML5 features of this site.')} );
                                
                                
                                
                                // TODO: send to email address
                                
                                
                                
                                
                                
                            
                            },'ajaxgetmsguserdata.php','user_id='+user_id);
                            
                            
                            /////////////////////////////////////////////
                            
                            
                        }, // end add
                        
                        formData: function(form) {
                            
                            
                            
                            return form.serializeArray();
                        }
                        
                    }); // end fileupload
                
                }); // end document.ready
                
            </script>  */ ?>
        
        <?php
    }
    
    static function sendPMsg($rec_id,$enc_key,$self_id,$self_enc_key,$mode,$enc_subject_i,$enc_body_i,$enc_sender_i,$enc_signature_i,$enc_subject_s,$enc_body_s,$enc_receiver_s,$enc_signature_s,$sendEmail)
    {
	if (peopleClass::isblocked($rec_id))
	{
	    exit();
	}
        
        $dbcm = dbconnector::getMsgConnection();
        
        $clean_rec_id          = sanitize::db($rec_id);
        $clean_enc_key         = sanitize::db($enc_key);
        $clean_self_id         = sanitize::db($self_id);
        $clean_self_enc_key    = sanitize::db($self_enc_key);
        $clean_mode            = sanitize::db($mode);
        $clean_enc_subject_i   = sanitize::db($enc_subject_i);
        $clean_enc_body_i      = sanitize::db($enc_body_i);
        $clean_enc_sender_i    = sanitize::db($enc_sender_i);
        $clean_enc_signature_i = sanitize::db($enc_signature_i);
        $clean_enc_subject_s   = sanitize::db($enc_subject_s);
        $clean_enc_body_s      = sanitize::db($enc_body_s);
        $clean_enc_receiver_s  = sanitize::db($enc_receiver_s);
        $clean_enc_signature_s = sanitize::db($enc_signature_s);
        
        //$msg_type = 'i';
        
        $q = "INSERT INTO pmessage_inbox (rec_id,enc_key,enc_mode,enc_subject,enc_body,enc_sender,enc_signature,post_time) VALUES ('$clean_rec_id','$clean_enc_key','$clean_mode','$clean_enc_subject_i','$clean_enc_body_i','$clean_enc_sender_i','$clean_enc_signature_i',UTC_TIMESTAMP())";
        $r = mysqli_query($dbcm,$q);
	
	DBmaintenance::maxPMsgInbox($rec_id);
        
        //$msg_type = 's';
        
        $q2 = "INSERT INTO pmessage_sent (rec_id,enc_key,enc_mode,enc_subject,enc_body,enc_receiver,enc_signature) VALUES ('$clean_self_id','$clean_self_enc_key','$clean_mode','$clean_enc_subject_s','$clean_enc_body_s','$clean_enc_receiver_s','$clean_enc_signature_s')";
        $r2 = mysqli_query($dbcm,$q2);
	
	DBmaintenance::maxPMsgSent();
        
        mysqli_close($dbcm);
	
	// handle sending email
	if ($sendEmail == "yes")
	{
	    // sender
	    $s = $_SESSION['user'];
	    $u = unserialize($s);
	    
	    // headers
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	    $headers .= 'From: Tipping Trees <noreply@tippingtrees.com>' . "\r\n";
	    
	    // receiver
	    $rec_user = new user($rec_id);
	    
	    // time
	    $utc_str = gmdate("M d Y H:i:s", time());
	    //$utc = strtotime($utc_str);
	    
	    // ----------------- COPY TO RECIPIENT -------------------------------------
	    
	    // receiver address
	    $email_i = $rec_user->email;
	    
	    // subject
	    $subject_i = $u->last_name . ", " . $u->first_name . " (" . $u->email . ") has sent you an encrypted message!";
	    
	    // compose link
	    $svr = $_SERVER['SERVER_NAME'];
	    $email_link_i = "https://$svr/index.php#pmessage-pmessageView-" . urlencode(json_encode(array('enc_key' => $clean_enc_key,
											       'enc_mode' => $clean_mode,
											       'enc_subject' => $clean_enc_subject_i,
											       'enc_body' => $clean_enc_body_i,
											       'enc_signature' => $clean_enc_signature_i,
											       'enc_sender' => $clean_enc_sender_i,
											       'enc_receiver' => '',
											       'post_time' => $utc_str)));
	    
	    $email_link_i = "<a href=\"" . $email_link_i . "\">" . $email_link_i . "</a>";
	    
	    // body
	    $body_i = "From: " . $u->last_name . ", " . $u->first_name . " (" . $u->email . ")";
	    $body_i = $body_i . "<br /><br />\n\n" . "------------- BEGIN TIPPING TREES MESSAGE --------------<br />\n";
	    $body_i = $body_i . wordwrap($clean_enc_body_i,70,"<br>\n") . "<br />\n";
	    $body_i = $body_i . "------------- END TIPPING TREES MESSAGE ----------------<br /><br />\n\n";
	    
	    $body_i = $body_i . "To decrypt message, click or copy and paste this link into your browser:<br />\n";
	    $body_i = $body_i . $email_link_i;
	    
	    mail($email_i, $subject_i, $body_i, $headers);
	    
	    // -------------------------- COPY TO SENDER ---------------------------------
	    
	    // receiver address
	    $email_s = $u->email;
	    
	    // subject
	    $subject_s = $rec_user->last_name . ", " . $rec_user->first_name . " (" . $rec_user->email . ") has received an encrypted message from you!";
	    
	    // compose link
	    $svr = $_SERVER['SERVER_NAME'];
	    $email_link_s = "https://$svr/index.php#pmessage-pmessageView-" . urlencode(json_encode(array('enc_key' => $clean_self_enc_key,
											       'enc_mode' => $clean_mode,
											       'enc_subject' => $clean_enc_subject_s,
											       'enc_body' => $clean_enc_body_s,
											       'enc_signature' => $clean_enc_signature_s,
											       'enc_sender' => '',
											       'enc_receiver' => $clean_enc_receiver_s,
											       'post_time' => $utc_str)));
	    
	    $email_link_s = "<a href=\"" . $email_link_s . "\">" . $email_link_s . "</a>";
	    
	    // body
	    $body_s = "To: " . $rec_user->last_name . ", " . $rec_user->first_name . " (" . $rec_user->email . ")";
	    $body_s = $body_s . "<br /><br />\n\n" . "------------- BEGIN TIPPING TREES MESSAGE --------------<br />\n";
	    $body_s = $body_s . wordwrap($clean_enc_body_s,70,"<br>\n") . "<br />\n";
	    $body_s = $body_s . "------------- END TIPPING TREES MESSAGE ----------------<br /><br />\n\n";
	    
	    $body_s = $body_s . "To decrypt message, click or copy and paste this link into your browser:<br />\n";
	    $body_s = $body_s . $email_link_s;
	    
	    mail($email_s, $subject_s, $body_s, $headers);
	    
	}
        
        return array('status' => 'sent');
        
    }
    
    static function read()
    {
        
        $s = $_SESSION['user'];
        $u = unserialize($s);
        //$u = new user($dbc,$_SESSION['user_id']);
            
        $rsakey = new RSA($u->user_id);
        
        $msgs = array();
        
        $dbcm = dbconnector::getMsgConnection();
        
        $clean_user_id = sanitize::db($u->user_id);
        $q = "SELECT * FROM pmessage_inbox WHERE rec_id='$clean_user_id' ORDER BY pmessage_inbox_id DESC";
        $r = mysqli_query($dbcm,$q);
        
        if ($r) {
            
            while($row=mysqli_fetch_assoc($r)) {
                
                $msgs[] = $row;
            }
        }
        
        
        mysqli_close($dbcm);
        
        return $msgs;
        
    }
    
    static function sent()
    {
        
        
        $s = $_SESSION['user'];
        $u = unserialize($s);
        //$u = new user($dbc,$_SESSION['user_id']);
            
        $rsakey = new RSA($u->user_id);
        
        $msgs = array();
        
        $dbcm = dbconnector::getMsgConnection();
        
        $clean_user_id = sanitize::db($u->user_id);
        $q = "SELECT * FROM pmessage_sent WHERE rec_id='$clean_user_id' ORDER BY pmessage_sent_id DESC";
        $r = mysqli_query($dbcm,$q);
        
        if ($r) {
            
            while($row=mysqli_fetch_assoc($r)) {
                
                $msgs[] = $row;
            }
        }
        
        
        mysqli_close($dbcm);
        
        return $msgs;
        
    }
    
    static function deletePMsg($type,$msg_id)
    {
	$s = $_SESSION['user'];
        $u = unserialize($s);
	
	$clean_user_id = sanitize::db($u->user_id);
	$clean_msg_id  = sanitize::db($msg_id);
	
	$dbcm = dbconnector::getMsgConnection();
	
	$result = false;
	switch($type)
	{
	    case 'inbox':
		$q = "DELETE FROM pmessage_inbox WHERE rec_id='$clean_user_id' AND pmessage_inbox_id='$clean_msg_id' LIMIT 1";
		$r = mysqli_query($dbcm,$q);
		$result = $r;
		break;
	    case 'sent':
		$q = "DELETE FROM pmessage_sent WHERE rec_id='$clean_user_id' AND pmessage_sent_id='$clean_msg_id' LIMIT 1";
		$r = mysqli_query($dbcm,$q);
		$result = $r;
		break;
	    
	    default:
		
	}
	
	mysqli_close($dbcm);
	
	return $result;
    }

}


?>
