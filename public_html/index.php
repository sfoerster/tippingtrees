<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
require_once('../includes/common/common.php');
?>
	    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Tipping Trees: Simply More than a Web of Trust</title>
		<meta name="description" content="Tipping Trees creates a platform for secure and authenticated encrypted communication, collaboration, and social sharing via client-side strong cryptography. Industry standard and expert-reviewed open-source algorithms including RSA (PKCS#1 v2.1) and the Advanced Encryption Standard (AES-CTR and AES-CBC) and open-source implementations (including Google's CryptoJS, and Stanford's RSAKey and SecureRandom) ensure privacy and security even from Tipping Trees' own server and database. Tipping Trees is committed to a vision of an open and free internet fostering the free exchange of ideas and value to the benefit of all. The Tipping Trees engineers strive for design such that if any administrator made available the contents of the server and database, our users' privacy would not be compromised." />
                <meta name="keywords" content="privacy, cryptography, cryptographic, RSA, AES, JavaScript, PHP, MySQL, security, secure, social network, e-commerce, universal identity, tipping trees, zero knowledge, trust, verify, authenticate, web of trust, digital signature, digital fingerprint" />
                <link rel="shortcut icon" href="img/logo/TTicon.png" />
		<link rel="stylesheet" type="text/css" href="css/RESsimple.css" />
		<link rel="stylesheet" type="text/css" href="css/ttstyle.css" />
		<link rel="stylesheet" type="text/css" href="css/jScrollPane.css" />
		<link rel="stylesheet" type="text/css" href="css/chat.css" />
                
                <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
                
                <link href="https://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:200,700|Open+Sans:400,300,700" rel="stylesheet" />
		<?php // <script src="js/jquery-1.8.3.min.js"></script> ?>
                
		<script src="REScss/5grid/init.js?use=mobile,desktop,1000px&amp;mobileUI=1&amp;mobileUI.theme=none&amp;mobileUI.titleBarHeight=0"></script>
		<?php /*
                <script src="RESjs/jquery.dropotron-1.2.js"></script>
		<script type="text/javascript" src="RESjs/jquery.slidertron-1.2.js"></script>
		*/ ?>
		<script src="RESjs/init.js"></script>
                <noscript>
			<link rel="stylesheet" href="REScss/5grid/core.css" />
			<link rel="stylesheet" href="REScss/5grid/core-desktop.css" />
			<link rel="stylesheet" href="REScss/5grid/core-1200px.css" />
			<link rel="stylesheet" href="REScss/5grid/core-noscript.css" />
			<link rel="stylesheet" href="REScss/style.css" />
			<link rel="stylesheet" href="REScss/style-desktop.css" />
		</noscript>
		<!--[if lte IE 9]><link rel="stylesheet" href="REScss/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="REScss/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="REScss/ie7.css" /><![endif]-->
		
		
		
		    
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
<body class="left-sidebar">
	<div id="statusMessage"></div>
    
    <?php // begin Facebook SDK link ?>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <?php // end Facebook SDK link ?>
    
    
		<!-- Wrapper Starts Here -->
			<div id="wrapper">
				<div id="wrapper-bgtop">
                                    <div align="right">
                                        <?php
                                        include('../includes/scripts/titlebar.php');
                                        ?>
                                    </div>
                                    
					<!-- Header Wrapper -->
						<div id="header-wrapper">
							<div class="5grid-layout">
								<div class="row">
								
									<div class="3u">
										<!-- Header -->
											<header id="header">
											
												<!-- Logo -->
												<h1><a href="#home" class="mobileUI-site-name">Tipping<span>Trees</span></a></h1>
											</header>
									
									</div>
								
									<?php RESsharedHTML::initNav(); ?>
									
								</div>					
							</div>
						</div>
					<!-- Header Wrapper Ends Here -->
		
					<!-- Main Wrapper -->
						<div id="main-wrapper">
	
							<!-- Main Content -->
								<div id="main" class="5grid-layout">
									<div class="row">
									
										<!-- Sidebar -->
											<div id="sidebar" class="3u">
																
												<section>
													<h2 class="title">Secure Online Presence</h2>
													<ul class="style2">
														<li class="first"><a href="#demo-demoAlgs">SSL/SSH/GPG compatible credentials</a></li>
														<li><a href="#demo-demoDB">Zero Knowledge: Encrypt Data & Connections</a></li>
														<li><a href="#about-aboutFirst">Groups: Private Sharing with your Friends and Contacts</a></li>
														<?php //<li><a href="#branch-branchBasic">Two Factor Authentication</a></li> ?>
														<li><a href="#about-aboutFeatures">Web of Trust: Your Online Reputation Scores</a></li>
													</ul>
												</section>
					
                                                                                                <?php RESsharedHTML::echoNews(); ?>
                                                                                                <?php /*
												<section>
													<h2 class="title">lorem aliquam</h2>
													<ul class="style3">
														<li class="first">
															<p><a href="#" class="date">December 1</a></p>
															<p><a href="#">Vivamus ut mauris tempus nibh sodales adipiscing in vel quam. Nam erat et posuere.</a></p>
														</li>
														<li>
															<p><a href="#" class="date">November 30</a></p>
															<p><a href="#">Mauris tempus nibh sodales adipiscing in vel quam. Nam erat et posuere tempus etiam.</a></p>
														</li>
													</ul>
												</section>
												//*/ ?>

												<?php /*
												<section>
													<h2 class="title">feugiat consequat</h2>
													<ul class="style4">
														<li class="first">
															<h3><a href="#">Praesent aliquam lorem dignissim</a></h3>
															<p><span class="date">6 hours ago</span><span class="comments"><a href="#">32 comments</a></span></p>
														</li>
														<li>
															<h3><a href="#">Tempus veroeros sed nulla adipiscing</a></h3>
															<p><span class="date">9 hours ago</span><span class="comments"><a href="#">28 comments</a></span></p>
														</li>
														<li>
															<h3><a href="#">Fermentum dolore varius lorem</a></h3>
															<p><span class="date">Yesterday</span><span class="comments"><a href="#">18 comments</a></span></p>
														</li>
													</ul>
												</section>
												*/ ?>
												
											</div>
										<!-- Sidebar Ends Here -->

										<!-- Content -->
                                                                                <div id="content" class="9u mobileUI-main-content">
											<?php RESsharedHTML::initContainers(); ?>
                                                                                </div>
										<!-- Content Ends Here -->

									</div>
								</div>						
							<!-- Main Content -->

								<!-- Main Content -->
								<div id="main" class="5grid-layout">
									<div class="row">
									
									<?php /*
										<!-- Content -->
											<div id="content" class="4u">
												<article>
													<h2 class="title">For Your Eyes Only</h2>
													<?php //<a href="#" class="image image-left image-style"><img src="images/pic01.jpg" alt=""></a> ?>
													<h3 class="subtitle">Send an Encrypted Message to ANY Email Address</h3>
													<?php //<p>Vivamus ut mauris tempus nibh sodales adipiscing in vel quam. Nam erat posuere laoreet, egestas at lacus. Vivamus ut mauris tempus nibh sodales adipiscing in dolor magna. Vivamus ut mauris tempus nibh sodales.</p> ?>
													<form name="publicfyeo" action="" method="post">
														<p>Recipient Email:</p>
														<input type="text" size="40" maxlength="50" name="destemail" class="rounded" />
														<p>Unencrypted Subject (so recipient will recognize it as something to open)</p>
														<input type="text" size="40" maxlength="100" name="plainsubject" class="rounded" />
														<p>Secret Message:</p>
														<textarea cols="40" rows="10" name="msg" maxlength="10000" class="rounded"></textarea>

													</form>
													<a href="" class="button button-medium">Send</a>
												</article>
											</div>
										<!-- Content Ends Here -->
										*/ ?>

										<!-- Sidebar -->
											<div id="content" class="6u">
											
												<section>
													<h2 class="title">For Your Eyes Only</h2>
													<?php //<a href="#" class="image image-left image-style"><img src="images/pic01.jpg" alt=""></a> ?>
													<h3 class="subtitle">Connect Securely, Even Before You Join</h3>
													<p>Browse for friends, family, or associates in the Web of Trust. View their Web Reputation Scores, create a For Your Eyes Only message, or join and connect with them! As a member you can send invitations to others to join your network!</p>
													<?php // <a href="" class="button button-medium">continue reading</a> ?>
													<form name="emailLookupForm">
            											<p>Search by Email: <br /><input type="text" maxlength="100" name="emailLookup" value="service@tippingtrees.com" size="50" class="rounded" /><br /><input type="button" class="button button-medium" value="Look up" onclick="ttLookupEmail(this.form);" /></p>
            										</form>
													<?php /*<ul class="style4">

														<li class="first">
															<h3><a href="#">Praesent aliquam lorem dignissim</a></h3>
															<p><span class="date">6 hours ago</span><span class="comments"><a href="#">32 comments</a></span></p>
														</li>
														<li>
															<h3><a href="#">Tempus veroeros sed nulla adipiscing</a></h3>
															<p><span class="date">9 hours ago</span><span class="comments"><a href="#">28 comments</a></span></p>
														</li>
														<li>
															<h3><a href="#">Fermentum dolore varius lorem</a></h3>
															<p><span class="date">Yesterday</span><span class="comments"><a href="#">18 comments</a></span></p>
														</li>
													</ul> */ ?>
												</section>
												
											</div>
										<!-- Sidebar Ends Here -->

										<!-- Content -->
											<div id="content" class="6u">
												<article>
													<h2 class="title">Connect to the Web of Trust</h2>
													<?php //<a href="#" class="image image-left image-style"><img src="images/pic01.jpg" alt=""></a> ?>
													<h3 class="subtitle">Start Sending Encrypted Messages & Creating ZeroKnowledge Groups</h3>
													<p>Industry standard RSA cryptographic credentials generated in your browser are linked to your email address. Anyone may connect with you securely, using your universal public cryptographic key, which is linked to your email address. As others connect with you, their digital signatures verify that your public key belongs to your email address. Your communication is encrypted with industry standard cryptography, and anything you choose to sign online can be verified with these same credentials. Universal privacy and authentication are now yours!</p>
													<?php // <a href="" class="button button-medium">continue reading</a> ?>
													<?php registerClass::inviteForm(); ?>
												</article>
											</div>
										<!-- Content Ends Here -->
										
										
										

									</div>
								</div>						
							<!-- Main Content -->

						</div>
					<!-- Main Wrapper Ends Here -->
				
				</div>
			</div>
		<!-- Wrapper Ends Here -->

		<!-- Footer Wrapper -->
			<div id="footer-wrapper">
				<footer id="footer" class="5grid-layout">
					<div class="row">
						<div class="4u">
							<section>
								<h2 class="title">Vision</h2>
								<p>Verify, then trust. We aim for an encrypted communication system that does not rely on blind trust, but instead on originality, openness, verification, and a history of reliability.</p>
								<p>Value for value in a voluntary exchange is the fundamental principle of prosperity, fair to both parties. Tipping Trees is the vehicle to eliminate any distance between producer and consumer, optimized for a digital world.</p>
								<a href="#about-aboutDetails" class="button button-style2" target="_blank">Learn More</a> 
							</section>
						</div>
						<div class="4u">
							<section>
								<h2 class="title">About</h2>
								<ul class="style2">
									<li class="first"><a href="#about-aboutFirst" target="_blank">Groups: A Secure Network of Intranets</a></li>
									<li><a href="#demo-demoDB" target="_blank">Zero Knowledge</a></li>
									<li><a href="#about-aboutFeatures" target="_blank">Secure, Verified Identities and Reputation Scores</a></li>
									<?php /*<li><a href="#">Odio inceptos emper vestibulum</a></li>
									<li><a href="#">Amet feugiat lorem dolore lectus</a></li>
									*/ ?>
								</ul>
							</section>
						</div>
						<div class="4u">
							<section>
								<?php /* <h2 class="title">Events</h2>
								<ul class="style3">
									<li class="first">
										<p><a href="#" class="date">December 1</a></p>
										<p><a href="#">Vivamus ut mauris tempus nibh sodales adipiscing in vel quam. Nam erat et posuere.</a></p>
									</li>
									<li>
										<p><a href="#" class="date">November 30</a></p>
										<p><a href="#">Mauris tempus nibh sodales adipiscing in vel quam. Nam erat et posuere tempus etiam.</a></p>
									</li>
								</ul>
								*/ ?>
								<h2 class="title">Contact Us</h2>
								<p>To discuss your specific business or personal needs:</p>
								<ul class="style4">
									<li>Setup a Secure Collaboration Environment, protected by Tipping Trees' Zero Knowledge Design</li>
									<li>Integrate Tipping Trees' Industry Standard Cryptography user credentials and libraries into your own site</li>
									<li>Verify your users' Signatures or Reputation</li>
									<li>Ask us how to get involved!</li>
								</ul>
								<a href="#about-aboutContact" class="button button-style2" target="_blank">Contact</a>
							</section>
						</div>
						<?php /*<div class="3u">
							<section>
								<h2 class="title">Register For Free</h2>
								<?php registerClass::inviteForm(); ?>
								
								<?php /* 
								<p>Mauris tempus nibh sodales adipiscing in vel quam. Nam erat et posuere.</p>
								<form method="post" action="#" id="newsletter">
									<input type="text" class="text" name="search" />
									<input type="submit" class="button" value="Sign Up!" />
								</form>
								/ ?>
							</section>
						</div>
						*/ ?>
					</div>
					<div class="row">
					
					<!-- Copyright -->
					<?php // LinkedIn ?>
                    <script src="//platform.linkedin.com/in.js" type="text/javascript">
                        lang: en_US
                    </script>
                    <script type="IN/FollowCompany" data-id="3682335" data-counter="none"></script>
                    <div class="clear"></div>
                    <?php // Facebook, see SDK link on index.php ?>
                    <div class="fb-like" data-href="https://tippingtrees.com/" data-width="50" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
                    <div class="clear"></div>
                    <?php // Twitter ?>
                    <a href="https://twitter.com/TippingTrees" class="twitter-follow-button" data-show-count="false">Follow @TippingTrees</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                    <div class="clear"></div>
                    <?php // Google+ ?>
                    <a href="https://plus.google.com/115972651404107473645" rel="publisher">Google+</a>

                    <br /><br />
                    <center><span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=xZHg1Or9j1GOfp2XvHLs1pQAQGv4I19C59xVUmfzs4pR3WepPESr7ci"></script></span></center>
					

					<div id="copyright"> &copy; Tipping Trees. All rights reserved. Patents Pending. </div>
					</div>
				</footer>
			</div>
		<!-- Footer Wrapper Ends Here -->

        <?php sharedHTML::echoLocalJS(); ?>
        
	</body>
</html>