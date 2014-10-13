
// ----------------- RSA PEM to RSAKey -------------------------------------

function publicPEMtoRSAKey(pem) {
    
    var rsaobj = new KJUR.asn1.x509.SubjectPublicKeyInfo();
    rsaobj.setRSAPEM(pem);
    return rsaobj.rsaKey;    
}

function privatePEMtoRSAKey(pem,pass) {
    
    var decryptedKeyHex = PKCS5PKEY.getDecryptedKeyHex(pem, pass);
    var k = new RSAKey();
    k.readPrivateKeyFromASN1HexString(decryptedKeyHex);
    
    return k;    
}

// ----------------- RSA RSAKey to PEM -------------------------------------

function publicRSAKeytoPEM(rsa) {
    
    return KJUR.asn1.x509.X509Util.getPKCS8PubKeyPEMfromRSAKey(rsa);

}

function privateRSAKeytoPEM(rsa,pass) {
    
    return PKCS5PKEY.getEryptedPKCS5PEMFromRSAKey(rsa, pass);
}

// ------------------ END RSA ----------------------------------------------

function getRSAkeyFromSesskey(pem,sesskey) {
    
    var aes = new pidCrypt.AES.CTR();
    var encpass = ProcessCookie('read','encpass');
    var pass = aes.decryptText(encpass,sesskey,{nbits:256});
    
    return privatePEMtoRSAKey(pem,pass);
    
}

function getRandKey(bits) {
    var rng = new SecureRandom();
    var randkey = new BigInteger(bits,rng);
    
    return hex2b64(randkey.toString(16));
}

function getRandHexString(bits) {
    var rng = new SecureRandom();
    var randkey = new BigInteger(bits,rng);
    
    return randkey.toString(16);
}

function rsatest(n,e,d) {
        
    var testvector = getRandKey(256);
    var rsa = new RSAKey();
    rsa.setPrivate(n,e,d);
    
    var enctest = rsa.encrypt(testvector);
    var dectest = rsa.decrypt(enctest);
    
    if (testvector == dectest) {
        return true;
    } else {
        return false;
    }
}

// ------------------- RSA --------------------------------------------


function ttRSAencryptWkey(rec_rsakey,plntxt,callback) { // CHANGED
    
    //var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    
    //getPubkey(user_id,function(rec_rsa,email) { //email not used
        
        var cryptext = rec_rsakey.encrypt(plntxt);
        callback(cryptext);
    
    //});

    
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
    
    var my_rsa = getRSAkeyFromSesskey(window.privkey,window.sesskey);
    return my_rsa.decrypt(cryptext);
}

function ttRSAverifyWkey(sender_rsakey,msg,sign) { // CHANGED
    
    //getPubkey(user_id,function(sender_rsa,email) { // email not used
        
        
        return sender_rsakey.verifyString(msg,sign);
        
    //});

}

// ------------------- HTML entities ----------------------------------

var tagsToReplace = {
    //'&': '&amp;',
    '<': '&lt;',
    '>': '&gt;'
};

function ttreplaceTag(tag) {
    return tagsToReplace[tag] || tag;
}

function ttsafe_tags_replace(str) {
    return str.replace(/[&<>]/g, ttreplaceTag);
}

// ------------------- AES --------------------------------------------

function ttAESencrypt(key,plntxt,mode) {
    
    //plntxt = ttsafe_tags_replace(plntxt);
    
    var cryptext = '';
    
    switch(mode) {
        case 1:
            var aes = new pidCrypt.AES.CTR();
            var encrypted_msgb64 = aes.encryptText(plntxt,key,{nbits:256});
            cryptext = b64tohex(encrypted_msgb64);
            break;
        case 'AES-CTR-128':
            var aes = new pidCrypt.AES.CTR();
            var encrypted_msgb64 = aes.encryptText(plntxt,key,{nbits:128});
            cryptext = b64tohex(encrypted_msgb64);
            break;
        case 'AES-CTR-256':
            var aes = new pidCrypt.AES.CTR();
            var encrypted_msgb64 = aes.encryptText(plntxt,key,{nbits:256});
            cryptext = b64tohex(encrypted_msgb64);
            break;
        //case 2:
        //    execute code block 2
        //    break;
        default:
            //cryptext = 'Unrecognized AES encrypt mode';
    }
    
    return cryptext;
}

function ttAESdecrypt(key,cryptext,mode) {
    
    var plntxt = '';
    
    switch(mode) {
        case 1:
            var aes = new pidCrypt.AES.CTR();
            var hexmsg = hex2b64(cryptext);
            plntxt = aes.decryptText(hexmsg,key,{nbits:256});
            break;
        case 'AES-CTR-128':
            var aes = new pidCrypt.AES.CTR();
            var hexmsg = hex2b64(cryptext);
            plntxt = aes.decryptText(hexmsg,key,{nbits:128});
            break;
        case 'AES-CTR-256':
            var aes = new pidCrypt.AES.CTR();
            var hexmsg = hex2b64(cryptext);
            plntxt = aes.decryptText(hexmsg,key,{nbits:256});
            break;
        //case 2:
        //    execute code block 2
        //    break;
        default:
            //plntxt = 'Unrecognized AES decrypt mode.';
    }
    
    plntxt = ttsafe_tags_replace(plntxt);
    
    return plntxt;    
}


function trim11 (str) {
    str = str.replace(/^\s+/, '');
    for (var i = str.length - 1; i >= 0; i--) {
        if (/\S/.test(str.charAt(i))) {
            str = str.substring(0, i + 1);
            break;
        }
    }
    return str;
}

function wordwrap( str, width, brk, cut ) {
    //wordwrap('The quick brown fox jumped over the lazy dog.', 20, '<br/>\n');
 
    brk = brk || '\n';
    width = width || 75;
    cut = cut || false;
 
    if (!str) { return str; }
 
    var regex = '.{1,' +width+ '}(\\s|$)' + (cut ? '|.{' +width+ '}|.+$' : '|\\S+?(\\s|$)');
 
    return str.match( RegExp(regex, 'g') ).join( brk );
 
}

// --------------- TABLE ------------------------------------------------

function ttRow(thistable,colnames) {
    
    var rowcolor = ProcessCookie('read','rowcolor');
    if (rowcolor == false) {
        rowcolor = '3E3E3E'; //'9AED5A';
        ProcessCookie('save','rowcolor',rowcolor);
    } else {
        ProcessCookie('erase','rowcolor');
        rowcolor = false;
    }
    
    
    var trow = document.createElement("tr");
    trow.setAttribute("valign","top");
    if (rowcolor != false) {
        trow.setAttribute("bgcolor","#"+rowcolor);
    }
    for (var n=0; n<colnames.length; n++) {
        
        var tcell = document.createElement("td");
        var myVar = colnames[n];
        
        if (typeof myVar == 'string' || myVar instanceof String) {
            // string
            tcell.innerHTML = myVar;
        } else if (myVar == null || myVar == '') {
            tcell.innerHTML = " ";
            
        } else {
            // object
            tcell.appendChild(myVar);
        }
        
        trow.appendChild(tcell);
    }
    
    thistable.appendChild(trow);
}

// --------------- TIME -------------------------------------------------

