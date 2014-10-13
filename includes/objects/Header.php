
<?php

require('../includes/common/common.php');

class Header
{

    static function echoHeader()
    {
        ?>	    
	    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Tipping Trees: Simply More than a Web of Trust</title>
		<meta name="description" content="Tipping Trees creates a platform for secure and authenticated encrypted communication, collaboration, and social sharing via client-side strong cryptography. Industry standard and expert-reviewed open-source algorithms including RSA (PKCS#1 v2.1) and the Advanced Encryption Standard (AES-CTR and AES-CBC) and open-source implementations (including Google's CryptoJS, and Stanford's RSAKey and SecureRandom) ensure privacy and security even from Tipping Trees' own server and database. Tipping Trees is committed to a vision of an open and free internet fostering the free exchange of ideas and value to the benefit of all. The Tipping Trees engineers strive for design such that if any administrator made available the contents of the server and database, our users' privacy would not be compromised." />
                <meta name="keywords" content="privacy, cryptography, RSA, AES, JavaScript, PHP, MySQL, security, social network, e-commerce, universal identity, tipping, trees" />
                <link rel="shortcut icon" href="img/logo/TTicon.png" />
		<link rel="stylesheet" type="text/css" href="css/simple.css" />
		<link rel="stylesheet" type="text/css" href="css/ttstyle.css" />
		<link rel="stylesheet" type="text/css" href="css/jScrollPane.css" />
		<link rel="stylesheet" type="text/css" href="css/chat.css" />
		
		
		<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
		    
		<script type="text/javascript" src="js/ttlib.js?1000"></script>
		<script type="text/javascript" src="js/script.js?1000"></script>
		<script type="text/javascript" src="js/dropscript.js"></script>
		
		
		<form id="paypaldonate" method="post" action="https://www.paypal.com/cgi-bin/webscr" target="_blank">
                   <input type="hidden" value="_donations" name="cmd">
                   <input type="hidden" value="tippingtrees@gmail.com" name="business">
                   <input type="hidden" value="US" name="lc">
                   <input type="hidden" value="Tipping Trees, LLC" name="item_name">
                   <input type="hidden" value="0" name="no_note">
                   <input type="hidden" value="USD" name="currency_code">
                   <input type="hidden" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest" name="bn">
               </form>
		
		<?php
                
                $rnd = new Random(false);
                $rndpool = array();
                for($n=0; $n<256; $n++) {
                    $rndpool[] = $rnd->int(255);
                }

                ?>
		
		<script type="text/javascript">
		    window.rndpool = <?php echo json_encode($rndpool); ?>; // intended as a backup only
		</script>
	    
	    
	    </head>
        
        <?php
    }


}


?>
