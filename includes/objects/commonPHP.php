
<?php

require('../includes/common/common.php');

class commonPHP
{

    static function checkFormSubmission()
    {
        /*$allowed_hosts = array('www.tippingtrees.com', 'tippingtrees.com');
    
        if (($_SERVER['REQUEST_METHOD'] != 'POST') || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
            
            redirect_user('index.php');
            exit();
            
        }*/
        
        // html_entity_decode needed to decode URIencoded "+"
        if ( !isset($_POST['sesskey']) || !isset($_SESSION['sesskey']) || (html_entity_decode($_POST['sesskey'],ENT_NOQUOTES,'UTF-8') != $_SESSION['sesskey']) || ($_SERVER['REQUEST_METHOD'] != 'POST') ) {
            
            //echo "POST: " . html_entity_decode($_POST['sesskey'],ENT_NOQUOTES,'UTF-8') . "\n";
            //echo "SESSION: " . $_SESSION['sesskey'] . "\n";
            
            //redirect_user('index.php');
            exit();
            
        }
        
    
    }
    
    static function checkisadmin()
    {
        if (commonPHP::isloggedin())
        {
            $s = $_SESSION['user'];
            $u = unserialize($s);
        
            $us = new user($u->user_id);
            
            if ($us->isadmin == 1)
            {
                return true;
            }
        }
        
        return false;
        
    }
    