function formatDate(date, format, utc) {
    var MMMM = ["\x00", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var MMM = ["\x01", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var dddd = ["\x02", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var ddd = ["\x03", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    function ii(i, len) {
        var s = i + "";
        len = len || 2;
        while (s.length < len) s = "0" + s;
        return s;
    }

    var y = utc ? date.getUTCFullYear() : date.getFullYear();
    format = format.replace(/(^|[^\\])yyyy+/g, "$1" + y);
    format = format.replace(/(^|[^\\])yy/g, "$1" + y.toString().substr(2, 2));
    format = format.replace(/(^|[^\\])y/g, "$1" + y);

    var M = (utc ? date.getUTCMonth() : date.getMonth()) + 1;
    format = format.replace(/(^|[^\\])MMMM+/g, "$1" + MMMM[0]);
    format = format.replace(/(^|[^\\])MMM/g, "$1" + MMM[0]);
    format = format.replace(/(^|[^\\])MM/g, "$1" + ii(M));
    format = format.replace(/(^|[^\\])M/g, "$1" + M);

    var d = utc ? date.getUTCDate() : date.getDate();
    format = format.replace(/(^|[^\\])dddd+/g, "$1" + dddd[0]);
    format = format.replace(/(^|[^\\])ddd/g, "$1" + ddd[0]);
    format = format.replace(/(^|[^\\])dd/g, "$1" + ii(d));
    format = format.replace(/(^|[^\\])d/g, "$1" + d);

    var H = utc ? date.getUTCHours() : date.getHours();
    format = format.replace(/(^|[^\\])HH+/g, "$1" + ii(H));
    format = format.replace(/(^|[^\\])H/g, "$1" + H);

    var h = H > 12 ? H - 12 : H == 0 ? 12 : H;
    format = format.replace(/(^|[^\\])hh+/g, "$1" + ii(h));
    format = format.replace(/(^|[^\\])h/g, "$1" + h);

    var m = utc ? date.getUTCMinutes() : date.getMinutes();
    format = format.replace(/(^|[^\\])mm+/g, "$1" + ii(m));
    format = format.replace(/(^|[^\\])m/g, "$1" + m);

    var s = utc ? date.getUTCSeconds() : date.getSeconds();
    format = format.replace(/(^|[^\\])ss+/g, "$1" + ii(s));
    format = format.replace(/(^|[^\\])s/g, "$1" + s);

    var f = utc ? date.getUTCMilliseconds() : date.getMilliseconds();
    format = format.replace(/(^|[^\\])fff+/g, "$1" + ii(f, 3));
    f = Math.round(f / 10);
    format = format.replace(/(^|[^\\])ff/g, "$1" + ii(f));
    f = Math.round(f / 10);
    format = format.replace(/(^|[^\\])f/g, "$1" + f);

    var T = H < 12 ? "AM" : "PM";
    format = format.replace(/(^|[^\\])TT+/g, "$1" + T);
    format = format.replace(/(^|[^\\])T/g, "$1" + T.charAt(0));

    var t = T.toLowerCase();
    format = format.replace(/(^|[^\\])tt+/g, "$1" + t);
    format = format.replace(/(^|[^\\])t/g, "$1" + t.charAt(0));

    var tz = -date.getTimezoneOffset();
    var K = utc || !tz ? "Z" : tz > 0 ? "+" : "-";
    if (!utc) {
        tz = Math.abs(tz);
        var tzHrs = Math.floor(tz / 60);
        var tzMin = tz % 60;
        K += ii(tzHrs) + ":" + ii(tzMin);
    }
    format = format.replace(/(^|[^\\])K/g, "$1" + K);

    var day = (utc ? date.getUTCDay() : date.getDay()) + 1;
    format = format.replace(new RegExp(dddd[0], "g"), dddd[day]);
    format = format.replace(new RegExp(ddd[0], "g"), ddd[day]);

    format = format.replace(new RegExp(MMMM[0], "g"), MMMM[M]);
    format = format.replace(new RegExp(MMM[0], "g"), MMM[M]);

    format = format.replace(/\\(.)/g, "$1");

    return format;
}

function getLocalTimeFromGMT(sTime){
  var conTime = sTime.replace(/-/g,"/");
  //console.log(conTime);
  var dte = new Date(conTime); 
  dte.setTime(dte.getTime() - dte.getTimezoneOffset()*60*1000);       
  //document.write(dte.toLocaleString());
  
  var outStr = dte.toLocaleString();

  if (outStr == 'Invalid Date') {
    outStr = 'No Timestamp Data Recorded, For Your Protection';
  }

  return outStr;
}

// ----------------- TT AJAX ----------------------------------------------

// Custom GET & POST wrappers:

//*
$.ttPOST = function(action,data,callback){
        //$.post('ajaxChat.php?action='+action,data,callback,'json');
        PostAjaxRequest(callback,'ajax.php?action='+action,data);
}
//*/

$.ttGET = function(action,data,callback){
        //$.get('ajaxChat.php?action='+action,data,callback,'json');
        GetAjaxRequest(callback,'ajax.php?action='+action);
}

// ------------------ VOUCHING --------------------------------------------

function ttvouche(user_id,email,pubkey) {
    
    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    var signedKey = my_rsa.signString(email+pubkey,'sha256');
    
    //console.log('user_id: '+user_id);
    //console.log('email: ' +email);
    //console.log('pubkey: '+pubkey);
    //console.log('signature: '+signedKey);
    $.ttPOST('vouche','user_id='+ user_id +'&signature='+ signedKey +'&type=RSA-sha256-email-pubkey',function(r){
            
        var trimmed = $.trim(this);
        //alert(trimmed);
        var data = jQuery.parseJSON(trimmed);
            
        displayVouches('peopleVouches');
        
    });
}

/*function ttvouche(user_id,callback) {
    
    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    
    getPubkey('',user_id,function(pubkey,email) {
        
        var signedKey = my_rsa.signString(email+pubkey,'sha256');
        
        $.ttPOST('vouche','user_id='+ user_id +'&signature='+ signedKey +'&type=RSA-sha256-email-pubkey',function(r){
            
            var trimmed = $.trim(this);
            //alert(trimmed);
            var data = jQuery.parseJSON(trimmed);
            
            callback();
        });
        
    });
}*/

function ttunvouche(user_id,callback) {
    
    $.ttPOST('unvouche','user_id='+ user_id,function(r){
            
        var trimmed = $.trim(this);
        //console.log(trimmed);
        var data = jQuery.parseJSON(trimmed);
        
        callback();
    });
}

function ttVerifyVouche(user_id,signature) {
    
    console.log('user_id: '+user_id+'; signature: '+signature);
    
    var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    
    getPubkey('',user_id,function(pubkey,email) {
        
        var signedKey = my_rsa.signString(email+pubkey,'sha256');
        
        if (signedKey == signature) {
            return "VERIFIED";
        } else {
            return "NOT VERIFIED";
        }
        
    });
}

function ttvoucheNav(user_id,signature) {
    //console.log('user_id: '+user_id);
    ttvouche(user_id,function() {
        
        //$('#vouche'+user_id).html(ttVerifyVouche(user_id,signature));
        navFindPeople();
    });
    
}

function ttunvoucheNav(user_id) {
    ttunvouche(user_id,function() {
        
        navFindPeople();
    });
    
}

// ------------------ BLOCK -----------------------------------------------

function ttblock(user_id,email,ukey) {
    
    //var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    //var signedKey = my_rsa.signString(email+pubkey,'sha256');
    
    $.ttPOST('block','user_id='+ user_id ,function(r){ // +'&signature='+ signedKey +'&type=RSA-sha256-email-pubkey'
            
        var trimmed = $.trim(this);
        //alert(trimmed);
        var data = jQuery.parseJSON(trimmed);
            
        displayVouches('peopleVouches');
        
    });
    
}

function ttunblock(user_id,email,ukey) {
    
    //var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
    //var signedKey = my_rsa.signString(email+pubkey,'sha256');
    
    $.ttPOST('unblock','user_id='+ user_id ,function(r){ // +'&signature='+ signedKey +'&type=RSA-sha256-email-pubkey'
            
        var trimmed = $.trim(this);
        //alert(trimmed);
        var data = jQuery.parseJSON(trimmed);
            
        displayVouches('peopleVouches');
        
    });
    
}

// ----------------- FORM CHECK -------------------------------------------

function checkPWForm(pw1,pw2,form) {
    
    if(pw1.value != "" && pw1.value == pw2.value) {
        
      if(pw1.value.length < 8) {
        statusMessage("Error: Password must contain at least eight characters.");
        pw1.focus();
        return false;
      }
      if(pw1.value == form.username.value) {
        statusMessage("Error: Password must be different from Username.");
        pw1.focus();
        return false;
      }
      re = /[0-9]/;
      if(!re.test(pw1.value)) {
        statusMessage("Error: password must contain at least one number (0-9).");
        pw1.focus();
        return false;
      }
      re = /[a-z]/;
      if(!re.test(pw1.value)) {
        statusMessage("Error: password must contain at least one lowercase letter (a-z).");
        pw1.focus();
        return false;
      }
      re = /[A-Z]/;
      if(!re.test(pw1.value)) {
        statusMessage("Error: password must contain at least one uppercase letter (A-Z).");
        pw1.focus();
        return false;
      }
    } else {
      statusMessage("Error: Please check that you've entered and confirmed your password.");
      pw1.focus();
      return false;
    }
    
    return true;
}

function checkUsernameForm(form) {
    
  if(form.username.value == "") {
    statusMessage("Error: Username cannot be blank.");
    form.username.focus();
    return false;
  }
  re = /^\w+$/;
  if(!re.test(form.username.value)) {
    statusMessage("Error: Username must contain only letters, numbers and underscores.");
    form.username.focus();
    return false;
  }
  return true;
}

function checkUserForm(form) {
    
  var usernamecheck = checkUsernameForm(form);
    
  if (usernamecheck == false) {
    return false;
  }
  
  
  if (form.changePassBox.checked) {
    
    var pwcheck = checkPWForm(form.new_pass1wd,form.new_pass2wd,form);
    
    if (pwcheck == true) {
        //statusMessage("You entered a valid password: " + form.new_pass1wd.value);
    } else {
        return false;
    }

  }

  return true;
}

function checkRegForm(form) {
    
    var usernamecheck = checkUsernameForm(form);
    
    if (usernamecheck == false) {
        return false;
    }
    
    var pwcheck = checkPWForm(form.pass1wd,form.pass2wd,form);
    
    if (pwcheck == true) {
        //statusMessage("You entered a valid password: " + form.pass1wd.value);
    } else {
        return false;
    }
    
    return true;
}

function ttfinishRegistration() {
    
    var userVars = document.regform;
    
    var validForm = checkRegForm(userVars);
    
    if (validForm) {
        
        userVars.regBtn.className = "hidden";
        
            $('#loading').css('visibility','visible');
            
            statusMessage("Generating Secure Credentials... May take up to a minute to generate the RSA-2048 key... Please Wait...");
        
        
            var rsa = new RSAKey();
            rsa.generateAsync(2048,"c0000001",function(){
               
               if (rsa.n.bitLength() != 2048 || rsa.n.divide(rsa.d) > 2) // ensure 1) modulus is as long as it's supposed to be and 2) d > 0.3*n
               {
                   ttfinishRegistration();
               }
               else
               {
                
                   // public key format
                    var pubpem = KJUR.asn1.x509.X509Util.getPKCS8PubKeyPEMfromRSAKey(rsa);
                    userVars.pubkey.value = pubpem;
                    
                    // private key format
                    var privpem = PKCS5PKEY.getEryptedPKCS5PEMFromRSAKey(rsa, userVars.pass1wd.value);
                    userVars.privkey.value = privpem;
                    
                    // hash password before sending to server
                    userVars.pass.value = pidCrypt.SHA512(userVars.pass1wd.value);
                    
                    
                    
                    // create a random key to store in the $_SESSION
                    var rng = new SecureRandom();
                    var randkey = new BigInteger(512,rng);
                    //userVars.sesskey.value = hex2b64(randkey.toString(16));
                    userVars.sesskey.value = randkey.toString(16);
                    
                    // encrypt password to store in cookie for loading the user private key
                    var aes = new pidCrypt.AES.CTR();
                    var encpass = aes.encryptText(userVars.pass1wd.value,userVars.sesskey.value,{nbits:256});
                    ProcessCookie('save','encpass',encpass);
                    
                    // clear cleartext password before sending form to server
                    userVars.pass1wd.value = '';
                    userVars.pass2wd.value = '';
                   
                   //document.getElementById('regstatus').innerHTML = '';
                   $('#loading').css('visibility','hidden');
                   userVars.submit();
               }
           });
            
            
        } else {
            //statusMessage('Form is not valid.');
        }
        
}

function ttfinishReset() {
    
    var userVars = document.delform;
    
    userVars.regBtn.className = "hidden";
    
    var argstr = 'email='+encodeURIComponent(userVars.email.value);
    argstr += '&token='+encodeURIComponent(userVars.token.value);
    
    $.ttPOST('deleteAcct',argstr,function(r){
            
        var trimmed = $.trim(this);
        //console.log(trimmed);
        var data = jQuery.parseJSON(trimmed);
        //console.log(data);
        
        userVars.email.value = "";
        userVars.token.value = "";
        
        if (data.status == 1) {
            statusMessage('Reset complete. Check your email for an invitation to rejoin Tipping Trees.');
        } else {
            statusMessage('Reset unsuccessful. Email and token do not match.');
        }
        
        
        
        //callback(callbackvar);
        window.location.hash = "#register-registerForm";
        
    });
    
}


// ----------------- NAV AJAX ---------------------------------------------

    function ttResetAccount(divid,hash) {
        
        try {
            
            var decoded = decodeURIComponent(hash);
            //console.log(decoded.substr(23));
            var jsonobj = JSON.parse(decoded.substr(23));
            
            var email = jsonobj.email;
            var token = jsonobj.token;
            
                var delform = document.createElement("form");
                delform.setAttribute('name','delform');
                delform.setAttribute('id','delform');
                
                var regtable = document.createElement("table");
                    
                    var trow = document.createElement("tr");
                    trow.setAttribute("valign","top");
                    var tcell = document.createElement("td");
                    tcell.setAttribute("colspan","2");
                    tcell.innerHTML = "<h2>Account Reset Information:</h2>";
                    trow.appendChild(tcell);
                    regtable.appendChild(trow);
                    
                    var iemailLbl = document.createElement("div");
                    iemailLbl.innerHTML = "<h3>Email:</h3>";
                    
                    var iemail = document.createElement("input");
                    iemail.type = "text";
                    iemail.name = "email";
                    iemail.size = "60";
                    iemail.className = "rounded";
                    iemail.value = email;
                    //iemail.readOnly = true;
                    
                        ttRow(regtable,[iemailLbl,iemail]);
                        
                    var itokenLbl = document.createElement("div");
                    itokenLbl.innerHTML = "<h3>Token:</h3>";
                    
                    var itoken = document.createElement("input");
                    itoken.type = "text";
                    itoken.name = "token";
                    itoken.size = "60";
                    itoken.className = "rounded";
                    itoken.value = token;
                    //itoken.readOnly = true;
                    
                        ttRow(regtable,[itokenLbl,itoken]);
                        
                var regBtn = document.createElement("input");
                regBtn.name = "regBtn";
                regBtn.type = "button";
                regBtn.className = "button button-red";
                regBtn.value = "Reset";
                regBtn.addEventListener('click',function() {
                            //window.location.hash = "#people-peopleContacts";
                            ttfinishReset();
                            //ttPeoplePublic(divid,hash);
                        },false);
                
                //delform.appendChild(titleBar);
                delform.appendChild(regtable);
                delform.appendChild(regBtn);
                
                document.getElementById(divid).appendChild(delform);
            
        } catch(err) {
            
        }
    }
    
    function ttRegisterFromInvitation(divid,hash) {
        
        try {
            
            var decoded = decodeURIComponent(hash);
            //console.log(decoded.substr(33));
            var jsonobj = JSON.parse(decoded.substr(33));
            
            var email = jsonobj.email;
            var token = jsonobj.token;
            var inviter_email = jsonobj.inviter_email;
            
            //console.log('email: '+email+' token: '+token+' inviter: '+inviter_email);
            
            //var argstr = 'user_id='+jsonobj.inviter_id;
            var argstr = 'page=getUserInfoFromEmail';
            //argstr += '&sesskey='+ encodeURIComponent(window.sesskey);
            argstr += '&email='+encodeURIComponent(inviter_email);
            PostAjaxRequest(function() {
                
                var data = jQuery.parseJSON(this);
                
                var regform = document.createElement("form");
                regform.setAttribute('name','regform');
                regform.setAttribute('id','regform');
                regform.setAttribute('action','register.php');
                regform.setAttribute('method','post');
                
                    var regtable = document.createElement("table");
                    
                    var trow = document.createElement("tr");
                    trow.setAttribute("valign","top");
                    var tcell = document.createElement("td");
                    tcell.setAttribute("colspan","2");
                    tcell.innerHTML = "<h2>Invitation Information (do not change, this authenticates your invitation):</h2>";
                    trow.appendChild(tcell);
                    regtable.appendChild(trow);
                
                    var iemailLbl = document.createElement("div");
                    iemailLbl.innerHTML = "<h3>Email (cannot be changed):</h3>";
                    
                    var iemail = document.createElement("input");
                    iemail.type = "text";
                    iemail.name = "email";
                    iemail.size = "60";
                    iemail.className = "rounded";
                    iemail.value = email;
                    //iemail.readOnly = true;
                    
                        ttRow(regtable,[iemailLbl,iemail]);
                    
                    var itokenLbl = document.createElement("div");
                    itokenLbl.innerHTML = "<h3>Security Token (unique identifier for this account):</h3>";
                    
                    var itoken = document.createElement("input");
                    itoken.type = "text";
                    itoken.name = "token";
                    itoken.size = "60";
                    itoken.className = "rounded";
                    itoken.value = token;
                    //itoken.readOnly = true;
                    
                        ttRow(regtable,[itokenLbl,itoken]);
                        
                    var trow = document.createElement("tr");
                    trow.setAttribute("valign","top");
                    var tcell = document.createElement("td");
                    tcell.setAttribute("colspan","2");
                    tcell.innerHTML = "<br /><h2>Your only un-encrypted information on Tipping Trees:</h2><br /><h3>Usernames can contain only letters, numbers, and underscores. It can be changed at any time; however, the username must be unique.</h3><br />";
                    trow.appendChild(tcell);
                    regtable.appendChild(trow);
                    
                    var iusernameLbl = document.createElement("div");
                    iusernameLbl.innerHTML = "<h3>Username:</h3>";
                    
                    var iusername = document.createElement("input");
                    iusername.type = "text";
                    iusername.name = "username";
                    iusername.size = "30";
                    iusername.maxlength = "30";
                    iusername.className = "rounded";
                    
                        ttRow(regtable,[iusernameLbl,iusername]);
                        
                    var ifirstnameLbl = document.createElement("div");
                    ifirstnameLbl.innerHTML = "<h3>First Name:</h3>";
                    
                    var ifirstname = document.createElement("input");
                    ifirstname.type = "text";
                    ifirstname.name = "first_name";
                    ifirstname.size = "30";
                    ifirstname.maxlength = "30";
                    ifirstname.className = "rounded";
                    
                        ttRow(regtable,[ifirstnameLbl,ifirstname]);
                    
                    var ilastnameLbl = document.createElement("div");
                    ilastnameLbl.innerHTML = "<h3>Last Name:</h3>";
                    
                    var ilastname = document.createElement("input");
                    ilastname.type = "text";
                    ilastname.name = "last_name";
                    ilastname.size = "30";
                    ilastname.maxlength = "30";
                    ilastname.className = "rounded";
                    
                        ttRow(regtable,[ilastnameLbl,ilastname]);
                    
                    var trow = document.createElement("tr");
                    trow.setAttribute("valign","top");
                    var tcell = document.createElement("td");
                    tcell.setAttribute("colspan","2");
                    tcell.innerHTML = "<br /><h2>Your chosen password will be SHA-512 hashed locally on your machine, sent to the server, salted, and SHA-512 hashed again:</h2><br /><h3>Passwords must be at least 8 characters long, contain at least one lower case letter, at least one upper case character, and at least one number.</h3><br />";
                    trow.appendChild(tcell);
                    regtable.appendChild(trow);
                        
                    var ipass1Lbl = document.createElement("div");
                    ipass1Lbl.innerHTML = "<h3>Password:</h3>";
                    
                    var ipass1 = document.createElement("input");
                    ipass1.type = "password";
                    ipass1.name = "pass1wd";
                    ipass1.size = "30";
                    ipass1.maxlength = "100";
                    ipass1.className = "rounded";
                    
                        ttRow(regtable,[ipass1Lbl,ipass1]);
                        
                    var ipass2Lbl = document.createElement("div");
                    ipass2Lbl.innerHTML = "<h3>Confirm Password:</h3>";
                    
                    var ipass2 = document.createElement("input");
                    ipass2.type = "password";
                    ipass2.name = "pass2wd";
                    ipass2.size = "30";
                    ipass2.maxlength = "100";
                    ipass2.className = "rounded";
                    
                        ttRow(regtable,[ipass2Lbl,ipass2]);
                    
                var titleBar = document.createElement("div");
                titleBar.innerHTML = "<h1>Welcome to Tipping Trees!</h1>";
                
                var regBtn = document.createElement("input");
                regBtn.name = "regBtn";
                regBtn.type = "button";
                regBtn.className = "button";
                regBtn.value = "Register";
                regBtn.addEventListener('click',function() {
                            //window.location.hash = "#people-peopleContacts";
                            ttfinishRegistration();
                            //ttPeoplePublic(divid,hash);
                        },false);
                
                regform.appendChild(titleBar);
                regform.appendChild(regtable);
                regform.appendChild(regBtn);
                
                // hidden inputs
                var pubkey = document.createElement("input");
                pubkey.type = "hidden";
                pubkey.name = "pubkey";
                
                var privkey = document.createElement("input");
                privkey.type = "hidden";
                privkey.name = "privkey";
                
                var pass = document.createElement("input");
                pass.type = "hidden";
                pass.name = "pass";
                
                var sesskey = document.createElement("input");
                sesskey.type = "hidden";
                sesskey.name = "sesskey";
                
                var inviter_email_input = document.createElement("input");
                inviter_email_input.type = "hidden";
                inviter_email_input.name = "inviter_email";
                inviter_email_input.value = inviter_email;
                
                regform.appendChild(pubkey);
                regform.appendChild(privkey);
                regform.appendChild(pass);
                regform.appendChild(sesskey);
                regform.appendChild(inviter_email_input);
                
                
                document.getElementById(divid).appendChild(regform);
                
                var agmt = document.createElement("div");
                agmt.innerHTML = 'By registering your account you agree to abide by Tipping Trees\' <a href="https://tippingtrees.com/index.php#about-aboutDetails" target="_blank">Terms & Conditions</a> and <a href="https://tippingtrees.com/index.php#about-aboutDetails" target="_blank">Privacy Policy</a>, and certify that you are at least 13 years old.';
                document.getElementById(divid).appendChild(agmt);
            
            },'ajaxPublic.php',argstr);
        
        } catch(err) {
            
        }
    }

    function ttPeoplePublicLink(email) {
        var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': email}));
        
        return deststr;
    }
    
    function getEncryptLink(pubkey,isloggedin,jsonobj,svrname,mode) {
        
        var userinfo = jsonobj.userinfo;
        
        var encryptLinkDiv = document.createElement("div");
        
        var alink = document.createElement("input");
        alink.type = "button"
        alink.className = "button button-medium";
        alink.value = "Encrypt Message For Your Eyes Only";
        //alink.data_user_id = userinfo['user_id'];
        //alink.data_email   = userinfo['email'];
        //alink.data_ukey    = userinfo['pubkey'];
        alink.addEventListener('click',function() {
            //window.location.hash = "#people-peopleContacts";
            //ttvouche(this.data_user_id,this.data_email,this.data_ukey);
            //ttPeoplePublic(divid,hash);
            if (document.getElementById('peoplePublicEncrypt').className == 'hidden') {
                
                document.getElementById('peoplePublicEncrypt').className = "visible";
                document.getElementById('peoplePublicEncryptBtn').className = "button button-medium visible";
                document.getElementById('peoplePublicLinkTxt').className = "visible";
            } else {
                document.getElementById('peoplePublicEncrypt').className = "hidden";
                document.getElementById('peoplePublicEncryptBtn').className = "hidden";
                document.getElementById('peoplePublicLinkTxt').className = "hidden";
            }
            
        },false);
        
        var txtBox = document.createElement("textarea");
        txtBox.className = "hidden";
        txtBox.innerHTML = "Type your message here for encryption. The link created below may be placed anywhere but only "+jsonobj.username+" will be able to decrypt and view the message.";
        txtBox.setAttribute('maxlength','2000');
        txtBox.setAttribute('rows','10');
        txtBox.setAttribute('cols','50');
        txtBox.setAttribute('id','peoplePublicEncrypt');
        
        var ebtn = document.createElement("input");
        ebtn.type = "button";
        ebtn.className = "hidden";
        ebtn.value = "Create Link";
        ebtn.setAttribute('id','peoplePublicEncryptBtn');
        ebtn.addEventListener('click',function() {
            // if empty, do nothing
            if (txtBox.value == "") {
                return;
            }
            
            // create link
            var msg = txtBox.value;
            var rec_rsakey = publicPEMtoRSAKey(pubkey);
            
            var pln_key = getRandKey(512);
            var enc_msg = ttAESencrypt(pln_key,msg,mode);
            var enc_key = rec_rsakey.encrypt(pln_key);
            
            var subject = "";
            var enc_subject = ttAESencrypt(pln_key,subject,mode);
            
            if (isloggedin == 1) { // sender is logged in
                var sender_id = userinfo['user_id'];
                //console.log('sender_id: '+sender_id);
                var enc_sender_id = ttAESencrypt(pln_key,sender_id,mode);
                
                var signature = ttRSAsign(enc_msg);
                var enc_signature = ttAESencrypt(pln_key,signature,mode);
            } else {
                var sender_id = '0';
                var enc_sender_id = ttAESencrypt(pln_key,sender_id,mode);
                
                var signature = "";
                var enc_signature = "";
            }
            
            var d = new Date;
            var utc_timestamp = formatDate(d,'yyyy-MM-dd HH:mm:ss',true);
            
            var lnktxt = "https://" + svrname + "/index.php#pmessage-pmessageView-" + encodeURIComponent(JSON.stringify({'enc_key' : enc_key,
											       'enc_mode' : mode,
											       'enc_subject' : enc_subject,
											       'enc_body' : enc_msg,
											       'enc_signature' : enc_signature,
											       'enc_sender' : enc_sender_id,
											       'enc_receiver' : '',
											       'post_time' : utc_timestamp}));
            
            
            
            
            // populate link
            var amsg = document.createElement("a");
            amsg.setAttribute('href',lnktxt);
            amsg.setAttribute('target','_blank');
            amsg.innerHTML = wordwrap(lnktxt,80,'<br />\n',true);
            lnkarea.innerHTML = "";
            lnkarea.appendChild(amsg);
            
        },false);
        
        var lnkarea = document.createElement("div");
        lnkarea.setAttribute('id','peoplePublicLinkTxt');
        lnkarea.className = "hidden";
        
        
        encryptLinkDiv.appendChild(alink);
        encryptLinkDiv.appendChild(txtBox);
        encryptLinkDiv.appendChild(ebtn);
        encryptLinkDiv.appendChild(lnkarea);
        
        return encryptLinkDiv;
    }

    function ttPeoplePublic(divid,hash) {
        
        try {
            //console.log(hash);
            var decoded = decodeURIComponent(hash);
            //console.log(decoded);
            //console.log(decoded.substr(21));
            //var jsonobj = JSON.parse(decoded.substr(21));
            var jsonobj = JSON.parse(decoded);
            
            var email = jsonobj.email;
            
            //document.getElementById(divid).innerHTML = 'Email: '+email;
            
            var argstr = 'page=getUserInfoFromEmail';
            //argstr += '&sesskey='+ encodeURIComponent(window.sesskey);
            argstr += '&email='+encodeURIComponent(email);
            
            PostAjaxRequest(function() {
            //$.ttPOST('getUserInfoFromEmail',argstr,function(r){
                
                //console.log(this);
                var jsonobj = JSON.parse(this);
                //console.log(jsonobj);
                
                var username   = jsonobj.username;
                var pubkey     = jsonobj.pubkey;
                var numsig     = jsonobj.numsig;
                var numSecSign = jsonobj.numSecSign;
                var isloggedin = jsonobj.isloggedin;
                var self       = jsonobj.self;
                var mode       = jsonobj.mode;
                var svrname    = jsonobj.svrname;
                var selfinfo   = ''; // must be logged in
                
                var sigPplstr = "people";
                if (numsig == 1) {
                    sigPplstr = "person";
                }
                
                var userexists = true;
                if (username == null && pubkey == null) {
                    username = "Future member of Tipping Trees";
                    pubkey = "Future member of Tipping Trees";
                    userexists = false;
                }
                
                
                var vouchinfo = '';
                var userinfo = '';
                
                var signed = '';
                var rlink = document.createElement("div");
                //var alink = '';
                var alink = document.createElement("div");
                var blink = document.createElement("div");
                if (isloggedin == 1 && self == 0 && userexists) {
                    userinfo = jsonobj.userinfo;
                    signed = jsonobj.signed;
                    selfinfo   = jsonobj.selfinfo;
                    if (signed == 0) {
                        //vouchinfo = jsonobj.vouchinfo;
                        
                        alink = document.createElement("input");
                        alink.type = "button"
                        alink.className = "button button-medium";
                        alink.value = "Connect";
                        alink.data_user_id = userinfo['user_id'];
                        alink.data_email   = userinfo['email'];
                        alink.data_ukey    = userinfo['pubkey'];
                        alink.addEventListener('click',function() {
                            window.location.hash = "#people-peopleContacts";
                            ttvouche(this.data_user_id,this.data_email,this.data_ukey);
                            //ttPeoplePublic(divid,hash);
                        },false);
                        
                    }
                    //else {
                    //    // key must be signed in order to block/unblock
                    //    
                    //    // Block/Unblock
                    //    blink = document.createElement("input");
                    //    blink.type = "button";
                    //    blink.className = "button button-small";
                    //    blink.value = "Block";
                    //    blink.data_user_id  = selfinfo.user_id;
                    //    blink.data_blocked_id = userinfo['user_id'];
                    //    blink.addEventListener('click',function() {
                    //        ttblockid(this.data_user_id,this.data_blocked_id);
                    //    },false);
                    //}
                    
                    // Send Message
                    rlink = document.createElement("input");
                    rlink.type = "button"
                    rlink.value = "Send Message";
                    rlink.className = "button button-medium";
                    rlink.data_user_id    = userinfo['user_id'];
                    rlink.data_user_email = userinfo['email'];
                    rlink.data_user_key   = userinfo['pubkey'];
                    rlink.addEventListener('click',function() {
                            selRecPMsg(this.data_user_id,this.data_user_email,this.data_user_key);    
                        },false);
                }
                
                //var signline = document.createElement("p");
                //signline.innerHTML = "This RSA key has been signed by " + numsig + " " + sigPplstr + ". The number of unique second level signatures (signers of signers): " + numSecSign;
                
                var signline = document.createElement("div");
                var ratetable = document.createElement("table");
                ratetable.setAttribute('align','right');
                ttRow(ratetable,['<h3>Ratings</h3>','<h3>Value</h3>']);
                ttRow(ratetable,['Signatures on this public key',''+numsig]);
                ttRow(ratetable,['Second-level signatures on this public key',''+numSecSign]);
                signline.appendChild(ratetable);
                signline.appendChild(document.createElement("p"));
                //console.log(divid);
                
                var disptable = document.createElement("div");
                if (isloggedin == 1) { // display key signers
                    vouchinfo  = jsonobj.vouchinfo;
                    selfinfo   = jsonobj.selfinfo;
                    var voucheme = vouchinfo.voucheme;
                    var voucheothers = vouchinfo.voucheothers;
                    
                    // -------------------------------------------------------------
                    var msg = vouchinfo.myemail + vouchinfo.mykey;
                    
                
                    //console.log('voucheme: '+voucheme);
                    
                    disptable = document.createElement("table");
                        var trow = document.createElement("tr");
                        trow.setAttribute("valign","top");
                        // var tcell = document.createElement("td");
                        // tcell.setAttribute("colspan","5");
                        // tcell.innerHTML = "<h2>Signatures:</h2>";
                        // trow.appendChild(tcell);
                        disptable.appendChild(trow);
                    ttRow(disptable,['<h3>Contact</h3>','<h3>Signature</h3>','<h3>Type</h3>','<h3>Sign Time</h3>','<h3>Action</h3>']);
                    
                    //var disptext = "";
                    //disptext += "<table>\n";
                    //disptext += "<tr><td colspan=\"5\"><h1>People Who Know Me:</h1></td></tr>\n";
                    //disptext += "<tr valign=\"top\">\n";
                    //disptext += "<td>Contact:</td><td>Signature:</td><td>Type:</td><td>Sign Time:</td><td>Action:</td>\n";
                    //disptext += "</tr>\n";
                    for (var n=0; n<voucheme.length; n++) {
                        
                        var signature = voucheme[n].signature['signature'];
                        var signtype  = voucheme[n].signature['type'];
                        var signtime  = voucheme[n].signature['sign_time'];
                        
                        
                        // link str
                        var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': voucheme[n].user['email']}));
                        var plink = document.createElement("a");
                        plink.setAttribute('href',deststr);
                        plink.innerHTML = voucheme[n].user['username'] + " (" + voucheme[n].user['email'] + ")";
                        
                        var user = document.createElement("span");
                        user.setAttribute('title',voucheme[n].key);
                        user.appendChild(plink);
                        
                        //var user      = "<span title=\"" + voucheme[n].key + "\">" + voucheme[n].user['last_name'] + ", " + voucheme[n].user['first_name'] + " (" + voucheme[n].user['email'] + ")</span>";
                        
                        //console.log(voucheme[n].key);
                        var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                        try {
                            var otherRSAkey = publicPEMtoRSAKey(voucheme[n].key);
                            //console.log(signature);
                            var isVerified = otherRSAkey.verifyString(msg,signature);
                            
                            if (isVerified) {
                                verified = "<span style=\"color:#009900\">VERIFIED</span>";
                            }
                        } catch(err) {
                            
                        }
                        
                        var verstr = "<span title=\"" + signature + "\">" + verified + "</span>";
                        
                        //disptext += "<tr valign=\"top\">\n";
                        //disptext += "<td>" + user + "</td><td>" + verstr + "</td><td>" + signtype + "</td><td>" + getLocalTimeFromGMT(signtime) + "</td>"; // only 4 cells, append the 5th
                        
                        
                        // Action cell
                        var tcell = document.createElement("td");
                        
                        
                        if (selfinfo.email != voucheme[n].user['email']) {
                            
                            // Send Message
                            var srlink = "";
                            srlink = document.createElement("a");
                            srlink.innerHTML = "Send Message";
                            srlink.className = "button button-small";
                            srlink.data_user_id = voucheme[n].user['user_id'];
                            srlink.data_user_email = voucheme[n].user['email'];
                            srlink.data_user_key = voucheme[n].key;
                            srlink.addEventListener('click',function() {
                                    selRecPMsg(this.data_user_id,this.data_user_email,this.data_user_key);    
                                },false);
                            
                            if (srlink.data_user_email != null) {
                                //console.log(srlink.data_user_email);
                                tcell.appendChild(srlink);
                            }
                        
                        }
                            
                        
                        
                        //if (containsUser) {
                        //    ttRow(disptable,[user,verstr,signtype,getLocalTimeFromGMT(signtime),""]);
                        //} else {
                            ttRow(disptable,[user,verstr,signtype,getLocalTimeFromGMT(signtime),tcell]);
                        //}
                        
                        
                        //disptext += "</tr>\n";
                    }
                    
                    
                    
                
                // ---------------------------------------------------------------------------
                
                
                }
                
                //console.log('mode: '+mode);
                if (userexists) {
                    var encryptLinkDiv = getEncryptLink(pubkey,isloggedin,jsonobj,svrname,mode);
                } else {
                    var encryptLinkDiv = document.createElement("div");
                }
                
                
                /*var pgHTML = document.createElement("span");
                pgHTML.setAttribute('title',pubkey);
                pg*/
                var pgHTML = "<span title=\"" + pubkey + "\"><h1>"+ "<em>" + username + "</em>       " + "<b>(" + email + ")</b>" + "</h1></span>";
                //pgHTML += "<p><h2>Public Key:</h2></p>";
                //pgHTML += "<p>" + pubkey + "</p>";
                
                // create table for Profile Action buttons and Rating table
                var toptable = document.createElement("table");
                toptable.setAttribute('width','740px');
                var toptablerow = document.createElement("tr");
                toptablerow.setAttribute('valign','top');
                var toptableleftcell = document.createElement("td");
                toptableleftcell.appendChild(alink);
                toptableleftcell.appendChild(rlink);
                var toptablerightcell = document.createElement("td");
                toptablerightcell.appendChild(signline);
                toptablerow.appendChild(toptableleftcell);
                toptablerow.appendChild(toptablerightcell);
                toptable.appendChild(toptablerow);
                
                document.getElementById(divid).innerHTML = pgHTML;
                //document.getElementById(divid).appendChild(alink);
                //document.getElementById(divid).appendChild(rlink);
                //document.getElementById(divid).appendChild(signline);
                document.getElementById(divid).appendChild(toptable);
                document.getElementById(divid).appendChild(disptable);
                document.getElementById(divid).appendChild(encryptLinkDiv);
                
                document.getElementById(divid).className = "divmh";
                
                
                //document.getElementById(divid).innerHTML += "<p>This RSA key has been signed by " + numsig + " " + sigPplstr + ".</p>";
                
                
                
            },'ajaxPublic.php',argstr);
        
        } catch(err) {
            
        }
    }
    
    function ttHomeContent(divid) {
                
        var argstr = 'page=getHomeContent';
        //argstr += '&sesskey='+ encodeURIComponent(window.sesskey);
        //argstr += '&email='+encodeURIComponent(email);
        
        PostAjaxRequest(function() {
            
            //console.log(this);
            var jsonobj = JSON.parse(this);
            
            document.getElementById(divid).innerHTML = "";
            
            var header = document.createElement("h1");
            header.innerHTML = jsonobj.header;
            document.getElementById(divid).appendChild(header);
            
            document.getElementById(divid).appendChild(document.createElement("p"));
            
            var intro  = document.createElement("h2");
            intro.innerHTML = jsonobj.intro;
            document.getElementById(divid).appendChild(intro);
            
            document.getElementById(divid).appendChild(document.createElement("p"));
            
            var steps = jsonobj.steps;
            var disptable = document.createElement("table");
            //ttRow(disptable,['<h3>Enhancement</h3>','<h3>Description</h3>']);
            var trow = document.createElement("tr");
            //trow.setAttribute("valign","top");
            var tcell1 = document.createElement("td");
            tcell1.setAttribute('width','246px');
            tcell1.setAttribute('align','center');
            tcell1.innerHTML = '<h3>'+steps[0][0]+'</h3>';
            var tcell2 = document.createElement("td");
            tcell2.setAttribute('width','247px');
            tcell2.setAttribute('align','center');
            tcell2.innerHTML = '<h3>'+steps[0][1]+'</h3>';
            var tcell3 = document.createElement("td");
            tcell3.setAttribute('width','246px');
            tcell3.setAttribute('align','center');
            tcell3.innerHTML = '<h3>'+steps[0][2]+'</h3>';
            trow.appendChild(tcell1);
            trow.appendChild(tcell2);
            trow.appendChild(tcell3);
            disptable.appendChild(trow);
            
            
            ttRow(disptable,[steps[1][0],steps[1][1],steps[1][2]]);
            document.getElementById(divid).appendChild(disptable);
            
            document.getElementById(divid).appendChild(document.createElement("p"));
            
            var features = jsonobj.features;
            var disptable = document.createElement("table");
            //ttRow(disptable,['<h3>Enhancement</h3>','<h3>Description</h3>']);
            for (var n=0; n<features.length; n++) {
                ttRow(disptable,['<h3>'+features[n].name+'</h3>',features[n].desc]);
            }
            document.getElementById(divid).appendChild(disptable);
            
            
            
            
        },'ajaxPublic.php',argstr);
        
    }
    
    function ttRoadmap(divid) {
                
        var argstr = 'page=getRoadmap';
        //argstr += '&sesskey='+ encodeURIComponent(window.sesskey);
        //argstr += '&email='+encodeURIComponent(email);
        
        PostAjaxRequest(function() {
            
            //console.log(this);
            var jsonobj = JSON.parse(this);
            
            document.getElementById(divid).innerHTML = "";
            
            var header = document.createElement("h1");
            header.innerHTML = jsonobj.header;
            document.getElementById(divid).appendChild(header);
            
            document.getElementById(divid).appendChild(document.createElement("p"));
            
            var intro  = document.createElement("h2");
            intro.innerHTML = jsonobj.intro;
            document.getElementById(divid).appendChild(intro);
            
            document.getElementById(divid).appendChild(document.createElement("p"));
            
            var features = jsonobj.features;
            var disptable = document.createElement("table");
            ttRow(disptable,['<h3>Enhancement</h3>','<h3>Description</h3>']);
            for (var n=0; n<features.length; n++) {
                ttRow(disptable,['<h3>'+features[n].name+'</h3>',features[n].desc]);
            }
            document.getElementById(divid).appendChild(disptable);
            
        },'ajaxPublic.php',argstr);
        
    }
    
    function ttdbPublic(divid) {
                
        var argstr = 'page=getdbChat';
        //argstr += '&sesskey='+ encodeURIComponent(window.sesskey);
        //argstr += '&email='+encodeURIComponent(email);
        
        PostAjaxRequest(function() {
        //$.ttPOST('getUserInfoFromEmail',argstr,function(r){
            
            //console.log(this);
            var jsonobj = JSON.parse(this);
            
            // the tables
            var chatuser  = jsonobj.chatuser;
            var chattoken = jsonobj.chattoken;
            var chatinfo  = jsonobj.chatinfo;
            var chatline  = jsonobj.chatline;
            
            var divwidth  = '270';
            var secdivwidth = '80';
            var thirddivwidth = '250';
            
            // chatuser
            var header = document.createElement("h1");
            header.innerHTML = "Table 1";
            document.getElementById(divid).appendChild(header);
            var disptable = document.createElement("table");
            ttRow(disptable,['<h2>User ID:</h2>','<h2>Encrypted Key:</h2>','<h2>Encrypted Token:</h2>','<h2>Encryption Mode:</h2>']);
            for (var n=0; n<chatuser.length; n++) {
                ttRow(disptable,[chatuser[n].recipient_id,'<div style="width: '+divwidth+'px; word-wrap: break-word">'+chatuser[n].enc_chat_key+'</div>','<div style="width: '+divwidth+'px; word-wrap: break-word">'+chatuser[n].enc_chat_token+'</div>',chatuser[n].enc_mode]);
            }
            document.getElementById(divid).appendChild(disptable);
            
            // chattoken
            header = document.createElement("h1");
            header.innerHTML = "Table 2";
            document.getElementById(divid).appendChild(header);
            disptable = document.createElement("table");
            ttRow(disptable,['<h2>Token:</h2>','<h2>Encrypted User ID:</h2>','<h2>Encrypted Status:</h2>','<h2>Encryption Mode:</h2>']);
            for (var n=0; n<chattoken.length; n++) {
                ttRow(disptable,['<div style="width: '+divwidth+'px; word-wrap: break-word">'+chattoken[n].chat_token+'</div>',chattoken[n].enc_user_id,chattoken[n].enc_isactive,chattoken[n].enc_mode]);
            }
            document.getElementById(divid).appendChild(disptable);
            
            // chatinfo
            header = document.createElement("h1");
            header.innerHTML = "Table 3";
            document.getElementById(divid).appendChild(header);
            disptable = document.createElement("table");
            ttRow(disptable,['<h2>Token:</h2>','<h2>Owner ID:</h2>','<h2>Encrypted Group Name:</h2>','<h2>Encrypted Open Invitation Status:</h2>','<h2>Owner Signature:</h2>','<h2>Encryption Mode:</h2>']);
            for (var n=0; n<chatinfo.length; n++) {
                ttRow(disptable,['<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatinfo[n].chat_token+'</div>',chatinfo[n].chat_owner,'<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatinfo[n].enc_chat_name+'</div>',chatinfo[n].enc_chat_invite,'<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatinfo[n].owner_signature+'</div>',chatinfo[n].enc_mode]);
            }
            document.getElementById(divid).appendChild(disptable);
            
            // chatline
            header = document.createElement("h1");
            header.innerHTML = "Table 4";
            document.getElementById(divid).appendChild(header);
            disptable = document.createElement("table");
            ttRow(disptable,['<h2>Token:</h2>','<h2>Encrypted Sender ID:</h2>','<h2>Encrypted Message:</h2>','<h2>Encrypted Signature:</h2>','<h2>Encryption Mode:</h2>','<h2>Post Time:</h2>']);
            for (var n=0; n<chatline.length; n++) {
                ttRow(disptable,['<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatline[n].chat_token+'</div>','<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatline[n].enc_sender_id+'</div>','<div style="width: '+secdivwidth+'px; word-wrap: break-word">'+chatline[n].enc_chat_msg+'</div>','<div style="width: '+thirddivwidth+'px; word-wrap: break-word">'+chatline[n].enc_signature+'</div>',chatline[n].enc_mode,chatline[n].post_time]);
            }
            document.getElementById(divid).appendChild(disptable);
            
        },'ajaxPublic.php',argstr);
    }

    function clearRecPMsg() {
        
        document.getElementById("pmRecUserID").value = "";
        document.getElementById("pmRecEmail").value  = "";
        document.getElementById("pmRecKey").value    = "";
                    
        document.getElementById("pmRecInfo").innerHTML = "";
    }

    function selRecPMsg(user_id,user_email,user_key) {
        
        document.getElementById("pmRecUserID").value   = user_id;
        document.getElementById("pmRecEmail").value    = user_email;
        document.getElementById("pmRecKey").value      = user_key;
        
        var clink = document.createElement("a");
        clink.innerHTML = "Clear";
        clink.className = "button button-small";
        clink.addEventListener('click',function() {
                clearRecPMsg();    
            },false);
        
        document.getElementById("pmRecInfo").innerHTML = "Sending to: " + user_email;
        document.getElementById("pmRecInfo").appendChild(clink);
        
        window.location.hash = "#pmessage-pmessageCompose";
        
    }

    function deletePMsg(type,msg_id,callback,callbackvar) {
        
        //console.log('msg_id: '+msg_id);
        
        $.ttPOST('deletePMsg','type='+type+'&msg_id='+msg_id,function(r){
                
            var trimmed = $.trim(this);
            //console.log(trimmed);
            var data = jQuery.parseJSON(trimmed);
            //console.log(data);
            
            callback(callbackvar);
            
        });
    }

    function ttPMsgView(divid,hash) {
        
        try {
            
            
            var decoded = decodeURIComponent(hash);
            
            var jsonobj = JSON.parse(decoded.substr(23));
            
            /*JSON.stringify({'enc_key': enc_key,
                                    'enc_mode': mode,
                                    'enc_subject': enc_subject,
                                    'enc_body': enc_body,
                                    'enc_signature': enc_signature,
                                    'enc_sender': '',
                                    'enc_receiver': enc_receiver});*/
            
            //console.log(jsonobj.enc_key);
            //console.log('privkey: ' + privkey);
            //console.log('sesskey: ' + sesskey);
            
            var finishPMsgView = function() {
                
                try {
                    
                    var pln_key   = ttRSAdecrypt(jsonobj.enc_key);
                
                
                    // pln_key = null when the wrong user account tries to read a message
                    
                    if (pln_key == null) {
                        
                        document.getElementById(divid).innerHTML = "<p>This message could not be decrypted with this account's RSA key.</p>";
                        
                        return;
                    }
                    
                    var mode      = jsonobj.enc_mode;
                    var csubject  = ttAESdecrypt(pln_key,jsonobj.enc_subject,mode);
                    var cbody     = ttAESdecrypt(pln_key,jsonobj.enc_body,mode);
                    cbody = cbody.replace(/\n/g, '<br />\n'); // make new lines viewable in HTML
                    var signature = ttAESdecrypt(pln_key,jsonobj.enc_signature,mode);
                    var ctime     = jsonobj.post_time;
                    
                    // change + to spaces in time
                    ctime = ctime.replace(/\+/g,' ');
                    
                    var user_mapping = [];
                    
                    var other_id = [];
                    if (jsonobj.enc_sender == "") {
                        var pln_id = ttAESdecrypt(pln_key,jsonobj.enc_receiver,mode);
                        other_id.push(pln_id);
                        user_mapping.push({user_id:pln_id,
                                               enc_user_id:jsonobj.enc_receiver,
                                               type:'receiver'});
                    } else {
                        var pln_id = ttAESdecrypt(pln_key,jsonobj.enc_sender,mode);
                        other_id.push(pln_id);
                        user_mapping.push({user_id:pln_id,
                                               enc_user_id:jsonobj.enc_sender,
                                               type:'sender'});
                    }
                    
                    PostAjaxRequest(function() {
                        
                        var userinfo = JSON.parse(this);
                        
                        var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                        var sender_rsakey = "";
                        var tftxt = 'FROM: ';
                        try {
                            
                            if (jsonobj.enc_sender == "") {
                                sender_rsakey = getRSAkeyFromSesskey(privkey,sesskey);
                                tftxt = 'TO: ';
                            } else {
                                sender_rsakey = publicPEMtoRSAKey(userinfo[0].pubkey);
                            }
                            
                            var isVerified = ttRSAverifyWkey(sender_rsakey,jsonobj.enc_body,signature);
                            if (isVerified) {
                                verified = "<span style=\"color:#009900\">VERIFIED</span>";
                            }
                            
                        } catch(err) {
                            
                        }
                        
                        var verified_str = "<span title=\"" + signature + "\">" + verified + "</span>";
                        
                        // link str
                        var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': userinfo[0].user.email}));
                        var plink = document.createElement("a");
                        plink.setAttribute('href',deststr);
                        
                        // assemble the elements on to the div
                        var pheading = document.createElement("div");
                        var headstr = "<h2>" + userinfo[0].user.last_name + ", " + userinfo[0].user.first_name + "</h2><h3><em>" + userinfo[0].user.username + "</em> (" + userinfo[0].user.email + ")</h3>";
                        headstr = tftxt+"<span title=\"" + userinfo[0].pubkey + "\">" + headstr + "</span>";
                        //headstr = headstr + "<h4><em>" + getLocalTimeFromGMT(ctime) + "</em></h4>";
                        plink.innerHTML = headstr;
                        pheading.appendChild(plink);
                        pheading.innerHTML = pheading.innerHTML + "<h4><em>" + getLocalTimeFromGMT(ctime) + "</em></h4>";
                        
                        var pheadingAlign = document.createElement("div")
                        pheadingAlign.setAttribute('style','float: right; padding: 10px 10px 10px 10px');
                        pheadingAlign.appendChild(pheading);
                        
                        //var psubject = document.createElement("div");
                        //psubject.innerHTML = "<p><h3>Subject:</h3></p><p>" + csubject + "</p>";
                        //
                        //var pbody    = document.createElement("div");
                        //pbody.innerHTML = "<p><h3>Body:</h3></p><p>" + cbody + "</p>";
                        
                        var msgtable = document.createElement("table");
                        msgtable.setAttribute('width','100%');
                        msgtable.setAttribute('style','align: left');
                        ttRow(msgtable,['<h3>Subject</h3>',csubject]);
                        ttRow(msgtable,['<h3>Body</h3>',cbody]);
                        
                        var psig     = document.createElement("div");
                        psig.innerHTML = verified_str;
                        pheadingAlign.appendChild(psig);
                        
                        // Actions:
                        var actionDiv = document.createElement("div");
                        
                        // Reply
                        var rlink = "";
                        if (jsonobj.enc_receiver == "") { // only reply to messages not sent from you
                            
                            rlink = document.createElement("a");
                            rlink.innerHTML = "Reply";
                            rlink.className = "button button-small";
                            rlink.data_user_id = userinfo[0].user.user_id;
                            rlink.data_user_email = userinfo[0].user.email;
                            rlink.data_user_key = userinfo[0].pubkey;
                            rlink.addEventListener('click',function() {
                                    selRecPMsg(this.data_user_id,this.data_user_email,this.data_user_key);    
                                },false);
                            
                            if (rlink.data_user_email != null) {
                                actionDiv.innerHTML = "<h3>Action</h3>";
                                actionDiv.appendChild(rlink);
                            }
                            
                        }
                        
                        
                        /* // msg_id
                        var dlink = "";
                        if (jsonobj.enc_sender == "") {
                            
                            dlink = document.createElement("a");
                            dlink.innerHTML = "Delete";
                            dlink.className = "button button-small";
                            dlink.data_msg_id = jsonobj[akey].pmessage_sent_id;
                            dlink.addEventListener('click',function() {
                                    deletePMsg('sent',this.data_msg_id,displayPMsgSent,divid);    
                                },false);
                            
                        } else {
                            
                            dlink = document.createElement("a");
                            dlink.innerHTML = "Delete";
                            dlink.className = "button button-small";
                            dlink.data_msg_id = jsonobj[akey].pmessage_inbox_id;
                            dlink.addEventListener('click',function() {
                                    deletePMsg('inbox',this.data_msg_id,displayPMsgRead,divid);    
                                },false);
                        }
                        //*/
                        
                        
                        
                        
                        
                        
                        // append
                        document.getElementById(divid).innerHTML = "";
                        document.getElementById(divid).appendChild(pheadingAlign);
                        //document.getElementById(divid).appendChild(psig);
                        //document.getElementById(divid).appendChild(psubject);
                        //document.getElementById(divid).appendChild(pbody);
                        document.getElementById(divid).appendChild(msgtable);
                        document.getElementById(divid).appendChild(actionDiv);
                        
                        document.getElementById(divid).className = "divmh";
                        
                    },'ajaxHTML.php','page=getUserInfo&sesskey='+ encodeURIComponent(window.sesskey) +'&user_ids='+JSON.stringify({needed_user_ids:other_id,
                                                                                                                                  user_mapping:user_mapping,
                                                                                                                                  table:'pmessage_view'}));
                } catch(err) {
                    document.getElementById(divid).innerHTML = "<p>This message could not be decrypted. You must be logged in to decrypt messages.</p>";
                }
            };
            
            if (typeof privkey != "undefined" && privkey != '') {
                finishPMsgView();
            } else {
                getPersonalKeys(finishPMsgView);
            }
        
        
        
        } catch(err) {
            
        }
        
        
        
    }

    function displayPMsgSent(divid) {
        
        var argstr = "page=getPMsgSent&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            //console.log(this);
            var jsonobj = JSON.parse(this);
            //console.log(jsonobj[0]);
            
            var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
            // compile list of needed user_ids
            var user_mapping = [];
            var needed_user_ids = [];
            for (var akey in jsonobj) {
                //console.log(akey);
                var enc_key = jsonobj[akey].enc_key;
                var enc_receiver = jsonobj[akey].enc_receiver;
                
                if (enc_receiver != "") {
                    var mode = jsonobj[akey].enc_mode;
                    var pln_key = my_rsa.decrypt(enc_key);
                    //console.log('pln_key: '+pln_key);
                    //console.log('enc_sender: '+enc_sender);
                    //console.log('mode: '+mode);
                    var receiver_id = ttAESdecrypt(pln_key,enc_receiver,mode);
                    
                    user_mapping.push({user_id:receiver_id,
                                       enc_user_id:enc_receiver});
                    
                    if (needed_user_ids.indexOf(receiver_id) < 0) { // not in array yet
                        needed_user_ids.push(receiver_id);
                        //console.log('sender_id: '+sender_id);
                    }
                }
            }
            
            PostAjaxRequest(function() {
                
                //console.log(this);
                var userinfo = JSON.parse(this);
                //console.log(userinfo);
                
                var disptable = document.createElement("table");
                ttRow(disptable,['<h2>To</h2>','<h2>Signature</h2>','<h2>Subject</h2>','<h2>Action</h2>']);
                for (var akey in jsonobj) {
                    // decrypt sender
                    var enc_key = jsonobj[akey].enc_key;
                    var pln_key = my_rsa.decrypt(enc_key);
                    var mode = jsonobj[akey].enc_mode;
                    var enc_receiver = jsonobj[akey].enc_receiver;
                    var enc_signature = jsonobj[akey].enc_signature;
                    
                    var enc_subject = jsonobj[akey].enc_subject;
                    var enc_body    = jsonobj[akey].enc_body;
                    
                    var receiver_id = ttAESdecrypt(pln_key,enc_receiver,mode);
                    var signature = ttAESdecrypt(pln_key,enc_signature,mode);
                    
                    var receiver_info = "";
                    var receiver_key = "";
                    var receiver_str = "";
                    for (var ukey in userinfo) {
                        //console.log('ukey: '+ukey);
                        //console.log('user_id: '+userinfo[ukey].user_id);
                        if (userinfo[ukey].user_id == receiver_id) {
                            receiver_info = userinfo[ukey];
                            receiver_key  = receiver_info.pubkey;
                            receiver_str  = receiver_info.user.last_name + ", " + receiver_info.user.first_name + "<br />\n(" + receiver_info.user.email + ") ";
                            
                            // link str
                            var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': receiver_info.user.email}));
                            var plink = document.createElement("a");
                            plink.setAttribute('href',deststr);
                            plink.innerHTML = receiver_str;
                            
                            var spRecStr = document.createElement("span");
                            spRecStr.setAttribute('title',receiver_key);
                            spRecStr.appendChild(plink);
                            //receiver_str = "<span title=\"" + receiver_key + "\">" + receiver_str + "</span>";
                        }
                    }
                    
                    var sender_rsakey = getRSAkeyFromSesskey(privkey,sesskey);
                    var isVerified = ttRSAverifyWkey(sender_rsakey,enc_body,signature);
                    var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                    if (isVerified) {
                        verified = "<span style=\"color:#009900\">VERIFIED</span>";
                    }
                    var verified_str = "<span title=\"" + signature + "\">" + verified + "</span>";
                    
                    var csubject = ttAESdecrypt(pln_key,enc_subject,mode);
                    var cbody    = ttAESdecrypt(pln_key,enc_body,mode);
                    //console.log('pln_key: '+pln_key);
                    //console.log('enc_subject: '+enc_subject);
                    //console.log('csubject: '+csubject);
                    
                    var deststr = "#pmessage-pmessageView-" + encodeURIComponent(JSON.stringify({'enc_key': enc_key,
                                                                              'enc_mode': mode,
                                                                              'enc_subject': enc_subject,
                                                                              'enc_body': enc_body,
                                                                              'enc_signature': enc_signature,
                                                                              'enc_sender': '',
                                                                              'enc_receiver': enc_receiver,
                                                                              'post_time': ''}));
                    
                    var alink = document.createElement("a");
                    alink.setAttribute("href",deststr);
                    alink.innerHTML = "View";
                    alink.className = "button button-small";
                    
                    var dlink = document.createElement("a");
                    dlink.innerHTML = "Delete";
                    dlink.className = "button button-small";
                    dlink.data_msg_id = jsonobj[akey].pmessage_sent_id;
                    dlink.addEventListener('click',function() {
                            this.className = "hidden";
                            deletePMsg('sent',this.data_msg_id,displayPMsgSent,divid);    
                        },false);
                    
                    var actionDiv = document.createElement("div");
                    actionDiv.appendChild(alink);
                    actionDiv.appendChild(dlink);
                    
                    ttRow(disptable,[spRecStr,verified_str,csubject,actionDiv]);
                    
                }
                
                // append to divid
                document.getElementById(divid).innerHTML = ""; // clear before appending
                document.getElementById(divid).appendChild(disptable);
                
                
                
            },'ajaxHTML.php','page=getUserInfo&sesskey='+ encodeURIComponent(window.sesskey) +'&user_ids='+JSON.stringify({needed_user_ids:needed_user_ids,
                                                                                                                          user_mapping:user_mapping,
                                                                                                                          table:'pmessage_sent'}));
            // end receipt of user info
                
            

            
        },'ajaxHTML.php',argstr);
     
        
    } // end display PMsgSent 

    function displayPMsgRead(divid) {
        
        var argstr = "page=getPMsgInbox&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            //console.log(this);
            var jsonobj = JSON.parse(this);
            //console.log(jsonobj[0]);
            
            var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
            
            // compile list of needed user_ids
            var needed_user_ids = [];
            for (var akey in jsonobj) {
                //console.log(akey);
                var enc_key = jsonobj[akey].enc_key;
                var enc_sender = jsonobj[akey].enc_sender;
                
                if (enc_sender != "") {
                    var mode = jsonobj[akey].enc_mode;
                    var pln_key = my_rsa.decrypt(enc_key);
                    //console.log('pln_key: '+pln_key);
                    //console.log('enc_sender: '+enc_sender);
                    //console.log('mode: '+mode);
                    var sender_id = ttAESdecrypt(pln_key,enc_sender,mode);
                    
                    if (needed_user_ids.indexOf(sender_id) < 0) { // not in array yet
                        needed_user_ids.push(sender_id);
                        //console.log('sender_id: '+sender_id);
                    }
                }
            }
            
            PostAjaxRequest(function() {
                
                //console.log(this);
                var userinfo = JSON.parse(this);
                //console.log(userinfo);
                
                var disptable = document.createElement("table");
                ttRow(disptable,['<h2>From</h2>','<h2>Signature</h2>','<h2>Subject</h2>','<h2>Time</h2>','<h2>Action</h2>']);
                for (var akey in jsonobj) {
                    
                    // decrypt sender
                    var enc_key = jsonobj[akey].enc_key;
                    var pln_key = my_rsa.decrypt(enc_key);
                    var mode = jsonobj[akey].enc_mode;
                    var enc_sender = jsonobj[akey].enc_sender;
                    var enc_signature = jsonobj[akey].enc_signature;
                    
                    var enc_subject = jsonobj[akey].enc_subject;
                    var enc_body    = jsonobj[akey].enc_body;
                    
                    var post_time   = getLocalTimeFromGMT(jsonobj[akey].post_time);
                    
                    var sender_id = "";
                    var signature = "";
                    var verified_str = "";
                    var sender_str  = "";
                    if (enc_sender != "") {
                        
                        sender_id = ttAESdecrypt(pln_key,enc_sender,mode);
                        signature = ttAESdecrypt(pln_key,enc_signature,mode);
                        
                        
                        // get sender info
                        var sender_info = "";
                        var sender_key  = "";
                        for (var ukey in userinfo) {
                            //console.log('ukey: '+ukey);
                            //console.log('user_id: '+userinfo[ukey].user_id);
                            if (userinfo[ukey].user_id == sender_id) {
                                sender_info = userinfo[ukey];
                                sender_key  = sender_info.pubkey;
                                sender_str  = sender_info.user.last_name + ", " + sender_info.user.first_name + "<br />\n(" + sender_info.user.email + ") ";
                                
                                // link str
                                var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': sender_info.user.email}));
                                var plink = document.createElement("a");
                                plink.setAttribute('href',deststr);
                                plink.innerHTML = sender_str;
                                
                                var spSendStr = document.createElement("span");
                                spSendStr.setAttribute('title',sender_key);
                                spSendStr.appendChild(plink);
                                
                                //sender_str = "<span title=\"" + sender_key + "\">" + sender_str + "</span>";
                            }
                        }
                        
                        //console.log('sender_str: '+sender_str);
                        //console.log('sender_key: '+sender_key);
                        var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                        try {
                            var sender_rsakey = publicPEMtoRSAKey(sender_key);
                            var isVerified = ttRSAverifyWkey(sender_rsakey,enc_body,signature);
                            
                            if (isVerified) {
                                verified = "<span style=\"color:#009900\">VERIFIED</span>";
                            }
                        } catch(err) {
                            
                        }
                        
                        verified_str = "<span title=\"" + signature + "\">" + verified + "</span>";
                        
                    } // end get sender info
                    
                    
                    
                    var csubject = ttAESdecrypt(pln_key,enc_subject,mode);
                    var cbody    = ttAESdecrypt(pln_key,enc_body,mode);
                    
                    //bodybtn = document.createElement("input");
                    //bodybtn.setAttribute("type","button");
                    
                    var deststr = "#pmessage-pmessageView-" + encodeURIComponent(JSON.stringify({'enc_key': enc_key,
                                                                              'enc_mode': mode,
                                                                              'enc_subject': enc_subject,
                                                                              'enc_body': enc_body,
                                                                              'enc_signature': enc_signature,
                                                                              'enc_sender': enc_sender,
                                                                              'enc_receiver': '',
                                                                              'post_time': jsonobj[akey].post_time}));
                    
                    var alink = document.createElement("a");
                    alink.setAttribute("href",deststr);
                    alink.innerHTML = "View";
                    alink.className = "button button-small";
                    
                    var dlink = document.createElement("a");
                    dlink.innerHTML = "Delete";
                    dlink.className = "button button-small";
                    dlink.data_msg_id = jsonobj[akey].pmessage_inbox_id;
                    dlink.addEventListener('click',function() {
                            this.className = "hidden";
                            deletePMsg('inbox',this.data_msg_id,displayPMsgRead,divid);    
                        },false);
                    
                    var actionDiv = document.createElement("div");
                    actionDiv.appendChild(alink);
                    actionDiv.appendChild(dlink);
                    
                    ttRow(disptable,[spSendStr,verified_str,csubject,post_time,actionDiv]);
                }
                
                
                
                
                
                //append to divid
                document.getElementById(divid).innerHTML = ""; // clear before appending
                document.getElementById(divid).appendChild(disptable);
                
                
            },'ajaxHTML.php','page=getUserInfo&sesskey='+ encodeURIComponent(window.sesskey) +'&user_ids='+JSON.stringify({needed_user_ids:needed_user_ids}));
            // end receipt of user info
            
            
        },'ajaxHTML.php',argstr);
    } // end display PMsgRead

    function displayVouches(divid) {
        
        var argstr = "page=getVouches&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            //console.log(this);
            var jsonobj = JSON.parse(this);
            
            //console.log(jsonobj);
            
            var msg = jsonobj.myemail + jsonobj.mykey;
            
            var voucheme     = jsonobj.voucheme;
            var voucheothers = jsonobj.voucheothers;
            var blockme      = jsonobj.blockme;
            var blockothers  = jsonobj.blockothers;
            //console.log('voucheme: '+voucheme);
            
            var disptable = document.createElement("table");
                var trow = document.createElement("tr");
                trow.setAttribute("valign","top");
                var tcell = document.createElement("td");
                tcell.setAttribute("colspan","5");
                tcell.innerHTML = "<h1>These People Have Connected With Me:</h1>";
                trow.appendChild(tcell);
                disptable.appendChild(trow);
            ttRow(disptable,['<h2>Contact</h2>','<h2>Their Signature</h2>','<h2>Type</h2>','<h2>Sign Time</h2>','<h2>Action</h2>']);
            
            //var disptext = "";
            //disptext += "<table>\n";
            //disptext += "<tr><td colspan=\"5\"><h1>People Who Know Me:</h1></td></tr>\n";
            //disptext += "<tr valign=\"top\">\n";
            //disptext += "<td>Contact:</td><td>Signature:</td><td>Type:</td><td>Sign Time:</td><td>Action:</td>\n";
            //disptext += "</tr>\n";
            for (var n=0; n<voucheme.length; n++) {
                
                var signature = voucheme[n].signature['signature'];
                var signtype  = voucheme[n].signature['type'];
                var signtime  = voucheme[n].signature['sign_time'];
                
                // link str
                var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': voucheme[n].user['email']}));
                var plink = document.createElement("a");
                plink.setAttribute('href',deststr);
                plink.innerHTML = voucheme[n].user['last_name'] + ", " + voucheme[n].user['first_name'] + " (" + voucheme[n].user['email'] + ")";
                
                var user = document.createElement("span");
                user.setAttribute('title',voucheme[n].key);
                user.appendChild(plink);
                
                //var user      = "<span title=\"" + voucheme[n].key + "\">" + voucheme[n].user['last_name'] + ", " + voucheme[n].user['first_name'] + " (" + voucheme[n].user['email'] + ")</span>";
                
                //console.log(voucheme[n].key);
                var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                try {
                    var otherRSAkey = publicPEMtoRSAKey(voucheme[n].key);
                    //console.log(signature);
                    var isVerified = otherRSAkey.verifyString(msg,signature);
                    
                    if (isVerified) {
                        verified = "<span style=\"color:#009900\">VERIFIED</span>";
                    }
                } catch(err) {
                    
                }
                
                var verstr = "<span title=\"" + signature + "\">" + verified + "</span>";
                
                //disptext += "<tr valign=\"top\">\n";
                //disptext += "<td>" + user + "</td><td>" + verstr + "</td><td>" + signtype + "</td><td>" + getLocalTimeFromGMT(signtime) + "</td>"; // only 4 cells, append the 5th
                
                
                // Action cell
                var tcell = document.createElement("td");
                
                // Sign link
                var alink = document.createElement("input");
                alink.type = "button"
                alink.className = "button button-small";
                alink.value = "Connect";
                //alink.data_user_all = voucheme[n].user;
                alink.data_user_id = voucheme[n].user['user_id'];
                alink.data_email   = voucheme[n].user['email'];
                alink.data_ukey    = voucheme[n].key;
                alink.addEventListener('click',function() {
                    ttvouche(this.data_user_id,this.data_email,this.data_ukey);
                },false);
                //tcell.appendChild(alink);
                //disptext.appendChild(tcell);
                //disptext += "<td>" + tcell.innerHTML + "</td>\n";
                
                // if this user is in voucheothers, do not display the Sign option
                var voucheothers = jsonobj.voucheothers;
                var containsUser = false;
                for (var m=0; m<voucheothers.length; m++) {
                    if (voucheothers[m].user['user_id'] == voucheme[n].user['user_id']) {
                        containsUser = true;
                        break;
                    }
                }
                
                if (!containsUser) {
                    tcell.appendChild(alink);
                }
                
                containsUser = false;
                for (var m=0; m<blockme.length; m++) {
                    //console.log('blocked: '+blockothers[m].user['user_id']);
                    if (blockme[m].user['user_id'] == voucheme[n].user['user_id']) {
                        containsUser = true;
                        break;
                    }
                }
                
                if (containsUser) {
                    // user has blocked you
                    var blockstr = document.createElement("span");
                    blockstr.setAttribute('title','This user will not receive invitations or messages from you.');
                    blockstr.innerHTML = "<br /><span style=\"color:#990000\">This user has blocked you.</span>";
                    user.appendChild(blockstr);
                }
                
                // Send Message
                var rlink = "";
                rlink = document.createElement("a");
                rlink.innerHTML = "Send Message";
                rlink.className = "button button-small";
                rlink.data_user_id = voucheme[n].user['user_id'];
                rlink.data_user_email = voucheme[n].user['email'];
                rlink.data_user_key = voucheme[n].key;
                rlink.addEventListener('click',function() {
                        selRecPMsg(this.data_user_id,this.data_user_email,this.data_user_key);    
                    },false);
                
                if (rlink.data_user_email != null) {
                    tcell.appendChild(rlink);
                }
                
                
                    
                
                
                //if (containsUser) {
                //    ttRow(disptable,[user,verstr,signtype,getLocalTimeFromGMT(signtime),""]);
                //} else {
                    ttRow(disptable,[user,verstr,signtype,getLocalTimeFromGMT(signtime),tcell]);
                //}
                
                
                //disptext += "</tr>\n";
            }
            //disptext += "</table>\n";
            
            
            // ------------------ OTHERS --------------------------------------------
            
            
            //var voucheothers = jsonobj.voucheothers; // moved above
            
            var trow = document.createElement("tr");
                trow.setAttribute("valign","top");
                var tcell = document.createElement("td");
                tcell.setAttribute("colspan","5");
                tcell.innerHTML = "<h1>I Have Connected With These People:</h1>";
                trow.appendChild(tcell);
                disptable.appendChild(trow);
            ttRow(disptable,['<h2>Contact</h2>','<h2>My Signature</h2>','<h2>Type</h2>','<h2>Sign Time</h2>','<h2>Action</h2>']);
            
            //disptext += "<tr><td colspan=\"5\"><h1>People I Know:</h1></td></tr>\n";
            //disptext += "<table>\n";
            //disptext += "<tr valign=\"top\">\n";
            //disptext += "<td>Contact:</td><td>Signature:</td><td>Type:</td><td>Sign Time:</td><td>Action:</td>\n";
            //disptext += "</tr>\n";
            for (var n=0; n<voucheothers.length; n++) {
                
                var signature = voucheothers[n].signature['signature'];
                var signtype  = voucheothers[n].signature['type'];
                var signtime  = voucheothers[n].signature['sign_time'];
                
                // link str
                var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': voucheothers[n].user['email']}));
                var plink = document.createElement("a");
                plink.setAttribute('href',deststr);
                plink.innerHTML = voucheothers[n].user['last_name'] + ", " + voucheothers[n].user['first_name'] + " (" + voucheothers[n].user['email'] + ")";
                
                var user = document.createElement("span");
                user.setAttribute('title',voucheothers[n].key);
                user.appendChild(plink);
                
                //var user      = "<span title=\"" + voucheothers[n].key + "\">" + voucheothers[n].user['last_name'] + ", " + voucheothers[n].user['first_name'] + " (" + voucheothers[n].user['email'] + ")</span>";
                
                //console.log(voucheothers[n].key);
                //console.log(jsonobj.mykey);
                //console.log(window.sesskey);
                var myRSAkey   = getRSAkeyFromSesskey(window.privkey,window.sesskey);
                //console.log(signature);
                var theircrt = voucheothers[n].user['email'] + voucheothers[n].key;
                var isVerified = myRSAkey.verifyString(theircrt,signature);
                var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                if (isVerified) {
                    verified = "<span style=\"color:#009900\">VERIFIED</span>";
                }
                
                var verstr = "<span title=\"" + signature + "\">" + verified + "</span>";
                
                //disptext += "<tr valign=\"top\">\n";
                //disptext += "<td>" + user + "</td><td>" + verstr + "</td><td>" + signtype + "</td><td>" + getLocalTimeFromGMT(signtime) + "</td><td></td>\n";
                
                // Action cell
                var tcell = document.createElement("td");
                
                // Block link **********************************************
                
                // if this user is in blockothers, do not display the Block option, display Unblock option
                var blockothers = jsonobj.blockothers;
                var containsUser = false;
                for (var m=0; m<blockothers.length; m++) {
                    //console.log('blocked: '+blockothers[m].user['user_id']);
                    if (blockothers[m].user['user_id'] == voucheothers[n].user['user_id']) {
                        containsUser = true;
                        break;
                    }
                }
                
                if (!containsUser) {
                    var alink = document.createElement("input");
                    alink.type = "button"
                    alink.className = "button button-small";
                    alink.value = "Block";
                    //alink.data_user_all = voucheme[n].user;
                    alink.data_user_id = voucheothers[n].user['user_id'];
                    alink.data_email   = voucheothers[n].user['email'];
                    alink.data_ukey    = voucheothers[n].key;
                    alink.addEventListener('click',function() {
                        ttblock(this.data_user_id,this.data_email,this.data_ukey);
                    },false);
                    //tcell.appendChild(alink);
                    //disptext.appendChild(tcell);
                    //disptext += "<td>" + tcell.innerHTML + "</td>\n";
                } else {
                    var alink = document.createElement("input");
                    alink.type = "button"
                    alink.className = "button button-small button-red";
                    alink.value = "Unblock";
                    //alink.data_user_all = voucheme[n].user;
                    alink.data_user_id = voucheothers[n].user['user_id'];
                    alink.data_email   = voucheothers[n].user['email'];
                    alink.data_ukey    = voucheothers[n].key;
                    alink.addEventListener('click',function() {
                        ttunblock(this.data_user_id,this.data_email,this.data_ukey);
                    },false);
                    //tcell.appendChild(alink);
                    //disptext.appendChild(tcell);
                    //disptext += "<td>" + tcell.innerHTML + "</td>\n";
                }
                
                //if (!containsUser) {
                    tcell.appendChild(alink);
                //}
                // end block ************************************************
                
                
                // Send Message
                var rlink = "";
                rlink = document.createElement("a");
                rlink.innerHTML = "Send Message";
                rlink.className = "button button-small";
                rlink.data_user_id = voucheothers[n].user['user_id'];
                rlink.data_user_email = voucheothers[n].user['email'];
                rlink.data_user_key = voucheothers[n].key;
                rlink.addEventListener('click',function() {
                        selRecPMsg(this.data_user_id,this.data_user_email,this.data_user_key);    
                    },false);
                
                if (rlink.data_user_email != null) {
                    tcell.appendChild(rlink);
                }
                
                ttRow(disptable,[user,verstr,signtype,getLocalTimeFromGMT(signtime),tcell]);
                
                //disptext += "</tr>\n";
            }
            //disptext += "</table>\n";
            
            // ------------------------- TO DOM -------------------------------
            //$('#'+divid).html(disptext);
            document.getElementById(divid).innerHTML = ""; // clear the slate before appending to it
            document.getElementById(divid).appendChild(disptable);
            
        },'ajaxHTML.php',argstr);
        
    }
    
    function displayNotifications(divid) {
        
        var argstr = "page=getNotifications&sesskey=" + encodeURIComponent(window.sesskey);
        
        var numNots = 0;
        
        PostAjaxRequest(function() {
            //console.log(this);
            var jsonobj = JSON.parse(this);
            //console.log(jsonobj);
            
            numNots = jsonobj.length;
            
            var my_rsa = getRSAkeyFromSesskey(privkey,sesskey);
            // compile list of needed user_ids
            var user_mapping = [];
            var needed_user_ids = [];
            for (var akey in jsonobj) {
                //console.log(akey);
                var enc_key = jsonobj[akey].enc_key;
                var enc_sender_id = jsonobj[akey].enc_sender_id;
                
                if (enc_sender_id != "") {
                    var mode = jsonobj[akey].enc_mode;
                    var pln_key = my_rsa.decrypt(enc_key);
                    //console.log('pln_key: '+pln_key);
                    //console.log('enc_sender: '+enc_sender);
                    //console.log('mode: '+mode);
                    var sender_id = ttAESdecrypt(pln_key,enc_sender_id,mode);
                    
                    user_mapping.push({user_id:sender_id,
                                       enc_user_id:enc_sender_id});
                    
                    if (needed_user_ids.indexOf(sender_id) < 0) { // not in array yet
                        needed_user_ids.push(sender_id);
                        //console.log('sender_id: '+sender_id);
                    }
                }
            }
            
            PostAjaxRequest(function() {
                
                //console.log(this);
                var userinfo = JSON.parse(this);
                //console.log(userinfo);
                
                var disptable = document.createElement("table");
                disptable.setAttribute('width','740px');
                ttRow(disptable,['<h2>From:</h2>','<h2>Signature:</h2>','<h2>Message:</h2>','<h2>Link:</h2>']);
                for (var akey in jsonobj) {
                    // decrypt sender
                    var enc_key = jsonobj[akey].enc_key;
                    var pln_key = my_rsa.decrypt(enc_key);
                    var mode = jsonobj[akey].enc_mode;
                    var enc_sender_id = jsonobj[akey].enc_sender_id;
                    var enc_signature = jsonobj[akey].enc_signature;
                    
                    var enc_content = jsonobj[akey].enc_content;
                    var enc_link    = jsonobj[akey].enc_link;
                    
                    var sender_id = ttAESdecrypt(pln_key,enc_sender_id,mode);
                    var signature = ttAESdecrypt(pln_key,enc_signature,mode);
                    
                    var sender_info = "";
                    var sender_key = "";
                    var sender_str = "";
                    for (var ukey in userinfo) {
                        //console.log('ukey: '+ukey);
                        //console.log('user_id: '+userinfo[ukey].user_id);
                        if (userinfo[ukey].user_id == sender_id) {
                            sender_info = userinfo[ukey];
                            sender_key  = sender_info.pubkey;
                            sender_str  = sender_info.user.last_name + ", " + sender_info.user.first_name + "<br />\n(" + sender_info.user.email + ") ";
                            
                            // link str
                            var deststr = "#people-peoplePublic-" + encodeURIComponent(JSON.stringify({'email': sender_info.user.email}));
                            var plink = document.createElement("a");
                            plink.setAttribute('href',deststr);
                            plink.innerHTML = sender_str;
                            
                            var spRecStr = document.createElement("span");
                            spRecStr.setAttribute('title',sender_key);
                            spRecStr.appendChild(plink);
                            //sender_str = "<span title=\"" + sender_key + "\">" + sender_str + "</span>";
                        }
                    }
                    
                    //var deststr = "#pmessage-pmessageView-" + encodeURIComponent(JSON.stringify({'enc_key': enc_key,
                    //                                                          'enc_mode': mode,
                    //                                                          'enc_subject': enc_subject,
                    //                                                          'enc_body': enc_body,
                    //                                                          'enc_signature': enc_signature,
                    //                                                          'enc_sender': '',
                    //                                                          'enc_sender_id': enc_sender_id,
                    //                                                          'post_time': ''}));
                    
                    var ccontent = ttAESdecrypt(pln_key,enc_content,mode);
                    var deststr = ttAESdecrypt(pln_key,enc_link,mode);
                    
                    var isVerified = false;
                    try {
                        var sender_rsakey = publicPEMtoRSAKey(sender_key); //getRSAkeyFromSesskey(privkey,sesskey);
                        isVerified = ttRSAverifyWkey(sender_rsakey,ccontent+deststr,signature);
                    } catch(err) {
                        
                    }
                    
                    //wrap the message
                    ccontent = wordwrap(ccontent,50,'<br />\n',true);
                    
                    var verified = "<span style=\"color:#990000\">UNVERIFIED</span>";
                    if (isVerified) {
                        verified = "<span style=\"color:#009900\">VERIFIED</span>";
                    }
                    var verified_str = "<span title=\"" + signature + "\">" + verified + "</span>";
                    
                    
                    //var cbody    = ttAESdecrypt(pln_key,enc_body,mode);
                    //console.log('pln_key: '+pln_key);
                    //console.log('enc_subject: '+enc_subject);
                    //console.log('csubject: '+csubject);
                    
                    
                    var alink = document.createElement("a");
                    alink.setAttribute("href",deststr);
                    alink.innerHTML = "Open";
                    alink.className = "button button-small";
                    
                    //var dlink = document.createElement("a");
                    //dlink.innerHTML = "Delete";
                    //dlink.className = "button button-small";
                    //dlink.data_msg_id = jsonobj[akey].pmessage_sent_id;
                    //dlink.addEventListener('click',function() {
                    //        this.className = "hidden";
                    //        deletePMsg('sent',this.data_msg_id,displayPMsgSent,divid);    
                    //    },false);
                    
                    var actionDiv = document.createElement("div");
                    actionDiv.appendChild(alink);
                    //actionDiv.appendChild(dlink);
                    
                    ttRow(disptable,[spRecStr,verified_str,ccontent,actionDiv]);
                    
                }
                
                // append to divid
                document.getElementById(divid).innerHTML = ""; // clear before appending
                document.getElementById(divid).appendChild(disptable);
                
                var numTxt = document.createElement("p");
                numTxt.innerHTML = "Sorted by recency. Only the most recent "+numNots+" notifications are shown.";
                document.getElementById(divid).appendChild(numTxt);
                
                
                
            },'ajaxHTML.php','page=getUserInfo&sesskey='+ encodeURIComponent(window.sesskey) +'&user_ids='+JSON.stringify({needed_user_ids:needed_user_ids,
                                                                                                                          user_mapping: user_mapping,
                                                                                                                          table: 'notification'}));
            // end receipt of user info
                
            

            
        },'ajaxHTML.php',argstr);
        
    } // end displayNotifications
    
    function displayTitleBar(divid,callback) {
        
        var argstr = "page=displayTitleBar&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            var jsonobj = JSON.parse(this);
            
            var alink = document.createElement("a");
            alink.setAttribute('href',ttPeoplePublicLink(jsonobj.email));
            alink.innerHTML = "<h1><span>" + jsonobj.last_name + ", " + jsonobj.first_name + "</span></h1>\n" + "<h2>" + jsonobj.email + " (" + jsonobj.username + ")</h2>\n";
            
            var spInfo = document.createElement("span");
            spInfo.setAttribute('title',jsonobj.pubkey);
            spInfo.appendChild(alink);
            
            document.getElementById(divid).innerHTML = "";
            document.getElementById(divid).appendChild(spInfo);
            
            callback();
            
        },'ajaxHTML.php',argstr);
    }
    
    function displaySelfProfile(divid) {
        
        var argstr = "page=displaySelfProfile&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            //console.log(this);
        
            document.getElementById(divid).innerHTML = this;
            
        },'ajaxHTML.php',argstr);
    }

    function navDemo() {
        
        var argstr = "page=demo&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            document.body.className = "right-sidebar";
            document.getElementById("main-wrapper").innerHTML = this;
            
            ttinit();
            genRSAkey();
            
            
        },'ajaxHTML.php',argstr);
        
        
        
    }
    
    function navHome() {
        
        var argstr = "page=home&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            document.body.className = "homepage";
            document.getElementById("main-wrapper").innerHTML = this;
            
            ttinit();
            
            
            
        },'ajaxHTML.php',argstr);
    }
    
    function navFindPeople() {
        
        var argstr = "page=findpeople&sesskey=" + encodeURIComponent(window.sesskey);
        
        PostAjaxRequest(function() {
            
            document.getElementById("main-wrapper").innerHTML = this;
            
            
        },'ajaxHTML.php',argstr);
        
    }

