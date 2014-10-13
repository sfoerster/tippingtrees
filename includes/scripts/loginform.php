<script type="text/javascript">
    
    function loginfinish() {
        
        userVars = document.logform;
        
        
        // create a random key to store in the $_SESSION
        var rng = new SecureRandom();
        var randkey = new BigInteger(512,rng);
        //userVars.sesskey.value = hex2b64(randkey.toString(16));
        userVars.sesskey.value = randkey.toString(16);
        
        // encrypt password to store in cookie for loading the user private key
        var aes = new pidCrypt.AES.CTR();
        var encpass = aes.encryptText(userVars.passwd.value,userVars.sesskey.value,{nbits:256});
        ProcessCookie('save','encpass',encpass);
        
        // hash password before submitting to server
        userVars.pass.value = pidCrypt.SHA512(userVars.passwd.value);
        
        // clear cleartext password before sending form to server
        var randnum = new BigInteger(512,rng);
        userVars.passwd.value = randnum.toString(16); // won't be used for anything
        
        userVars.submit();
    }
    
</script>

<form name="logform" action="login.php" method="post">
    
    <?php /*<a href="#" class="button button-small">Register</a>
       <a href="#" class="button button-small">Invite</a>*/ ?>
       <label for="loginemail"><span style="color: #777777">Email:</span></label> <input type="text" class="text rounded" name="email" id="loginemail" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
       <label for="loginpasswd"><span style="color: #777777">Password:</span></label> <input type="password" class="rounded" name="passwd" id="loginpasswd" />
       <input type="hidden" name="pass" />
       <input type="hidden" name="sesskey" />
       <?php //echo "<input type=\"button\" class=\"button\" value=\"Login\" onclick=\"loginfinish();\" />"; ?>
       <a onClick="loginfinish();" class="button button-small">Login</a>
    
    
    
    
</form>