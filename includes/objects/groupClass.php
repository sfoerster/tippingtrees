
<?php

require('../includes/common/common.php');

class groupClass
{

    static function getCreateForm()
    {
        $s = $_SESSION['user'];
	$u = unserialize($s);
        
        ?>
            <script type="text/javascript">
                
                function createGroup() {
                    
                    var userVars = document.groupCreate;
                    
                    //var mode    = 'AES-CTR-256';
                    //var mode    = 'AES-CTR-128';
                    var mode = '<?php echo prefs::getSymCipher($u->user_id); ?>';
                            
                    var grpKey_512  = getRandKey(512);
                    var grpKey = b64tohex(grpKey_512);
                    
                    var chat_token_512 = getRandKey(512);
                    var chat_token = b64tohex(chat_token_512);
                    var enc_chat_token = ttAESencrypt(grpKey,chat_token,mode);
                    
                    var enc_chat_key    = ttRSAencryptSelf(grpKey);
                    var enc_chat_name   = ttAESencrypt(grpKey,userVars.gname.value,mode);
                    var enc_user_id     = ttAESencrypt(grpKey,'<?php echo $u->user_id; ?>',mode);
                    var enc_chat_owner  = ttAESencrypt(grpKey,'<?php echo $u->user_id; ?>',mode);
                    
                    var enc_isactive    = ttAESencrypt(grpKey,'1',mode);
                    
                    var chat_invite = '';
                    if (userVars.anyInvite.checked) {
                        chat_invite = '1';
                    } else {
                        chat_invite = '0';
                    }
                    var enc_chat_invite = ttAESencrypt(grpKey,chat_invite,mode);
                    
                    var owner_signature = ttRSAsign(chat_token);
                    
                    var argstr = 'user_id='      + '<?php echo $u->user_id; ?>';
                    argstr += '&enc_mode='       + mode;
                    argstr += '&enc_user_id='    + enc_user_id;
                    argstr += '&chat_token='     + chat_token;
                    argstr += '&enc_chat_token=' + enc_chat_token;
                    argstr += '&enc_chat_key='   + enc_chat_key;
                    argstr += '&enc_chat_name='  + enc_chat_name;
                    argstr += '&chat_owner='     + '<?php echo $u->user_id; ?>'; //chat_owner;
                    argstr += '&enc_chat_owner=' + ttAESencrypt(grpKey,'<?php echo $u->user_id; ?>',mode);
                    argstr += '&enc_chat_invite='+ enc_chat_invite;
                    argstr += '&owner_signature='+ owner_signature;
                    argstr += '&enc_isactive='   + enc_isactive;
                    
                    //console.log(argstr);
                    
                    $.ttPOST('login',argstr,function(r){
                            
                                
                        //alert('success enc_chat_token: ' + enc_chat_token + ' enc_chat_key: ' + enc_chat_key + ' chat_key: ' + chat_key + ' chat_token: ' + chat_token);
                        var trimmed = $.trim(this);
                        //console.log(trimmed);
                        var data = jQuery.parseJSON(trimmed);
                        //var data = trimmed; //.replace('\\','');
                        //console.log(data);
                        
                        userVars.gname.value = "";
                        
                        if (data.status == 0) {
                            statusMessage(data.error);
                        } else if (data.status == 1) {
                            initChats(function() {});
                        }
                        
                        
                        
                        /*if(data.error){
                                chat.displayError(data.error);
                        }
                        else {
                                //alert('name: ' + data.name + ' gravatar: ' + data.gravatar + ' chat_token: ' + chat_token);
                                
                                //chat.login(data.name,data.gravatar,chat_token);
                                //chat.init(chat_token);
                        }*/
                        
                        //chat.addUserToChat(chat_token,chat_key,rec_id);
                                        
                                        
                    });
                    
                }
                
            </script>
            
            <form name="groupCreate" id="groupCreate" onsubmit="return false;">
                
                <h1>Create a New Group:</h1>
                <p>Group Name:<input type="text" class="rounded" size="50" maxlength="100" name="gname" />
                <p>Any member can invite into others group:<input type="checkbox" name="anyInvite" /></p>
                <p><input type="button" class="button" name="groupCreateBtn" value="Create" onclick="createGroup();" /></p>
                <p>Posts are encrypted before leaving your computer and can only be decrypted by other group members.</p>
                
            </form>
            
        <?php
    }
    



}

?>