// ---------------------- PROFILE --------------------------------------

    function toggleHTMLkey(divid) {
        //console.log(document.getElementById(divid).className);
        if (document.getElementById(divid).className == 'hidden htmlkey') {
            document.getElementById(divid).className='visible htmlkey';
        } else {
            document.getElementById(divid).className='hidden htmlkey';
        }
    }
    
    function getPersonalKeys(callback) {
        
        var argstr = "page=getPersonalKeys";
        
        PostAjaxRequest(function() {
            
            var jsonobj = JSON.parse(this);
            //console.log(jsonobj);
            
            if (jsonobj.status == 1) { // logged in
                pubkey = jsonobj.pubkey;
                privkey = jsonobj.privkey;
                
                window.pubkey = pubkey;
                window.privkey = privkey;
            }
            
            
            callback();
            
        },'ajaxPublic.php',argstr);
    }

    function profileTogglePasswordDiv() {
                            
        if (document.getElementById('changePassBox').checked) {
            document.getElementById('profileNewPassword').className = "visible";
        } else {
            document.getElementById('profileNewPassword').className = "hidden";
        }
        
    }
    
    function ttLookupEmail(form) {
        
        //var email = document.getElementById('emailLookup').value;
        var email = form.elements['emailLookup'].value;
        var link  = ttPeoplePublicLink(email);
        
        window.location.hash = link;

        // scroll to top of page
        document.body.scrollTop = document.documentElement.scrollTop = 0;
    }

    function finishModify() {
        
        userVars = document.modform;
        
        profileFormCheck = checkUserForm(userVars);
        
        if (profileFormCheck) {
            
            if (userVars.changePassBox.checked) {
                userVars.changePassBox.value = 'on';
            } else {
                userVars.changePassBox.value = 'off';
            }
            
        
            // hash password before submitting to server
            userVars.pass.value = pidCrypt.SHA512(userVars.passwd.value);
            userVars.new_pass1.value = pidCrypt.SHA512(userVars.new_pass1wd.value);
            userVars.new_pass2.value = pidCrypt.SHA512(userVars.new_pass2wd.value);
            
            
            // create the new potential certificate
            //var mypem = '<?php echo echoKey($rsakey->privkey); ?>';
            var mykey = getRSAkeyFromSesskey(window.privkey,window.sesskey);//privatePEMtoRSAKey(mypem,userVars.passwd.value);
            userVars.new_privkey.value = privateRSAKeytoPEM(mykey,userVars.new_pass1wd.value);
            
            // encrypt password to store in cookie for loading the user private key
            var sesskey = window.sesskey; //'<?php echo $_SESSION['sesskey']; ?>';
            var aes = new pidCrypt.AES.CTR();
            var encpass = aes.encryptText(userVars.new_pass1wd.value,sesskey,{nbits:256}); // save new cookie data in case of password change (handled in ajax callback)
            
            
            
            // clear cleartext password before sending form to server
            var rng = new SecureRandom();
            var randnum = new BigInteger(512,rng);
            userVars.passwd.value = randnum.toString(16); // won't be used for anything
            randnum = new BigInteger(512,rng);
            userVars.new_pass1wd.value = randnum.toString(16); // won't be used for anything
            randnum = new BigInteger(512,rng);
            userVars.new_pass2wd.value = randnum.toString(16); // won't be used for anything
            
            var argstr = "page=profileUpdate&sesskey=" + encodeURIComponent(window.sesskey) + "&username=" + userVars.username.value;
            argstr += "&first_name=" + userVars.first_name.value + "&last_name=" + userVars.last_name.value + "&changePassBox=" + userVars.changePassBox.value;
            argstr += "&pass=" + userVars.pass.value + "&new_pass1=" + userVars.new_pass1.value + "&new_pass2=" + userVars.new_pass2.value;
            argstr += "&new_privkey=" + encodeURIComponent(userVars.new_privkey.value);
            
            //userVars.submit();
            PostAjaxRequest(function() {
                //console.log(this);
                var jsonobj = JSON.parse(this);
                //console.log(jsonobj);
                
                // display errors in status message
                var errortext = ""
                for (var n=0; n<jsonobj.errors.length; n++) {
                    
                    errortext += jsonobj.errors[n] + '\n';
                }
                statusMessage(errortext);
                
                
                if (jsonobj.pwupdated == true) {
                    ProcessCookie('save','encpass',encpass);
                    privkey = jsonobj.newprivkey;
                    
                    window.privkey = privkey;
                    //console.log(privkey);
                }
                
                displayTitleBar('titleBarInfo',function() {
                    displaySelfProfile('peopleProfile');
                });
                
                
            },'ajaxHTML.php',argstr);
        
        }
    }


