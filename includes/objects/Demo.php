
<?php

require('../includes/common/common.php');

class Demo
{
    static function Process()
    {
        ?>
        <img src="img/info/EncryptionSchemeBoxDiagram.png" width="740px" />
        <p>On Tipping Trees, RSA encryption keys is used to exchange AES encryption keys which are in 
turn used to encrypt and decrypt messages. Here is a simplified overview of the procedure: </p>
        <ol>
            <li>Sender and recipient both generate (or already have) asymmetric RSA public and private 
                keys. The public keys are shared openly but the private keys are kept secret.</li>
            <li>The sender generates a symmetric AES encryption key and uses it to encrypt their 
                message.</li>
            <li>The AES key is padded using OAEP and then encrypted with the recipient's RSA public key. </li>
            <li>The sender uses their own RSA private key to sign their message. </li>
            <li>The sender sends the AES encrypted message, RSA encrypted AES key, and digital 
                signature to the recipient.</li>
            <li>The recipient decrypts the AES key using their own RSA private key and removes the OAEP 
                padding. </li>
            <li>The recipient decrypts the message using the recovered AES key. </li>
            <li>The recipient uses the sender's RSA public key to verify the signature and confirm the 
                sender's identity.</li>
        </ol>
 
            <p>The message is transferred securely and the sender's identity is verified. Success!</p>
        <?php
    }
    
    static function demoDB()
    {
        ?>
        <p>On Tipping Trees, everything you do is not only encrypted, it is also compartmentalized in such a 
        way that even meta data is unrecoverable. The data held on Tipping Trees' server does not show 
        what groups you are a member of, who you chat with, or even what time you post to a 
        group. On Tipping Trees, privacy is the expectation, not the exception. 
        </p>
        <img src="img/info/ZeroKnowledge.png" width="740px" />
        
        <h1 style="text-align:center">Live Database Data</h1>
        <h2 style="text-align:center">This is real data underlying live groups</h2>
        
        <?php
    }

    static function echoAlgDemo()
    {
        ?>    
        
               
            <form name="RSAform">
                <p>On this page you can explore the cryptographic methods used on Tipping Trees.</p>
                <h2 class="title">Public Key Cryptography (RSA)</h2>
                <p></p><h3 class="subtitle">OpenSSL Compatible Keys:</h3>
                <p></p>
                <table>
                    <tr>
                        <td>
                            Choose key bit length:
                        </td>
                        <td>
                            <input type="text" class="rounded" name="bits" value="512" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Choose a Private Key Password<br />(default="abc123"):
                        </td>
                        <td>
                            <input type="password" class="rounded" name="privkeypass" value="abc123" />
                        </td>
                    </tr>
                </table>
                
                <p></p><input type="button" class="button" name="generateBtn" value="Generate RSA Key" onClick="genRSAkey(function() {});" />
                
                <!--<table cellpadding="20"><tr><td>Public Key:<br /><textarea name="pubkey" rows="12" cols="70"></textarea></td>
                <td>Private Key:<br /><textarea name="privkey" rows="12" cols="70"></textarea></td></tr></table>-->
                
                <p>Every user owns an RSA public/private key pair. The keys are created on your computer. Only the public key and encrypted private key are ever sent to or stored on the Tipping Trees server.
                The decrypted private key is only ever accessible to you and never leaves your local machine. 
                Even we (Tipping Trees) can't access it.</p>
                <p></p>Generated RSA Public Key:<br /><textarea name="pubkey" class="rounded" rows="5" cols="70"></textarea>
                <p></p>Generated RSA Private Key:<br /><textarea name="privkey" class="rounded" rows="14" cols="70"></textarea>
                
                <!--<p></p><table cellpadding="20"><tr><td>Public Exponent:<br /><textarea name="e" rows="5" cols="45"></textarea></td>
                <td>Private Exponent:<br /><textarea name="d" rows="5" cols="45"></textarea></td>
                <td>Modulus:<br /><textarea name="n" rows="5" cols="45"></textarea></td></tr></table>-->
                <!--<p></p>n/d ratio:<br />--><input type="hidden" name="ndrat" />
                <hr />
                <p></p>
                <h3 class="subtitle">Public Key Encryption/Decryption:</h3>
                <p>Every message and action in Tipping Trees is encrypted with a random key. This key is encrypted with your RSA public key before it is sent to or stored on the Tipping Trees server.
                Meaning only you can decrypt it (using your private key).</p>
                <p></p>Input a plaintext message to encrypt with the RSA public key:<br /><textarea name="plain" class="rounded" rows="5" cols="70">RSA PKCS#1 v2.1 published as RFC 3447.</textarea>
                <p></p>Encrypted ciphertext, decrypts only with the RSA private key:<br /><textarea name="crypt" class="rounded" rows="5" cols="70"></textarea>
                <p></p><input type="button" class="button" name="rsaEncryptBtn" value="Encrypt with Public Key" onClick="rsaEncrypt();" /> &nbsp;
                       <input type="button" class="button" name="rsaDecryptBtn" value="Decrypt with Private Key" onClick="rsaDecrypt();" />
                <hr />
                <p></p><h3 class="subtitle">Message Signing:</h3>
                <p>Every message and action in Tipping Trees is signed with your RSA private key. Even when you are offline, your RSA public key is available to verify your authorship of messages to your friends and associates.</p>
                <p></p>Input a message to sign:<br /><textarea name="signedText" class="rounded" rows="5" cols="70">RSA Optimal Asymmetric Encryption Padding is defined in PKCS #1 v2.0.</textarea>
                <p></p>Resulting signature:<br /><textarea name="signatureBox" class="rounded" rows="5" cols="70" onChange="verify();"></textarea><br />Signed using SHA-256 and the RSA key above.
                <p></p><input type="button" class="button" name="signBtn" value="Sign with Private Key" onClick="sign();" />
                <p></p><input type="text" class="rounded" name="verifiedText" value="" /> &nbsp;
                           <input type="button" class="button" name="verifyBtn" value="Verify Signature with Public Key" onClick="verify();" />
                <hr />
                <h2 class="title">Advanced Encryption Standard (AES-CTR-128)</h2>
                <p>Every message and action on Tipping Trees is encrypted with the Advanced Encryption Standard in Counter mode using an effective 128-bit key size.</p>
                <p></p>Choose an AES password (default=abc123):<br /><input type="text" class="rounded" name="aesPassword" value="abc123" />
                <p></p>Input plaintext to encrypt with AES key:<br /><textarea name="aesPlain" class="rounded" rows="5" cols="70">The Advanced Encryption Standard employs the Rijndael cipher algorithm. CTR is counter mode. CTR mode was standardized in 2001 by NIST in SP 800-38A.</textarea>
                <p></p>Encrypted ciphertext, decrypts with the AES key:<br /><textarea name="aesCrypt" class="rounded" rows="5" cols="70"></textarea>
                <p></p><input type="button" class="button" name="aesEncryptBtn" value="AES Encrypt" onClick="aesEncrypt();" /> &nbsp; <input type="button" class="button" name="aesDecryptionBtn" value="AES Decrypt" onClick="aesDecrypt();" />
                
            </form>
        
        

        
        
        
        <?php
    }
    