    static function ispass($pass)
    {
        if (commonPHP::isloggedin()) {
            
            $s = $_SESSION['user'];
            $u = unserialize($s);
            
            $ps = $pass . $u->salt;
            $hps = HASH("sha512",$ps);
            if ($hps == $u->pass) {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }

    }
    
    static function isloggedin()
    {
        if (isset($_SESSION['agent']) && ($_SESSION['agent'] == HASH("sha512",$_SERVER['HTTP_USER_AGENT'])) ) {
            
            $s = $_SESSION['user'];
            $u = unserialize($s);
        
            $us = new user($u->user_id);
            
            if ($us->active == 1)
            {
                return true;
            }
        
        }
        
        //redirect_user('logout.php');
            
        return false;
            
        
    }
    
    static function getuserjs()
    {
        if (commonPHP::isloggedin())
        {
            $s = $_SESSION['user'];
            $u = unserialize($s);
            //$u = new user($dbc,$_SESSION['user_id']);
            
            $rsakey = new RSA($u->user_id);
            
            ?>
            
            <script type="text/javascript">
                
                // essentially global user variables
                var pubkey  = '<?php echo echoKey($rsakey->pubkey); ?>';
                
                var privkey = '<?php echo echoKey($rsakey->privkey); ?>';
                
                var sesskey = '<?php echo $_SESSION['sesskey']; ?>';
                window.sesskey = sesskey;
                
                //getPersonalKeys(function() {});
                window.pubkey  = pubkey;
                window.privkey = privkey;
                
                
                
                function getPubkey(chat_token,user_id,callback) {
                    
                    //var elem = document.getElementById('form' + chat_token); //.elements['pubkey'+user_id];
                    
                    
                    if ( (typeof(document.getElementById('form' + chat_token)) != 'undefined') && (document.getElementById('form' + chat_token) != null ) && (typeof(document.getElementById('form' + chat_token).elements['pubkey'+user_id]) != 'undefined') && (document.getElementById('form' + chat_token).elements['pubkey'+user_id] != null ) && (typeof(document.getElementById('form' + chat_token).elements['email'+user_id]) != 'undefined') && (document.getElementById('form' + chat_token).elements['email'+user_id] != null ) ) {
                        
                        //var elem2 = document.getElementById('form' + chat_token).elements['pubkey'+user_id];
                        

                            
                        var pubkey = document.getElementById('form' + chat_token).elements['pubkey'+user_id].value;
                        var my_rsa = publicPEMtoRSAKey(pubkey);
                        
                        var email = document.getElementById('form' + chat_token).elements['email'+user_id].value;
                        
                        callback(my_rsa,email);
                            
                            
                        
                        
                    } else {
                    
                        //console.log('user_id: '+user_id);
                        
                        var argstr = 'user_id='+user_id;
                            
                        //PostAjaxRequest(function() {
                        $.ttPOST('getOneUserData',argstr,function(r){
                        
                            var trimmed = $.trim(this);
                            //console.log(trimmed);
                            var data = jQuery.parseJSON(trimmed);
                            
                            var pubpem = data.pubkey;
                            var my_rsa = publicPEMtoRSAKey(pubpem);
                            
                            var email = data.email;
                            
                            callback(my_rsa,email);
                        });
                        
                        //},'ajaxgetmsguserdata.php','user_id='+user_id);
                        
                    
                    }
                    
                    
                }
                
                function ttRSAencrypt(plntxt,chat_token,user_id,callback) {
                    
                    //var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
                    
                    getPubkey(chat_token,user_id,function(rec_rsa,email) { //email not used
                        
                        var cryptext = '';
                        try {
                            cryptext = rec_rsa.encrypt(plntxt);
                        } catch(err) {
                            console.log('could not RSA encrypt');
                        }
                        
                        callback(cryptext);
                    
                    });

                    
                }
                
                function ttRSAencryptSelf(plntxt) {
                    
                    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
                    return my_rsa.encrypt(plntxt);
                }
                
                function ttRSAsign(signedText) {
                    
                    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
                    var signedhash = my_rsa.signString(signedText,'sha256');
                    
                    return signedhash;
                }
                
                function ttRSAdecrypt(cryptext) {
                    
                    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
                    return my_rsa.decrypt(cryptext);
                }
                
                function ttRSAverify(chat_token,user_id,msg,sign) {
                    
                    getPubkey(chat_token,user_id,function(sender_rsa,email) { // email not used
                        
                        var verified = false;
                        try {
                            verified = sender_rsa.verifyString(msg,sign);
                        } catch(err) {
                            
                        }
                        
                        return verified;
                        
                    });

                }
                
                
                var chat = {
                        
                        // data holds variables for use in the class:
                        
                        data : {
                                        
                                        //lastID 		 : 0,
                                        noActivity	 : 0,
                                        nextRequest      : 10000,
                                        //finishedUserData : true
                                        
                        },
                        
                        // Init binds event listeners and sets up timers:
                        
                        init : function(chat_token){
                                
                                // Using the defaultText jQuery plugin, included at the bottom:
                                //$('#msgchat_token name').defaultText('Nickname');
                                //$('#msgchat_token email').defaultText('Email (Gravatars are Enabled)');
                                
                                // Converting the #chatLineHolder div into a jScrollPane,
                                // and saving the plugin's API in chat.data:
                                
                                //chat.data.jspAPI =
                                $('#chatLineHolder' + chat_token).jScrollPane({
                                        verticalDragMinHeight: 12,
                                        verticalDragMaxHeight: 12
                                }).data('jsp');
                                
                                chat.data.chat_token = {};
                                chat.data.chat_token.lastID = 0;
                                
                                //console.log('init chat lastID: ' + chat.data.chat_token.lastID);
                                
                                // We use the working variable to prevent
                                // multiple form submissions:
                                
                                var working = false;
                                
                                // Logging a person in the chat:
                                
                                /*
                                $('#msgchat_token loginForm').submit(function(){
                                        
                                        if(working) return false;
                                        working = true;
                                        
                                        // Using our tzPOST wrapper function
                                        // (defined in the bottom):
                                        
                                        $.ttPOST('login',$(this).serialize(),function(r){
                                                working = false;
                                                
                                                if(r.error){
                                                        chat.displayError(r.error);
                                                }
                                                else chat.login(r.name,r.gravatar);
                                        });
                                        
                                        return false;
                                });
                                //*/
                                
                                /*
                                $.ttPOST('login',{'chat_token' : chat_token},function(r){
                                        
                                        if(r.error){
                                                chat.displayError(r.error);
                                        }
                                        else chat.login(r.name,r.gravatar,chat_token);
                                });
                                //*/
                                
                                // Submitting a new chat entry:
                                
                                $('#form'+chat_token).submit(function(){
                                        
                                        var text = $('#chatText'+chat_token).val();
                                        
                                        
                                        if(text.length == 0){
                                                return false;
                                        }
                                        
                                        text = ttsafe_tags_replace(text);
                                        
                                        if(working) return false;
                                        working = true;
                                        
                                        // Assigning a temporary ID to the chat:
                                        /*
                                        var tempID = 't'+getRandHexString(512),
                                            params = {
                                                        id		: tempID,
                                                        author		: chat.data.name,
                                                        gravatar	: chat.data.gravatar,
                                                        text		: text.replace(/</g,'&lt;').replace(/>/g,'&gt;')
                                                };
                                        //*/
                
                                        // Using our addChatLine method to add the chat
                                        // to the screen immediately, without waiting for
                                        // the AJAX request to complete:
                                        
                                        //chat.addChatLine($.extend({},params));
                                        
                                        // Using our ttPOST wrapper method to send the chat
                                        // via a POST AJAX request:
                                        
                                        //var mode = 'AES-CTR-256';
                                        //var mode = 'AES-CTR-128';
                                        var mode = '<?php echo prefs::getSymCipher($u->user_id); ?>';
                                        
                                        var chat_key        = $('#chatkey'+chat_token).val();
                                        
                                        //var chat_token
                                        var chatline_token  = getRandHexString(512);
                                        var enc_chat_msg    = ttAESencrypt(chat_key,text,mode);
                                        var enc_sender_id   = ttAESencrypt(chat_key,'<?php echo $u->user_id; ?>',mode);
                                        
                                        var signature       = ttRSAsign(text);
                                        var enc_signature   = ttAESencrypt(chat_key,signature,mode);
                                        
                                        $('#chatText'+chat_token).val(enc_chat_msg);
                                        
                                        var argstr = 'chat_token='+chat_token;
                                        argstr += '&chatline_token='+chatline_token;
                                        argstr += '&enc_mode='+mode;
                                        argstr += '&enc_chat_msg='+enc_chat_msg;
                                        argstr += '&enc_sender_id='+enc_sender_id;
                                        argstr += '&enc_signature='+enc_signature;
                                        
                                        $.ttPOST('submitChat',argstr,function(r){
                                                
                                                working = false;
                                                $('#chatText'+chat_token).val('');
                                                //$('div.chat-'+tempID).remove();
                                                
                                                var trimmed = $.trim(this);
                                                //console.log(trimmed);
                                                var data = JSON.parse(trimmed);
                                                
                                                if (data.status == 0) {
                                                    statusMessage(data.error);
                                                }
                                                
                                                //console.log(data);
                                                var params = {chatline_id:data.insertID,
                                                              chatline_token:chatline_token,
                                                              enc_mode:mode,
                                                              enc_chat_msg:enc_chat_msg,
                                                              enc_sender_id:enc_sender_id,
                                                              enc_signature:enc_signature,
                                                              time:data.time,
                                                              post_time:data.post_time};
                                                
                                                chat.addChatLine(chat_token,chat_key,params);
                                                chat.decryptLine(chat_token,chat_key,params);
                                                
                                                //alert(data.insertID);
                                                //chat.data.lastID = data.insertID;
                                                
                                                //params['id'] = data.insertID;
                                                
                                                
                                                //chat.addChatLine($.extend({},params));
                                        });
                                        
                                        // notifications for users
                                        var users = chat.data.chat_token.user_ids[chat_token];
                                        var user_active = chat.data.chat_token.user_active[chat_token];
                                        
                                        var notmsg = '<?php echo $u->username; ?> says: ' + text;
                                        var notlnk = 'https://<?php echo $_SERVER['SERVER_NAME']; ?>/index.php#group-groupView-'+chat_token;
                                              
                                        var notifications = {};                                  
                                        for (var key in users) {
                                            //console.log('key: '+key+' user:'+users[key]+' isactive: '+user_active[users[key]]);
                                            
                                            if (user_active[users[key]] == '1') {
                                                
                                                var rndkey = getRandHexString(512);
                                                ttRSAencrypt(rndkey,chat_token,users[key],function(enc_rndkey) {
                                                    
                                                    var enc_content   = ttAESencrypt(rndkey,notmsg,mode);
                                                    var enc_link      = ttAESencrypt(rndkey,notlnk,mode);
                                                    //var enc_mode      = ttAESencrypt(rndkey,mode,mode);
                                                    var enc_sender_id = ttAESencrypt(rndkey,'<?php echo $u->user_id; ?>',mode);
                                                    var signature       = ttRSAsign(notmsg+notlnk);
                                                    var enc_signature   = ttAESencrypt(rndkey,signature,mode);
                                            
                                                    var notification = {user_id :   users[key], // recipient
                                                                        enc_key :   enc_rndkey,
                                                                        enc_sender_id : enc_sender_id,
                                                                        enc_content:  enc_content,
                                                                        enc_link :   enc_link,
                                                                        enc_signature : enc_signature,
                                                                        enc_post_time :   'unused',
                                                                        enc_mode      : mode};
                                                                        
                                                    //console.log('notification='+JSON.stringify(notification));
                                                    $.ttPOST('sendNotification','notification='+JSON.stringify(notification),function(r){
                                                        //var trimmed = $.trim(this);
                                                        //console.log(trimmed);
                                                        //var data = jQuery.parseJSON(trimmed);
                                                        //console.log('output: '+this);
                                                    }); // end send notification
                                                                        
                                                                        
                                                }); // end RSAencrypt
                                                    
                                                    
                                                    
                                            } // end if active user in group
                                                
                                                                    
                                        } // end loop through users in group
                                    
                                        
                                        
                                    return false;
                                    
                                }); // end submit
                                
                                // Logging the user out:
                                
                                //*
                                //$('a.logoutButton').live('click',function(){
                                //$('#logout'+chat_token).click(function(e){
                                
                                var logoutlink = document.getElementById('logout'+chat_token);
                                logoutlink.data_user_id = <?php echo $u->user_id; ?>;
                                logoutlink.data_enc_chat_token = document.getElementById('form' + chat_token).enc_chat_token.value;
                                logoutlink.data_chat_token = chat_token;
                                logoutlink.data_enc_user_id = "";
                                logoutlink.data_enc_isactive = "";
                                logoutlink.addEventListener('click',function(e) {
                                            //sendRequest(this.data_user_id,this.data_chat_token,this.data_chat_key);
                                            
                                            //var enc_user_id = document.getElementById('form' + chat_token).enc_user_id.value;
                                            var enc_user_id = this.data_enc_user_id;
                                            //console.log('user_id: ' + this.data_user_id + '\nenc_chat_token: ' + this.data_enc_chat_token + '\nchat_token: ' + this.data_chat_token + '\nenc_user_id: ' + enc_user_id);
                                            
                                            //*
                                            
                                            this.className = "hidden";
                                            
                                            var argstr = 'user_id='+this.data_user_id;
                                            argstr += '&enc_chat_token='+this.data_enc_chat_token;
                                            argstr += '&chat_token='+this.data_chat_token;
                                            argstr += '&enc_user_id='+this.data_enc_user_id;
                                            argstr += '&enc_isactive='+this.data_enc_isactive;
                                            
                                            $.ttPOST('logout',argstr,function() {
                                                
                                                document.getElementById("groupNavList").removeChild(document.getElementById('groupNavLI'+chat_token));
                                                document.getElementById("groupView").removeChild(document.getElementById('msg'+chat_token));
                                                initChats(function() {});
                                            });
                                            //*/
                                            
                                            e.preventDefault();
                                        },false);
                                        
                                        
                                        //$('#chatTopBar'+ chat_token +' > span').fadeOut(function(){
                                        //        $(this).remove();
                                        //});
                                        
                                        //$('#submitForm'+chat_token).fadeOut(function(){
                                        //        $('#msgchat_token loginForm').fadeIn();
                                        //});
                                        
                                        /*
                                        var user_id = 
                                        var enc_chat_token = 
                                        var chat_token = chat_token;
                                        var enc_user_id = 
                                        
                                        console.log('user_id: ' + user_id + '\nenc_chat_token: ' + enc_chat_token + '\nchat_token: ' + chat_token + '\nenc_user_id: ' + enc_user_id);
                                        //*/
                                        
                                        
                                        
                                        
                                        //return false;
                                        //e.preventDefault();
                                //});
                                //*/
                                
                                
                                
                                // Checking whether the user is already logged (browser refresh)
                                /*$.ttGET('checkLogged',function(r){
                                        if(r.logged){
                                                chat.login(r.loggedAs.name,r.loggedAs.gravatar);
                                        }
                                });*/
                                
                                
                        },
                        
                        // The login method hides displays the
                        // user's login data and shows the submit form
                        
                        login : function(name,gravatar,chat_token){
                                
                                chat.data.name = name;
                                chat.data.gravatar = gravatar;
                                chat.data.chat_token = chat_token;
                                $('#chatTopBar'+chat_token).html(chat.render('loginTopBar',chat.data));
                                
                                //$('#submitForm'+chat_token).fadeIn();
                                $('#form'+chat_token).fadeIn();
                                $('#chatText'+chat_token).focus();
                                
                                //alert('name: ' + name + ' gravatar: ' + gravatar + ' chat_token: ' + chat_token);
                                
                                /*
                                $('#msgchat_token loginForm').fadeOut(function(){
                                        $('#msgchat_token submitForm').fadeIn();
                                        $('#msgchat_token chatText').focus();
                                });
                                //*/
                                
                        },
                        
                        // The render method generates the HTML markup 
                        // that is needed by the other methods:
                        
                        render : function(template,params){
                                
                                var arr = [];
                                switch(template){
                                        case 'loginTopBar':
                                                arr = [
                                                '<span><img src="',params.gravatar,'" width="23" height="23" />',
                                                '<span class="name">',params.name,
                                                '</span><a href="" class="logoutButton rounded"><div id="logout',params.chat_token,'">Leave</div></a></span>'];
                                        break;
                                        
                                        case 'chatLine':
                                                arr = [
                                                        '<div class="chat chat-',params.chatline_token,' rounded"><span class="gravatar"><div id="gravatar',params.chatline_token,'"><img src=https://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size=23" width="23" height="23" onload="this.style.visibility=\'visible\'" /></div>','</span><span class="author"><div id="author',params.chatline_token,
                                                        '"></div>:</span><span class="text"><div id="text',params.chatline_token,'">',params.text,'</div></span><span class="time">',params.time,'</span></div>'];
                                        break;
                                        
                                        case 'user':
                                                var actstr = "Unknown:";
                                                if (params.active == '0') {
                                                    actstr = "Inactive:";
                                                } else if (params.active == '1') {
                                                    actstr = "Active:";
                                                } 
                                                //console.log('active: '+params.active);
                                                
                                                //console.log('actstr: '+actstr);
                                                arr = [
                                                        '<div class="user" title="',actstr,':',params.name,':',params.email,'"><a href="',ttPeoplePublicLink(params.email),'"><img src="',
                                                        params.gravatar,'" width="30" height="30" onload="this.style.visibility=\'visible\'" /></a></div>'
                                                ];
                                        break;
                                }
                                
                                // A single array join is faster than
                                // multiple concatenations
                                
                                return arr.join('');
                                
                        },
                        
                        decryptLine : function(chat_token,chat_key,params) {
                            
                            var chatline_id     = params.chatline_id;
                            var chatline_token  = params.chatline_token;
                            var mode            = params.enc_mode;
                            var enc_chat_msg    = params.enc_chat_msg;
                            var enc_sender_id   = params.enc_sender_id;
                            var enc_signature   = params.enc_signature;
                            
                            var chat_msg = ttAESdecrypt(chat_key,enc_chat_msg,mode);
                            var sender_id = ttAESdecrypt(chat_key,enc_sender_id,mode);
                            var signature = ttAESdecrypt(chat_key,enc_signature,mode);
                            
                            
                                
                                //var user_ids = [];
                                //user_ids[0] = sender_id;
                                //$.ttPOST('getUserInfo','user_ids='+JSON.stringify({ user_ids: user_ids }),function(r) {
                                                            
                                    //alert(this);
                                    /*var trimmed_user = $.trim(this);
                                    var user_data    = jQuery.parseJSON(trimmed_user);
                                    var user         = user_data[0];*/
                                    
                                    //var name        = user.name;
                                    //var gravatar    = user.gravatar;
                                    //var email       = user.email;
                                    
                                    var f            = document.getElementById('form' + chat_token);//document.forms['form' + chat_token];
                                    var name         = "Unknown";
                                    var gravatarHTML = "";
                                    
                                    //var verified = ttRSAverify(sender_id,chat_msg,signature);
                                    //alert(pubkey);
                                    var verString = "(<span style=\"color:#990000\">UNVERIFIED</span>)";
                                    try {
                                        var pubkey      = f.elements['pubkey'+sender_id].value;//['value'];//f.elements['pubkey'+sender_id].value;// 
                                        //alert(pubkey.value);
                                        name        = f.elements['username'+sender_id].value;
                                        var gravatar    = f.elements['gravatar'+sender_id].value;
                                        var email       = f.elements['email'+sender_id].value;
                                        
                                        var sender_rsa = publicPEMtoRSAKey(pubkey);
                                        var verified = sender_rsa.verifyString(chat_msg,signature);
                                        gravatarHTML = '<img src="'+gravatar+'" width="23" height="23" onload="this.style.visibility=\'visible\'" />';
                                        
                                        if (verified) {
                                            verString = "(<span style=\"color:#009900\">VERIFIED</span>)";
                                            
                                            verString = "<span title=\"" + signature + "\">" + verString + "</span>";
                                        } 
                                    } catch(err) {
                                        
                                    }
                                    
                                    
                                    $('#author'+chatline_token).html(name+verString);
                                    
                                    setTimeout(function() {
                                        $('#text'+chatline_token).html(chat_msg);
                                        
                                        $('#chatLineHolder' + chat_token).jScrollPane({
                                            verticalDragMinHeight: 12,
                                            verticalDragMaxHeight: 12
                                        }).data('jsp').reinitialise();//chat.data.jspAPI.reinitialise();
                                        
                                        $('#chatLineHolder' + chat_token).jScrollPane({
                                            verticalDragMinHeight: 12,
                                            verticalDragMaxHeight: 12
                                        }).data('jsp').scrollToBottom(true); //chat.data.jspAPI.scrollToBottom(true);
                                        
                                    },2000);
                                    
                                    //gravatar insert
                                    
                                    $('#gravatar'+chatline_token).html(gravatarHTML);
                                    
                                    
                            
                                
                                
                                
                                //});
                            
                            
                        },
                        
                        // The addChatLine method ads a chat entry to the page
                        
                        addChatLine : function(chat_token,chat_key,params){
                                
                                // All times are displayed in the user's timezone
                                
                                var d = new Date();
                                if(params.time) {
                                        
                                        // PHP returns the time in UTC (GMT). We use it to feed the date
                                        // object and later output it in the user's timezone. JavaScript
                                        // internally converts it for us.
                                        
                                        d.setUTCHours(params.time.hours,params.time.minutes);
                                }
                                
                                params.time = (d.getHours() < 10 ? '0' : '' ) + d.getHours()+':'+
                                                          (d.getMinutes() < 10 ? '0':'') + d.getMinutes();
                                                          
                                var chatline_id     = params.chatline_id;
                                var chatline_token  = params.chatline_token;
                                var enc_chat_msg    = params.enc_chat_msg;
                                var enc_sender_id   = params.enc_sender_id;
                                var enc_signature   = params.enc_signature;
                                
                                var renderparams = {chatline_token: chatline_token,
                                                    text: enc_chat_msg,
                                                    time: getLocalTimeFromGMT(params.post_time)};
                                                    //time: params.time};
                                
                                var markup = chat.render('chatLine',renderparams);
                                        //exists = $('#chatLineHolder'+ chat_token +' .chat-'+chatline_token);
                                        
                
                                //if(exists.length){
                                //        exists.remove();
                                //}
                                
                                if(!chat.data.chat_token.lastID){
                                        // If this is the first chat, remove the
                                        // paragraph saying there aren't any:
                                        
                                        //$('#chatLineHolder'+ chat_token +' p').remove();
                                        $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').getContentPane().html('');
                                        
                                        //chat.data.chat_token.lastID = chatline_id;
                                }
                                
                                // If this isn't a temporary chat:
                                //if(params.id.toString().charAt(0) != 't'){
                                        
                                        /*var previous = $('#chatLineHolder'+ chat_token +' .chat-'+(+chatline_id - 1));
                                        if(previous.length){
                                                previous.after(markup);
                                        }
                                        
                                        else*/
                                        
                                //console.log('markup: '+markup);
                                        
                                $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').getContentPane().append(markup); //chat.data.jspAPI.getContentPane().append(markup);
                                
                               // }
                                //else chat.data.jspAPI.getContentPane().append(markup);
                                
                                // As we added new content, we need to
                                // reinitialise the jScrollPane plugin:
                                
                                
                                //*
                                $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').reinitialise();//chat.data.jspAPI.reinitialise();
                                
                                $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').scrollToBottom(true); //chat.data.jspAPI.scrollToBottom(true);
                                //*/
                        },
                        
                        // This method requests the latest chats
                        // (since lastID), and adds them to the page.
                        
                        getChats : function(callback) { //function(chat_tokens,chat_keys,callback){
                            
                            var chat_tokens = chat.data.chat_tokens;
                            var chat_keys   = chat.data.chat_keys;
                            
                            //console.log('noActivity: '+chat.data.noActivity+' nextRequest: '+chat.data.nextRequest);
                            
                            //console.log(chat_tokens);
                            
                                $.ttPOST('getChats','chat_tokens='+JSON.stringify({ chat_tokens: chat_tokens }),function(r){ //+'&lastID='+chat.data.lastID
                                    
                                    var trimmed = $.trim(this);
                                    //console.log(trimmed);
                                    var data = jQuery.parseJSON(trimmed);
                                    
                                    var argstr = "page=getVouches&sesskey=" + encodeURIComponent(window.sesskey);
                                            
                                    PostAjaxRequest(function() {   
                                    var jsonobj = JSON.parse(this);
                                        
                                        var infos = data.infos;
                                        //console.log(infos);
                                        
                                        for (var chat_token in infos) {
                                            
                                            // get chat_key
                                            var chat_key = 'no key found';
                                            for (var k=0; k<chat_tokens.length; k++) {
                                                
                                                if (chat_tokens[k] == chat_token) {
                                                    chat_key = chat_keys[k];
                                                }
                                            }
                                            
                                            // chat info
                                            var info = infos[chat_token];
                                            var chatname = ttAESdecrypt(chat_key,info.enc_chat_name,info.enc_mode);
                                            //console.log(chatname);
                                            var chat_owner_id = info.chat_owner;
                                            //console.log(chatname);
                                            document.getElementById('groupView'+chat_token).innerHTML = chatname.substr(0,15);
                                            var ownerStr = document.createElement("a");
                                            ownerStr.setAttribute('href',ttPeoplePublicLink(info.chat_owner_info.email));
                                            ownerStr.innerHTML = "<p>Group Admin: "+info.chat_owner_info.username+" ("+info.chat_owner_info.email+")</p>";
                                            document.getElementById('titleDiv'+chat_token).innerHTML = "<h1>"+chatname+"</h1>";
                                            document.getElementById('titleDiv'+chat_token).appendChild(ownerStr);
                                            var anyInvite = ttAESdecrypt(chat_key,info.enc_chat_invite,info.enc_mode);
                                            //console.log(chatname+': '+anyInvite);
                                            // inviteDiv+chat_token
                                            var user_ids = chat.data.chat_token.user_ids;
                                            var user_active = chat.data.chat_token.user_active[chat_token];
                                            var user_enc_user_id = chat.data.chat_token.user_enc_user_id;
                                            /*for (var ukey in user_ids[chat_token]) {
                                                console.log(chatname+': '+user_ids[chat_token][ukey]);
                                            }*/
                                            if (anyInvite == '1' || info.chat_owner == '<?php echo $u->user_id; ?>') {
                                                
                                                
                                                    var voucheothers = jsonobj.voucheothers;
                                                    var voucheme     = jsonobj.voucheme;
                                                    
                                                    // only add the user if they are not already a member of the group
                                                    var inviteIds = [];
                                                    //for (var n=0; n<voucheothers.length; n++) {
                                                    //    if(user_ids[chat_token].indexOf(voucheothers[n].user['user_id']) < 0 || user_active[voucheothers[n].user['user_id']] == '0') {
                                                    //        var enc_user_id = '';
                                                    //        if (user_ids[chat_token].indexOf(voucheothers[n].user['user_id']) >= 0 && user_ids[chat_token].indexOf(voucheme[n].user['user_id']) >= 0) {
                                                    //            enc_user_id = user_enc_user_id[voucheothers[n].user['user_id']];
                                                    //        }
                                                    //        
                                                    //        //console.log('isactive: '+user_active[voucheothers[n].user['user_id']]);
                                                    //        inviteIds.push({user_id: voucheothers[n].user['user_id'],
                                                    //                        email:   voucheothers[n].user['email'],
                                                    //                        first_name: voucheothers[n].user['first_name'],
                                                    //                        last_name: voucheothers[n].user['last_name'],
                                                    //                        username: voucheothers[n].user['username'],
                                                    //                        enc_user_id: enc_user_id});
                                                    //    }
                                                    //}
                                                    
                                                    for (var n=0; n<voucheme.length; n++) {
                                                        if(user_ids[chat_token].indexOf(voucheme[n].user['user_id']) < 0 || user_active[voucheme[n].user['user_id']] == '0') {
                                                            var enc_user_id = '';
                                                            //console.log('voucheme length: '+voucheme[n]);
                                                            //console.log('voucheothers length: '+voucheothers[n]);
                                                            
                                                            try {
                                                                if (user_ids[chat_token].indexOf(voucheothers[n].user['user_id']) >= 0 && user_ids[chat_token].indexOf(voucheme[n].user['user_id']) >= 0) {
                                                                    enc_user_id = user_enc_user_id[voucheme[n].user['user_id']];
                                                                }
                                                            } catch(err) {
                                                                
                                                            }
                                                            
                                                            
                                                            //console.log('isactive: '+user_active[voucheme[n].user['user_id']]);
                                                            inviteIds.push({user_id: voucheme[n].user['user_id'],
                                                                            email:   voucheme[n].user['email'],
                                                                            first_name: voucheme[n].user['first_name'],
                                                                            last_name: voucheme[n].user['last_name'],
                                                                            username: voucheme[n].user['username'],
                                                                            enc_user_id: enc_user_id});
                                                        }
                                                    }
                                                    
                                                    //console.log(inviteIds[0].email);
                                                    document.getElementById('inviteDiv'+chat_token).innerHTML = "You can invite someone into your group if they have connected with you. These people have connected with you so far:";
                                                    for (var ukey in inviteIds) {
                                                        var str = inviteIds[ukey].username + " - ";
                                                        str += inviteIds[ukey].last_name + ", ";
                                                        str += inviteIds[ukey].first_name + " (";
                                                        str += inviteIds[ukey].email + ") ";
                                                        
                                                        var nlink = document.createElement("a");
                                                        nlink.innerHTML = "Invite";
                                                        nlink.className = "button button-small";
                                                        nlink.data_user_id = inviteIds[ukey].user_id;
                                                        nlink.data_enc_user_id = inviteIds[ukey].enc_user_id;
                                                        nlink.data_chat_token = chat_token;
                                                        nlink.data_chat_key = chat_key;
                                                        nlink.addEventListener('click',function() {
                                                            //sendRequest(this.data_user_id,this.data_chat_token,this.data_chat_key);
                                                            this.className = "hidden";
                                                            chat.addUserToChat(this.data_chat_token,this.data_chat_key,this.data_user_id,this.data_enc_user_id);
                                                            initChats(function() {});
                                                        },false);
                                                        
                                                        var userinfostr = document.createElement("div");
                                                        userinfostr.innerHTML = '<br />\n'+str;
                                                        
                                                        var ntable = document.createElement("table");
                                                        var nrow = document.createElement("tr");
                                                        var ncellstr = document.createElement("td");
                                                            ncellstr.appendChild(userinfostr);
                                                        var ncelllnk = document.createElement("td");
                                                            ncelllnk.appendChild(nlink);
                                                        
                                                        nrow.appendChild(ncellstr);
                                                        nrow.appendChild(ncelllnk);
                                                        ntable.appendChild(nrow);
                                                        
                                                        document.getElementById('inviteDiv'+chat_token).appendChild(ntable);
                                                        
                                                        //document.getElementById('inviteDiv'+chat_token).appendChild(userinfostr); 
                                                        //document.getElementById('inviteDiv'+chat_token).appendChild(nlink);
                                                    }
                                                    
                                                
                                            } else {
                                                document.getElementById('inviteDiv'+chat_token).innerHTML = "You are not able to invite others to join this group.";
                                            }
                                            
                                        }
                                        
                                        var chats = data.chats;
                                        //console.log(chats);
                                        
                                        var has_played = 0;
                                        
                                        // iterate through chat_tokens
                                        var incNoActivity = 1;
                                        for (var chat_token in chats) {
                                            
                                            var chat_key = 'no key found';
                                            for (var k=0; k<chat_tokens.length; k++) {
                                                
                                                if (chat_tokens[k] == chat_token) {
                                                    chat_key = chat_keys[k];
                                                }
                                            }
                                            
                                            
                                            // iterate through chat_lines
                                            
                                            for(var i=0;i<chats[chat_token].length;i++){
                                                
                                                var elem = document.getElementById('text'+chats[chat_token][i].chatline_token);
                                                
                                                if (typeof(elem) == 'undefined' || elem == null) {
                                                    
                                                    var playSound = document.getElementById('audioBox'+chat_token).checked;
                                                    if (has_played == 0 && playSound) {
                                                        var msgaudio = document.getElementById('msgaudio');
                                                        msgaudio.play();
                                                        has_played = 1;
                                                    
                                                        chat.data.noActivity = 0;
                                                        
                                                    } else {
                                                        //chat.data.noActivity++;
                                                        incNoActivity = 1;
                                                    }
                                                    
                                                    
                                                    chat.addChatLine(chat_token,chat_key,chats[chat_token][i]);
                                                    try {
                                                        chat.decryptLine(chat_token,chat_key,chats[chat_token][i]);
                                                    } catch(err) {
                                                        // user is no longer in group
                                                        //console.log(err.stack);
                                                    }
                                                    
                                                    
                                                    
                                                    
                                                    // store recent activity
                                                    if(chats[chat_token].length){
                                                        
                                                        //console.log('chats_chat_token_length: '+chats[chat_token].length);
                                                        
                                                        var len = chats[chat_token].length;
                                                        
                                                        if (chat.data.chat_token.lastID < chats[chat_token][len-1].chatline_id) {
                                                            
                                                            //chat.data.noActivity = 0;
                                                            chat.data.chat_token.lastID = chats[chat_token][len-1].chatline_id;
                                                            
                                                            //alert(chat.data.lastID);
                                                            
                                                        } else {
                                                            
                                                            //chat.data.noActivity++;
                                                        }
                                                        
                                                    }
                                                    else{
                                                            // If no chats were received, increment
                                                            // the noActivity counter.
                                                            
                                                            //chat.data.noActivity++;
                                                    }
                                                    
                                                    
                                                }
                                                
                                                
                                                
                                                
                                                
                                                
                                            } // end iterate through chat_lines
                                            
                                            
                                            
                                            /*
                                            $('#chatLineHolder' + chat_token).jScrollPane({
                                                verticalDragMinHeight: 12,
                                                verticalDragMaxHeight: 12
                                            }).data('jsp').reinitialise();//chat.data.jspAPI.reinitialise();
                                            
                                            $('#chatLineHolder' + chat_token).jScrollPane({
                                                verticalDragMinHeight: 12,
                                                verticalDragMaxHeight: 12
                                            }).data('jsp').scrollToBottom(true); //chat.data.jspAPI.scrollToBottom(true);
                                            //*/
                                            
                                            //console.log('chat_token lastID: '+chat.data.chat_token.lastID);
                                            
                                            if(!chat.data.chat_token.lastID){
                                                    //chat.data.jspAPI.getContentPane().html('<p class="noChats">No chats yet</p>');
                                                    $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').getContentPane().html('<p class="noChats">No chats yet</p>');
                                            }
                                            
                                            if (chat.data.noActivity == 1) {
                                                $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').reinitialise();//chat.data.jspAPI.reinitialise();
                                                
                                                $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').scrollToBottom(true); //chat.data.jspAPI.scrollToBottom(true);
                                            }
                                            
                                            
                                            
                                        } // end iterate through chat_tokens
                                        
                                        if (incNoActivity == 1) {
                                            chat.data.noActivity++;
                                        }
                                        
                                        
                                        
                                        
                                        
                                        
                                        // Setting a timeout for the next request,
                                        // depending on the chat activity:
                                        
                                        // 5 seconds
                                        chat.data.nextRequest = 10000;
                                        
                                        // 8 seconds
                                        if(chat.data.noActivity > 3){
                                                chat.data.nextRequest = 10000;
                                                
                                                
                                                
                                        }
                                        
                                        // 10 seconds
                                        if(chat.data.noActivity > 10){
                                                chat.data.nextRequest = 12000;
                                        }
                                        
                                        // 15 seconds
                                        if(chat.data.noActivity > 100){
                                                chat.data.nextRequest = 15000;
                                        }
                                        
                                        // 30 seconds
                                        if(chat.data.noActivity > 150){
                                                chat.data.nextRequest = 20000;
                                        }
                                        
                                        // 1 min
                                        if (chat.data.noActivity > 200) {
                                                chat.data.nextRequest = 30000;
                                        }
                                        
                                        /*if (chat.data.noActivity > 300) {
                                                chat.data.nextRequest = 300000;
                                        }*/
                                        
                                        callback();
                                
                                        /*setTimeout(function() {
                                            //console.log('noActivity: '+chat.data.noActivity+' nextRequest: '+chat.data.nextRequest);
                                            //chat.getChats(chat_tokens,chat_keys,function() {});
                                            chat.getChats(function() {});
                                        },chat.data.nextRequest);*/
                                        
                                    },'ajaxHTML.php',argstr);
                                });
                        },
                        
                        getKeys : function(callback) {
                            
                            //alert('gets to getKeys');
                            
                            $.ttPOST('getKeys','',function(r) {
                                
                                var trimmed = $.trim(this);
                                //console.log(trimmed);
                                var data = jQuery.parseJSON(trimmed);
                                
                                var keys = data.keys;
                                
                                var chat_keys = [];
                                var chat_tokens = [];
                                
                                for (n=0; n<keys.length; n++) {
                                    
                                    //console.log('numkeys: '+keys.length);
                                    
                                    var enc_chat_token = keys[n].enc_chat_token;
                                    var enc_chat_key   = keys[n].enc_chat_key;
                                    var mode           = keys[n].enc_mode;
                                    
                                    //console.log('getKeys mode: '+mode);
                                    
                                    chat_keys[n]   = ttRSAdecrypt(enc_chat_key);
                                    var chat_key = chat_keys[n];
                                    chat_tokens[n] = ttAESdecrypt(chat_key,enc_chat_token,mode);
                                    var chat_token = chat_tokens[n];
                                    
                                    
                                    
                                    //console.log('chat_key   ' + n + ': ' + chat_key);
                                    //console.log('chat_token ' + n + ': ' + chat_token);
                                    
                                    
                                    if (typeof window['msg' + chat_token] == 'undefined' || window['msg' + chat_token] == 0) {
                                        
                                        chat.newMsgWindow(chat_token,chat_key);
                                        chat.login(data.name,data.gravatar,chat_token);
                                        
                                        // store enc_chat_token for logout
                                        var enc_chat_token_input = document.createElement("input");
                                        enc_chat_token_input.type = "hidden";
                                        enc_chat_token_input.name = "enc_chat_token";
                                        //enc_chat_token_input.id   = "enc_chat_token";
                                        enc_chat_token_input.value = enc_chat_token;
                                        document.getElementById('form' + chat_token).appendChild(enc_chat_token_input);
                                        
                                        
                                        
                                        chat.init(chat_token);
                                        
                                        
                                            
                                        addChatToNav(chat_token); // no more email, username
                                        //console.log('called addChatToNav for '+chat_token);
                                            
                                        // save existence of object for later reference
                                        window['msg' + chat_token] = 1; // done in newMsgWindow()
                                    }
                                    
                                }
                                
                                chat.data.chat_tokens = chat_tokens;
                                chat.data.chat_keys   = chat_keys;
                                
                                
                                
                                
                                callback();
                                
                                //alert(keys.length);
                                
                                
                                
                            });
                            
                            
                        },
                        
                        // Requesting a list with all the users                        
                        getUsers : function(callback) { //function(chat_tokens,chat_keys,callback){
                            
                            var chat_tokens = chat.data.chat_tokens;
                            var chat_keys   = chat.data.chat_keys;
                            
                            //for (var key in chat_tokens) {
                            //    console.log('chat_token: '+chat_tokens[key]);
                            //}
                            
                            
                                $.ttPOST('getUsers','chat_tokens='+JSON.stringify({ chat_tokens: chat_tokens }),function(r){
                                    
                                        var trimmed = $.trim(this);
                                        //console.log(trimmed);
                                        var data = jQuery.parseJSON(trimmed);
                                        
                                        var emptychattokens = data.emptychattokens;
                                        for (var n=0; n<emptychattokens.length; n++) {
                                            var chat_token = emptychattokens[n];
                                            document.getElementById('logout'+chat_token).click();
                                        }
                                        
                                        var users = data.users;
                                        
                                        
                                        var enc_users = [];
                                        var user_ids = {};
                                        //var user_active = {};
                                        var user_enc_user_id = {};
                                        var user_chat_active = {};
                                        
                                        var user_mapping = {};
                                        
                                        // iterate through chat_tokens
                                        for (var chat_token in users) {
                                            
                                            var user_active = {};
                                            
                                            // get current chat_key
                                            var chat_key = 'key not found';
                                            for (var k=0; k<chat_tokens.length; k++) {
                                                
                                                if (chat_tokens[k] == chat_token) {
                                                    chat_key = chat_keys[k];
                                                    break;
                                                }
                                            }
                                            
                                            
                                            // iterate through users in each chat
                                            for (var n=0; n<users[chat_token].length; n++) {
                                                
                                                //user_ids[n].chat_token = chat_tokens[n];
                                                //user_ids[chat_tokens[n]]
                                            
                                                // iterate through users in each chat
                                                //var enc_users = data[chat_token][n].users;
                                                
                                                user_ids[chat_token] = [];
                                                user_mapping[chat_token] = [];
                                                
                                                var enc_users = users[chat_token];
                                                var my_enc_user_id = '';
                                                for(var i=0; i< enc_users.length;i++){
                                                        if(enc_users[i]){
                                                            
                                                            //console.log('enc_mode: '+enc_users[i].enc_mode);
                                                            var user = ttAESdecrypt(chat_key,enc_users[i].enc_user_id,enc_users[i].enc_mode);
                                                            
                                                            if (user == '<?php echo $u->user_id; ?>') {
                                                                //console.log('form1 enc_user_id: '+document.getElementById('form' + chat_token).enc_user_id);
                                                                
                                                                // keep for logout
                                                                //var enc_user_id_input = document.createElement("input");
                                                                //enc_user_id_input.type = "hidden";
                                                                //enc_user_id_input.name = "enc_user_id";
                                                                //enc_user_id_input.value = enc_users[i].enc_user_id;
                                                                
                                                                //console.log('enc_user_id: '+enc_user_id_input.value);
                                                                //document.getElementById('form' + chat_token).enc_user_id.value = enc_users[i].enc_user_id
                                                                
                                                                document.getElementById('logout'+chat_token).data_enc_user_id = enc_users[i].enc_user_id;
                                                                document.getElementById('logout'+chat_token).data_enc_isactive = ttAESencrypt(chat_key,'0',enc_users[i].enc_mode);
                                                                
                                                                //document.getElementById('form' + chat_token).appendChild(enc_user_id_input);
                                                                //console.log('form2 enc_user_id: '+document.getElementById('form' + chat_token).enc_user_id.value);
                                                            }
                                                            
                                                            //user_ids[i] = user;
                                                            
                                                            //user_ids[n].id[i] = user;
                                                            user_ids[chat_token].push(user);
                                                            
                                                            user_mapping[chat_token].push({user_id:user,
                                                                                          enc_user_id:enc_users[i].enc_user_id});
                                                            
                                                            var isactive = ttAESdecrypt(chat_key,enc_users[i].enc_isactive,enc_users[i].enc_mode);
                                                            user_active[user] = isactive;
                                                            //console.log('isactive: '+isactive);
                                                            
                                                            user_enc_user_id[user] = enc_users[i].enc_user_id;
                                                            
                                                            
                                                            //console.log('chat: ' + chat_token + ' with user: ' + user);
                                                            
                                                            // save for logout
                                                            if (user == <?php echo $u->user_id; ?>) {
                                                                my_enc_user_id = enc_users[i].enc_user_id;
                                                            }
                                                            
                                                        }
                                                        
                                                        
                                                } // end iterate through enc_users
                                                
                                                
                                                
                                                
                                                
                                            } // end iterate through each user in chat
                                            
                                            user_chat_active[chat_token] = user_active;
                                            
                                        }// end iterate through chat_tokens
                                        

                                        chat.data.chat_token.user_ids = user_ids;
                                        chat.data.chat_token.user_active = user_chat_active; //user_active;
                                        chat.data.chat_token.user_enc_user_id = user_enc_user_id;
                                        
                                        
                                        //alert(JSON.stringify({user_ids:user_ids}));
                                        
                                        //console.log('user_ids: '+user_ids);
                                        
                                        $.ttPOST('getUserInfo','user_ids='+JSON.stringify({ user_ids: user_ids,
                                                                                          user_mapping: user_mapping }),function(r) {
                                                        
                                            //console.log(this);
                                            var trimmed_user = $.trim(this);
                                            //console.log(trimmed_user);
                                            var user_data    = jQuery.parseJSON(trimmed_user);
                                            
                                            //alert(user_data.name + ' ' + user_data.email);
                                            var usersHTML = {};
                                            var user_chats = {};
                                            
                                            //console.log('user data: ' + user_data);
                                            
                                            for (var chat_token in user_data) {
                                                
                                                usersHTML[chat_token] = [];
                                                user_chats[chat_token] = [];
                                                
                                                for (var n=0; n<user_data[chat_token].length; n++) {
                                                
                                                    //alert(user_data[n].name + ' ' + user_data[n].email);
                                                    
                                                    var user_id = user_data[chat_token][n].user_id;
                                                    var params = {name: user_data[chat_token][n].name,
                                                                  email: user_data[chat_token][n].email,
                                                                  gravatar: user_data[chat_token][n].gravatar,
                                                                  active: user_active[user_id]};
                                                    
                                                    usersHTML[chat_token].push(chat.render('user',params));
                                                    
                                                    //console.log('user data: '+user_data[chat_token][n]);
                                                    
                                                    if (typeof document.getElementById('form' + chat_token).elements['pubkey'+user_data[chat_token][n].user_id] == 'undefined')  {
                                                        
                                                        
                                                        //user_chats[chat_token].push(user_data[chat_token][n].user_id);
                                                        user_chats[chat_token].push(user_data[chat_token][n]);
                                                        
                                                        
                                                        //alert('added user '+user_ids[0]);
                                                    } else {
                                                        //(function() {callback();})();
                                                        //callback();
                                                    }
                                                    
                                                }
                                            }
                                            
                                            
                                            
                                            chat.addUserToChatForm({chat_tokens: chat_tokens,
                                                                        chat_keys: chat_keys},user_chats,function() {
                                                                            
                                                for (var chat_token in user_data) {
                                                    
                                                    
                                                    var message = '';
                                                    if(user_ids[chat_token].length<1){
                                                            message = 'No one is online';
                                                    }
                                                    else {
                                                            message = user_data[chat_token].length+' '+(user_data[chat_token].length == 1 ? 'person':'people')+' online';
                                                    }
                                                    
                                                    usersHTML[chat_token].push('<p class="count">'+message+'</p>');
                                                    
                                                    $('#chatUsers'+chat_token).html(usersHTML[chat_token].join(''));
                                                    
                                                    
                                                    
                                                }
                                                                        
                                                                        
                                                //setTimeout(callback,15000);
                                                                        
                                                                        
                                                callback();
                                                                        
                                            }); // chatobj includes chat_token and chat_key
                                            
                                            
                                            
                                            
                                        });
                                        
                                        
                                        
                                        
                                });
                        },
                        
                        newChat : function(rec_id) { // no longer used
                            // never called any more
                            // replaced by createGroup in groupClass
                            
                            // create chat token
                            var chat_token_512 = getRandKey(512);
                            var chat_token = b64tohex(chat_token_512);
                            
                            // -----------------------------------------------------------------------------------------
                                    
                            var chat_key_512 = getRandKey(512);
                            var chat_key = b64tohex(chat_key_512);
                            
                            var enc_chat_key 	= ttRSAencryptSelf(chat_key);
                            var enc_chat_token 	= ttAESencrypt(chat_key,chat_token,1);
                            
                            var enc_user_id = ttAESencrypt(chat_key,'<?php echo $u->user_id; ?>',1);
                            
                            //alert('pre enc_chat_token: ' + enc_chat_token + ' enc_chat_key: ' + enc_chat_key + ' chat_key: ' + chat_key + ' chat_token: ' + chat_token);
                            
                            $.ttPOST('login','user_id='+ '<?php echo $u->user_id; ?>' +'&enc_user_id='+ enc_user_id +'&chat_token='+ chat_token +'&enc_chat_token='+enc_chat_token+'&enc_chat_key='+enc_chat_key,function(r){
                            
                                
                                        //alert('success enc_chat_token: ' + enc_chat_token + ' enc_chat_key: ' + enc_chat_key + ' chat_key: ' + chat_key + ' chat_token: ' + chat_token);
                                        var trimmed = $.trim(this);
                                        //alert(trimmed);
                                        var data = jQuery.parseJSON(trimmed);
                                        //var data = trimmed; //.replace('\\','');
                                        //alert(data);
                                        
                                        if(data.error){
                                                chat.displayError(data.error);
                                        }
                                        else {
                                                //alert('name: ' + data.name + ' gravatar: ' + data.gravatar + ' chat_token: ' + chat_token);
                                                
                                                //chat.login(data.name,data.gravatar,chat_token);
                                                //chat.init(chat_token);
                                        }
                                        
                                        chat.addUserToChat(chat_token,chat_key,rec_id);
                                        
                                        
                            });
                        },
                        
                        addUserToChat : function(chat_token,chat_key,rec_id,old_enc_rec_id) {
                            
                            //var mode = 'AES-CTR-256';
                            //var mode = 'AES-CTR-128';
                            var mode = '<?php echo prefs::getSymCipher($u->user_id); ?>';
                            
                            var enc_user_id    = ttAESencrypt(chat_key,rec_id+'',mode);
                            var enc_chat_token = ttAESencrypt(chat_key,chat_token,mode);
                            var enc_isactive   = ttAESencrypt(chat_key,'1',mode);
                            
                            //var rec_rsa = publicPEMtoRSAKey(pubkey);
                            //var enc_chat_key = rec_rsa.encrypt(chat_key);
                            ttRSAencrypt(chat_key,chat_token,rec_id,function(enc_chat_key) {
                                
                                
                                var argstr = 'user_id='+ rec_id;
                                argstr += '&enc_mode=' + mode;
                                argstr += '&enc_user_id=' + enc_user_id;
                                argstr += '&old_enc_user_id=' + old_enc_rec_id;
                                argstr += '&chat_token=' + chat_token;
                                argstr += '&enc_chat_token=' + enc_chat_token;
                                argstr += '&enc_chat_key=' + enc_chat_key;
                                argstr += '&enc_isactive=' + enc_isactive;
                                
                                
                                
                                $.ttPOST('addUserToGroup',argstr,function(r){
                                
                                    //alert('success enc_chat_token: ' + enc_chat_token + ' enc_chat_key: ' + enc_chat_key + ' chat_key: ' + chat_key + ' chat_token: ' + chat_token);
                                    var trimmed = $.trim(this);
                                    //alert(trimmed);
                                    var data = jQuery.parseJSON(trimmed);
                                    //var data = trimmed; //.replace('\\','');
                                    //alert(data);
                                    
                                    if(data.error){
                                            chat.displayError(data.error);
                                    }
                                    else {
                                            //alert('name: ' + data.name + ' gravatar: ' + data.gravatar + ' chat_token: ' + chat_token);
                                            
                                            //chat.login(data.name,data.gravatar,chat_token);
                                            //chat.init(chat_token);
                                    }
                                });
                            
                            
                            
                            });
                            
                            
                            
                        },
                        
                        newMsgWindow : function(chat_token,chat_key) {
                                    
                                
                                    
                                // save existence of object for later reference
                                window['msg' + chat_token] = 1;
                                    
                                // chat_token wrapper
                                var chatwrapper = document.createElement("div");
                                chatwrapper.setAttribute('id','msg'+chat_token);
                                chatwrapper.className = "hidden";
                                
                                    // title bar (for chat name)
                                    var titleDiv = document.createElement("div");
                                    titleDiv.setAttribute('id','titleDiv'+chat_token);
                                    
                                    // invite div
                                    var inviteDiv = document.createElement("div");
                                    inviteDiv.setAttribute('id','inviteDiv'+chat_token);
                                    inviteDiv.className = "scrollDiv";
                                    
                                        // create chat window
                                        var cont = document.createElement("div");
                                        cont.setAttribute('id','chatContainer'+chat_token);
                                        cont.className = "chatContainer";
                                        
                                            //top bar
                                            var topbar = document.createElement("div");
                                            topbar.setAttribute('id','chatTopBar'+chat_token);
                                            topbar.className = "chatTopBar rounded";
                                            
                                            //line holder **********
                                            var lineholder = document.createElement("div");
                                            lineholder.setAttribute('id','chatLineHolder'+chat_token);
                                            lineholder.className = "chatLineHolder";
                                            
                                            
                                            //chat Users
                                            var chatusers = document.createElement("div");
                                            chatusers.className = "chatUsers rounded";
                                            chatusers.setAttribute('id','chatUsers'+chat_token);
                                            
                                            // bottom bar
                                            var bottombar = document.createElement("div");
                                            bottombar.setAttribute('id','chatBottomBar'+chat_token);
                                            bottombar.className = "chatBottomBar rounded";
                                            
                                                // tip (triangle)
                                                var tip = document.createElement("div")
                                                tip.className = "tip";
                                                
                                                // submission form
                                                var subform = document.createElement("form");
                                                //subform.setAttribute('method','post');
                                                subform.setAttribute('id','form'+chat_token);
                                                subform.className = "submitForm";
                                                //subform.setAttribute('onkeypress',"return event.keyCode != 13");
                                                
                                                    // chat key
                                                    var key_elem = document.createElement("input");
                                                    key_elem.setAttribute('id','chatkey'+chat_token);
                                                    key_elem.setAttribute('name','chat_key');
                                                    key_elem.setAttribute('type','hidden');
                                                    key_elem.setAttribute('value',chat_key);
                                                    
                                                
                                                    // submission text
                                                    var chatText = document.createElement("input");
                                                    chatText.setAttribute('id','chatText'+chat_token);
                                                    chatText.setAttribute('type','text');
                                                    chatText.setAttribute('autocomplete','off');
                                                    chatText.setAttribute('name','chatText');
                                                    chatText.className = "chatText rounded";
                                                    chatText.setAttribute('maxlength','1000');
                                                    
                                                    // submission button
                                                    var chatButton = document.createElement("input");
                                                    chatButton.setAttribute('type','submit'); // button
                                                    chatButton.className = "blueButton";
                                                    chatButton.setAttribute('value','Submit');
                                                    //chatButton.setAttribute('onClick',"sendmsg(" + chat_token + ");"); // *********************
                                                    
                                                    // audio checkbox
                                                    var audioBox = document.createElement("input");
                                                    audioBox.setAttribute('id','audioBox'+chat_token);
                                                    audioBox.setAttribute('type','checkbox');
                                                    
                                                    // audio checkbox label
                                                    var audioBoxLbl = document.createElement("label");
                                                    audioBoxLbl.setAttribute('for','audioBox');
                                                    audioBoxLbl.innerHTML = 'Play sound for incoming messages:';
                                                
                                                subform.appendChild(chatText);
                                                subform.appendChild(chatButton);
                                                subform.appendChild(key_elem);
                                                
                                            bottombar.appendChild(tip);
                                            bottombar.appendChild(subform);
                                            
                                            
                                        cont.appendChild(topbar);
                                        cont.appendChild(lineholder);
                                        cont.appendChild(chatusers);
                                        cont.appendChild(bottombar);
                                        cont.appendChild(document.createElement("p"));
                                        cont.appendChild(audioBoxLbl);
                                        cont.appendChild(audioBox);
                                        
                                    chatwrapper.appendChild(titleDiv);
                                    chatwrapper.appendChild(cont);
                                    chatwrapper.appendChild(inviteDiv);
                                    
                                    // attach this chat to the window
                                    //document.getElementById("message").appendChild(chatwrapper);
                                    document.getElementById("groupView").appendChild(chatwrapper);
                                    
                                    
                                    
                                    
                                    
                                    
                                /*return {chat_token  :chat_token,
                                        chat_key    :chat_key};*/
                        },
                        
                        addUserToChatForm : function(chatobj,user_chats,callback) { // chatobj includes chat_token and chat_key
                            /*chatobj = {chat_token  :chat_token,
                                        chat_key    :chat_key};*/
                                    
                                /*if (chatobj == '') {
                                        chatobj = chat.newMsgWindow();
                                }*/
                                
                                
                                
                                chat.data.finishedUserData = false;
                                
                                var chat_tokens = chatobj.chat_tokens;
                                var chat_keys   = chatobj.chat_keys;
                                
                                
                                for (var c=0; c<chat_tokens.length; c++) {
                                    
                                    var chat_token = chat_tokens[c];
                                    var chat_key   = chat_keys[c];
                                    
                                    var thisform = document.getElementById('form' + chat_token);
                                    
                                    
                                    //console.log('chat_token: '+chat_token);
                                    //for (var key in user_chats) {
                                    //    console.log('user_chats key: ' + key);
                                    //}
                                    
                                    if ((typeof user_chats[chat_token] != 'undefined') && (user_chats[chat_token] != null)) {
                                        
                                        for (var n=0; n<user_chats[chat_token].length; n++) {
                                            
                                            
                                                
                                                var jsonobj = user_chats[chat_token][n];
                                                
                                                var username = jsonobj.name;
                                                var gravatar = jsonobj.gravatar;
                                                var email = jsonobj.email;
                                                var pubkey = jsonobj.pubkey;
                                                var rec_id = jsonobj.user_id;
                                                
                                            // if user is already a part of the form, don't add again
                                            if (typeof thisform['pubkey'+rec_id] == "undefined") {
                                                
                                            
                                                
                                                // store public key
                                                var pk = document.createElement("input");
                                                pk.type = "hidden";
                                                pk.name = "pubkey"+rec_id;
                                                    
                                                var rec_email = document.createElement("input");
                                                rec_email.type = "hidden";
                                                rec_email.name = "email"+rec_id;
                                                    
                                                var rec_username = document.createElement("input");
                                                rec_username.type = "hidden";
                                                rec_username.name = "username"+rec_id;
                                                
                                                var rec_gravatar = document.createElement("input");
                                                rec_gravatar.type = "hidden";
                                                rec_gravatar.name = "gravatar"+rec_id;
                                                
                                                
                                                
                                                //t.innerHTML = "<span title=\"" + email + "\"><h3>Chat with " + username + "</h3><a class=\"button button-small\" href=\"#\" onClick=\"closeChats('message');\">Close</a></span>";
                                                pk.value = pubkey; // n;
                                                rec_email.value = email;
                                                rec_username.value = username;
                                                rec_gravatar.value = gravatar;
                                                
                                                thisform.appendChild(rec_gravatar);
                                                thisform.appendChild(pk);
                                                thisform.appendChild(rec_email);
                                                thisform.appendChild(rec_username);
                                                
                                                
                                            } // end if user form info doesn't already exist  
                                                
                                        }// end loop through user_chat data
                                        
                                        
                                        
                                        
                                        
                                    } // end if user_chat[chat_token] has data
                                    

                                    
                                    
                                } // end loop through chat tokens
                                    
                                
                                    
                                    
                                    
                                
                                        
                                        
                                        
                                        
                                        //jsonobj = jQuery.parseJSON(trim11(this));
                                        
                                        
                                        
                                        //alert('added user ' + rec_username);
                                        
                                        chat.data.finishedUserData = true;
                                        callback();
                                        
                                        
                                        /*if (typeof window['msg' + chat_token] == 'undefined') {
                                            
                                            addChatToNav('msg' + chat_token); // no more email, username
                                            
                                            // save existence of object for later reference
                                            //window['msg' + chat_token] = 1; // done in newMsgWindow()
                                        }*/
                                        
                               // });
                                //},'ajaxgetmsguserdata.php','user_id='+rec_id);
                                
                                
                        },
                        
                        // This method displays an error message on the top of the page:
                        
                        displayError : function(msg){
                                var elem = $('<div>',{
                                        id	: 'chatErrorMessage',
                                        html	: msg
                                });
                                
                                elem.click(function(){
                                        $(this).fadeOut(function(){
                                                $(this).remove();
                                        });
                                });
                                
                                setTimeout(function(){
                                        elem.click();
                                },5000);
                                
                                elem.hide().appendTo('body').slideDown();
                        }
                };
                
                
                // A custom jQuery method for placeholder text:
                
                $.fn.defaultText = function(value){
                        
                        var element = this.eq(0);
                        element.data('defaultText',value);
                        
                        element.focus(function(){
                                if(element.val() == value){
                                        element.val('').removeClass('defaultText');
                                }
                        }).blur(function(){
                                if(element.val() == '' || element.val() == value){
                                        element.addClass('defaultText').val(value);
                                }
                        });
                        
                        return element.blur();
                }
                
                
                
                
                
            function chatCallback() {
                
                //console.log('calls chatCallback');
                
                initChats(function() {
                        chat.getChats(function(){});
                    });
                
                //console.log('noActivity: '+chat.data.noActivity+' nextRequest: '+chat.data.nextRequest);
                
                setTimeout(function() {
                           chatCallback();
                           //console.log(chat.data.nextRequest);
                            },chat.data.nextRequest);
                
            }
            
            
            function initChats(callback) {
                
                //console.log('calls initChats');
                
                chat.getKeys(function() {
                    
                    var chat_tokens = chat.data.chat_tokens;
                    
                    if (chat_tokens.length > 0) {
                        
                        //chat.getUsers(chat_tokens,chat_keys,function() {
                        chat.getUsers(function() {    
                            //chat.getChats(chat_tokens,chat_keys,function() {
                              
                            //chat.getChats(function() {  
                                //setTimeout(callback,chat.data.nextRequest);
                            //});
                            
                            callback();
                            
                            
                            
                            
                        });
                    }
                    
                });
                
                
                
            }
            
            $(document).ready(function(){
	
                    // Run the init method on document ready:
                    // chat.init();
                    
                    // Self executing timeout functions
                                

                    
                    /*
                    (function getKeysTimeoutFunction(){
                            chat.getKeys(getKeysTimeoutFunction);
                    })();
                    //*/
                    
                    //window.setInterval(chat.getKeys,10000);

                    /*initChats(function() {
                        chat.getChats(function() {});
                    });*/
                    
                    //console.log('opens ready function');
                    
                    chatCallback();
                    
                    //setTimeout(initChats(function() {}),chat.data.nextRequest);
                    
                    /*window.setInterval(function() {
                        initChats(function() {});
                    },60000);*/

                    
            });    
                
            
            </script>
            
            <audio src="/audio/button-9.mp3" preload="auto" id="msgaudio"></audio>
            
            
            
            <?php
        }
    }



    static function getadminjs()
    {
        if (commonPHP::checkisadmin())
        {
            ?>
            
            <script type="text/javascript">
            
            function navBlogEntry() {
    
                var argstr = "page=blogentry&sesskey=" + encodeURIComponent(window.sesskey);
                
                PostAjaxRequest(function() {
                    
                    document.body.className = "right-sidebar";
                    document.getElementById("main-wrapper").innerHTML = this;
                    
                    ttinit();
                    
                    $('#blogform').ajaxForm();
                    
                },'ajaxHTML.php',argstr);
            }
            
            function fileSelected(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
        
                    reader.onload = function (e) {
                        $('#blogimgpreview')
                            .attr('src', e.target.result) //.width(150)
                            .height(100)
                            .attr('class','visible');
                    };
        
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            function submitBlog(blogform) {
                
                //$(blogform).find("input[name='sesskey']").val(encodeURIComponent(window.sesskey));
                blogform.sesskey.value = encodeURIComponent(window.sesskey);
                $(blogform).ajaxSubmit({
                    url: 'ajaxpostblog.php',
                    type: 'post',
                    success: function(responseText, statusText)  {
                        // for normal html responses, the first argument to the success callback
                        // is the XMLHttpRequest object's responseText property
                    
                        // if the ajaxSubmit method was passed an Options Object with the dataType
                        // property set to 'xml' then the first argument to the success callback
                        // is the XMLHttpRequest object's responseXML property
                    
                        // if the ajaxSubmit method was passed an Options Object with the dataType
                        // property set to 'json' then the first argument to the success callback
                        // is the json data object returned by the server
                    
                        //alert('status: ' + statusText + '\n\nresponseText: \n' + trim11(responseText) +
                        //   '\n\nThe output div should have already been updated with the responseText.');
                    
                        //alert(trim11(responseText));
                    
                        if (trim11(responseText) == "success!") {
                            navBlogEntry();
                        }
                        
                        //document.getElementById("main-wrapper").innerHTML = e;
                    
                    }
                });
                
                
            }
            
            
                
            </script>

            
            
            <?php
        }
    }

}



?>