// ---------------------- CHAT NODES -----------------------------------

function closeChats(parentid) {
    // parentdiv = "msg60" ex
    //alert(parentid);
    var iter = document.getElementById(parentid).childNodes;
    
    for (var n=0; n < iter.length; n++)
    {
        // iter[n].style.visibility="hidden";
        iter[n].className = "hidden";
    }

}

function showChat(chat_token,forceShow) { // forceShow disables the toggle
    
    var chatid = 'msg'+chat_token;
    
    try { // reloading with open group, the group won't exist before this is called
        
        var chatDoesNotExist = document.getElementById(chatid).id == undefined;
        //console.log(chatDoesNotExist);
        
        if (!chatDoesNotExist)
        {
            //console.log('not undefined');
            var currchat = document.getElementById(chatid);
            
            if ((currchat.className == "visible") && (!forceShow)) {
                
                currchat.className = "hidden";
                
            } else {
                
                var parentid = document.getElementById(chatid).parentNode;
                //console.log('chat parend id: '+parentid.getAttribute('id'));
                closeChildren(parentid.getAttribute('id'));
                currchat.className = "visible";  
            }
            
            $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').reinitialise();//chat.data.jspAPI.reinitialise();
            
            $('#chatLineHolder' + chat_token).jScrollPane().data('jsp').scrollToBottom(true); //chat.data.jspAPI.scrollToBottom(true);
        }
        
    } catch(err) {
        //console.log('');
    }
}