    static function echoFileDemo()
    {
        ?>
        
            <script type="text/javascript">
    
                var file	   = null;
                var pkbvisible = false;
                var worker     = null;
                
                
                function Save(data, op){
                    window.URL = window.webkitURL || window.URL;
                    var bb = new Blob([data], { "type" : "application\/octet-stream" });
                    var a = document.createElement('a');
                      a.download = file.name;
                      a.href = window.URL.createObjectURL(bb);
                      a.className = "button";
                      a.textContent = op;
                      a.dataset.downloadurl = ['application/octet-stream', a.download, a.href].join(':');
                      if(document.getElementById('download').firstChild)
                        document.getElementById('download').replaceChild(a,document.getElementById('download').firstChild);
                      else
                        document.getElementById('download').appendChild(a);
                }
                    
                    
                function dragenter(e) {
                  e.stopPropagation();
                  e.preventDefault();
                }
            
                function dragover(e) {
                  e.stopPropagation();
                  e.preventDefault();
                }
                function drop(e) {
                  e.stopPropagation();
                  e.preventDefault();
            
                  var dt = e.dataTransfer;
                  var files = dt.files;
            
                  handleFiles(files);
                }
                function handleFiles(f)
                {
                    file = f[0];
                    document.getElementById("fileproc").innerHTML="Selected file: "+file.name;
                }
                
                function file_encrypt()
                {
                    if(document.getElementById("aespasswd").value.length==0)
                    {
                            alert("Please set a password first!");
                            return;
                    }
            
                    if(file==null)
                    {
                        alert("No file selected!");
                        return;
                    }
                    var reader = new FileReader();
                    
                    reader.onload=function(e){
                        var filedata=e.target.result;
            
                        worker.postMessage({cmd:1,data:filedata,pass:document.getElementById("aespasswd").value,len:32});
                        //delete filedata;
                    };
                    
                    document.getElementById('download').innerHTML="Encrypting ...";
                    reader.readAsArrayBuffer(file);
                }
                
                
                function file_decrypt()
                {
                    if(document.getElementById("aespasswd").value.length==0)
                    {
                            alert("Please set a password first!");
                            return;
                    }
                            
                    if(file==null)
                    {
                        alert("No file selected!");
                        return;
                    }
                    
                    var reader = new FileReader();
                    reader.onload=function(e){
                        var filedata=e.target.result;
                        
                        worker.postMessage({cmd:2,data:filedata,pass:document.getElementById("aespasswd").value,len:32});
                        //delete filedata;
                    };
                    
                    document.getElementById('download').innerHTML="Decrypting ...";
                    reader.readAsArrayBuffer(file);
                }
                
                
                function strencrypt()
                {
                    if(document.getElementById("aespasswd").value.length==0)
                    {
                            alert("Please set a password first!");
                            return;
                    }
                    
                    var str = document.getElementById("datastr").value;
                    document.getElementById("encstr").value = encode(str, document.getElementById("aespasswd").value);
                }
                
                
                function strdecrypt()
                {
                    if(document.getElementById("aespasswd").value.length==0)
                    {
                        alert("Please set a password first!");
                        return;
                    }
                    
                    var str=document.getElementById("datastr").value;
                    
                    try
                    {
                        var result = decode(str, document.getElementById("aespasswd").value);
                    }
                    catch(e)
                    {
                        result = e;
                    }
                    
                    document.getElementById("encstr").value = result;
                }
                    
                    
                function keyboard()
                {
                    PKb.input = document.getElementById("aespasswd");
                    
                    if(pkbvisible)
                        PKb.remove();
                    else
                        PKb.create(document.getElementById("pkb_container"));
                        
                    pkbvisible=!pkbvisible;
                }
                
                function textsel()
                {
                    document.getElementById("filenc").style.display="none";
                    document.getElementById("textenc").style.display="block";
                }
                
                function filesel()
                {
                    document.getElementById("filenc").style.display="block";
                    document.getElementById("textenc").style.display="none";
                }
                    
                function file_worker_load()
                {
                    if (window.File && window.FileReader && window.FileList && window.Blob) {
                    }
                    else {
                      alert("The File API is needed for this application! Your browser is not supported!");
                    }
                    
                    var dropbox;
                    dropbox = document.getElementById("filedrop");  
                    dropbox.addEventListener("dragenter", dragenter, false);  
                    dropbox.addEventListener("dragover", dragover, false);  
                    dropbox.addEventListener("drop", drop, false);  
                    
                    worker = new Worker('js/jsaes/worker.js');
                    
                    worker.onmessage = function(e) {
                        if(e.data.cmd == 0)
                        {
                            Save(e.data.data, e.data.msg);
                        }
                        else if(e.data.cmd == 1)
                        {
                            document.getElementById('download').innerHTML = e.data.prg;
                        }
                    };
                    
                    filesel();
                }
            
            </script> 
        
            <div class="main">
    
                <table align="center" border="0">
                    <tr>
                        <td ><!--<img src="img/key.png" alt="key" />--></td>
                        <td align="center"><h1 style="margin:0">Crypter</h1><b>Encrypt/Decrypt any file (documents, images, archives, whatever)
                        with an AES-256 encryption key</b><br></td>
                        <td ><!--<img src="img/key.png" alt="key" />--></td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" align="center">Choose/Input Password for Encryption/Decryption: <input type="password" id="aespasswd" class="rounded" size="30" /></td>
                        <td><img src="img/kb.png" alt="keyboard" onclick="keyboard()"/></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center"><div id="pkb_container"></div></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center"><hr></td>
                    </tr>
                    <!--<tr>
                        <td align="center"><img src="img/text.png" alt="text" onclick="textsel()"/></td>
                        <td align="center"><b>Select TEXT or FILE encryption</b></td>
                        <td><img src="img/file.png" alt="file" onclick="filesel()"/> </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center"><hr></td>
                    </tr>-->
                    <tr>
                        <td colspan="3" align="center">
                            <div id="filenc" style="display:none">
                                <div id="filedrop"><b>Drop a file here or </b>
                                    <input type="file" name="filedata" onchange="handleFiles(this.files)"/>
                                </div><br>
                                <p id="fileproc"></p>
                                <input type="button" class="button" value ="Encrypt" onclick="file_encrypt()"/>
                                <input type="button" class="button" value ="Decrypt" onclick="file_decrypt()"/>
                                <h4 id="download"></h4>
                                <h5>Files are not uploaded, they are encrypted in the browser.</h5>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">
                            <div id="textenc" style="display:none">
                                <p align="center">Text to encrypt/decrypt:</p>
                                <textarea id="datastr" rows="8" cols="50"></textarea>
                                <p align="center">Result:</p>
                                <textarea id="encstr" rows="8" cols="50"></textarea><br><br>
                                <input type="button" class="button" value ="Encrypt" onclick="strencrypt()"/>
                                <input type="button" class="button" value ="Decrypt" onclick="strdecrypt()"/>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <?php //<h5 align="center"><a href="http://lazarsoft.info" target="_blank">by Lazar Laszlo</a></h5> ?>
            </div>
        
        <?php
    }


}

?>
