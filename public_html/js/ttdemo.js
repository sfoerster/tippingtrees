

            function genRSAkey(callback) {
                var rsa = new RSAKey();
                var userVars = document.RSAform;
                
                if (userVars.privkeypass.value != '') {
                
                    $('#loading').css('visibility','visible');
                    
                    /*do
                    {
                        rsa.generate(parseInt(userVars.bits.value),"10001"); // bitlength, base 10 integer and public exponent, string of hex
                    }
                    while (rsa.n.bitLength() != 512 || rsa.n.divide(rsa.d) > 2); // ensure 1) modulus is as long as it's supposed to be and 2) d > 0.3*n */
                    
                    rsa.generateAsync(parseInt(userVars.bits.value),"c0000001",function(){
                        
                        if (rsa.n.bitLength() != parseInt(userVars.bits.value) || rsa.n.divide(rsa.d) > 2) // ensure 1) modulus is as long as it's supposed to be and 2) d > 0.3*n
                        {
                            genRSAkey(callback);
                        }
                        else
                        {
                            userVars.bits.value = parseInt(rsa.n.bitLength());
                            userVars.ndrat.value = parseInt(rsa.n.divide(rsa.d));
                            
                            userVars.pubkey.value = publicRSAKeytoPEM(rsa);
                            userVars.privkey.value = privateRSAKeytoPEM(rsa,userVars.privkeypass.value);
                            
                            $('#loading').css('visibility','hidden');
                            callback();
                        }
                    });
                
                } else {
                    
                    alert('Enter a password for the private key.');
                    
                }
                
            }
            
            function rsaEncrypt() {
                
                var userVars = document.RSAform;
                //var rsaEn = new RSAKey();
                //rsaEn.setPublic(userVars.n.value,userVars.e.value);
                rsaEn = publicPEMtoRSAKey(userVars.pubkey.value);
                
                var cpt = rsaEn.encrypt(userVars.plain.value);
                
                userVars.crypt.value = cpt;
                userVars.plain.value = '';
            }
            
            function rsaDecrypt() {
                
                var userVars = document.RSAform;
                //var rsaDec = new RSAKey();
                //rsaDec.setPrivate(userVars.n.value,userVars.e.value,userVars.d.value);
                rsaDec = privatePEMtoRSAKey(userVars.privkey.value,userVars.privkeypass.value);
                
                var pln = rsaDec.decrypt(userVars.crypt.value);
                
                userVars.plain.value = pln;
                userVars.crypt.value = '';
            }
            
            
            function aesEncrypt() {
                
                var userVars = document.RSAform;
                var aes = new pidCrypt.AES.CTR();
                
                
                userVars.aesCrypt.value = aes.encryptText(userVars.aesPlain.value,userVars.aesPassword.value,{nbits:128});
                userVars.aesPlain.value = '';
                
            }
            
            function aesDecrypt() {
                
                var userVars = document.RSAform;
                var aes = new pidCrypt.AES.CTR();
                
                
                userVars.aesPlain.value = aes.decryptText(userVars.aesCrypt.value,userVars.aesPassword.value,{nbits:128});
                userVars.aesCrypt.value = '';
                
            }
            
            function verify() {
                
                var userVars = document.RSAform;
                //var rsa = new RSAKey();
                //rsa.setPrivate(userVars.n.value,userVars.e.value,userVars.d.value);
                // rsa = privatePEMtoRSAKey(userVars.privkey.value,userVars.privkeypass.value);
                rsa = publicPEMtoRSAKey(userVars.pubkey.value);
                
                var msg = userVars.signedText.value;
                var signature = userVars.signatureBox.value;
                
                var verified = rsa.verifyString(msg,signature);
                
                if (verified==true) {
                    userVars.verifiedText.value = "VERIFIED";
                } else {
                    userVars.verifiedText.value = "NOT VERIFIED";
                }
                
                
            }
            
            
            function sign() {
                
                var userVars = document.RSAform;
                //var rsa = new RSAKey();
                //rsa.setPrivate(userVars.n.value,userVars.e.value,userVars.d.value);
                rsa = privatePEMtoRSAKey(userVars.privkey.value,userVars.privkeypass.value);
                
                var msg = userVars.signedText.value;
                
                userVars.signatureBox.value = rsa.signString(msg,'sha256');
                
                
                // verify signature
                // var verified = sender_rsa.verifyString(msg,sign);
                
                verify();
                
            }