function addChatToNav(chat_token) { // no longer dependent on username, email
    
    /*if (!document.contains("ulchat")) {
        // add ulchat
        var chatParent = document.getElementById("lichat");
        var chatul = document.createElement("ul");
        chatul.setAttribute('id','ulchat');
        chatParent.appendChild(chatul);
        //chatParent.innerHTML = chatParent.innerHTML + '<ul id="ulchat"></ul>';
    } else {
        // ulchat already exists
        var chatul = document.getElementById("ulchat");
    }*/
    
    //var chatid = 'msg'+chat_token;
    
    var auser = document.createElement("a")
    //auser.setAttribute('href','#');
    //auser.className = "button button-small";
    var linkstr = 'showChat("' + chat_token + '",false);';
    //alert(linkstr);
    auser.setAttribute('href',"#group-groupView-"+chat_token);
    //--auser.setAttribute('onClick',linkstr);
    auser.setAttribute('id','groupView'+chat_token);
    //auser.innerHTML = "Chat:" + username;
    auser.innerHTML = "Group"+chat_token.substr(0,6);
    
    /*
    var spanuser = document.createElement("span");
    spanuser.setAttribute('title',email);
    spanuser.appendChild(auser);
    //*/
    
    var groupNavListItem = document.createElement("li");
    groupNavListItem.setAttribute('id','groupNavLI'+chat_token);
    groupNavListItem.appendChild(auser);
    
    //document.getElementById("chatbar").appendChild(spanuser);
    document.getElementById("groupNavList").appendChild(groupNavListItem);
    //console.log('addChatToNav called for chat: '+chat_token);
    
    /*var liuser = document.createElement("li");
    liuser.appendChild(auser);
    chatul.appendChild(liuser);*/
    
    
}

// ------------ GROUP FUNCTIONS -----------------------------



// ------------ Temporary (Dynamic) -------------------------

function statusMessage(msg) {
    
    var wrap = document.createElement("div");
    wrap.className = "statusMessage";
    wrap.innerHTML = msg;
    
    $('#statusMessage').css("background-color", '000000');
    $('#statusMessage').html('');
    $('#statusMessage').append(wrap);
    
    setTimeout(function() {
        
        $('#statusMessage').html('');
        $('#statusMessage').css("background-color", "");
        
    },30000);
}

// ----------- File Handling --------------------------------

function ttfileSelected(input,preview_id) {
    
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#'+preview_id)
                .attr('src', e.target.result) //.width(150)
                .height(100)
                .attr('class','visible');
        };

        reader.readAsDataURL(input.files[0]);
        
        
    }
}

// ------------ Personal Messaging ------------------------

// Helper function that formats the file sizes
function formatFileSize(bytes) {
    if (typeof bytes !== 'number') {
        return '';
    }

    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }

    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
}

function processPMsg(formdata,callback) {
    
    var pmkey = getRandKey(512);
    
    var user_id = formdata.recipientSel.value;
    //console.log('user_id: '+user_id);
    
    // get recipient info
    PostAjaxRequest(function() {
                        
        var trimmed = $.trim(this);
        var data = jQuery.parseJSON(trimmed);
        
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
            
            callback();
            
          }, function() { alert('A modern web browser such as Google Chrome is needed to access all HTML5 features of this site.')} );
        
        
        
        // TODO: send to email address
        
        
        
        
        
    
    },'ajaxgetmsguserdata.php','user_id='+user_id);
    
}