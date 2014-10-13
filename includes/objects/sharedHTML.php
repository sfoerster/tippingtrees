
<?php

require('../includes/common/common.php');

class sharedHTML
{
    
    static function initContainers()
    {
        ?>
        
            <div id="pageContent">
        
                <div id="home" class="hidden">
                    
                    <div id="homeNav"></div>
                    
                    <div class="clear"></div>
                    
                    <div id="homeContent">
                        <div id="homeDefault">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    ttHomeContent('homeDefault');
                                });
                            </script>
                        </div>
                    </div>
                </div>
                
                <div id="about" class="hidden">
                    
                    <div id="aboutNav">
                        <ul class="ttnav">
                            <?php //<li><a href="#about-aboutFAQ">FAQ</a></li> ?>
                            <li><a href="#about-aboutFirst" id="aboutFirstNavLink">First</a></li>
                            <li><a href="#about-aboutDetails" id="aboutDetailsNavLink">Details</a></li>
                            <li><a href="#about-aboutFeatures" id="aboutFeaturesNavLink">Features</a></li>
                            <li><a href="#about-aboutContact" id="aboutContactNavLink">Contact</a></li>
                            <?php //<li><a href="#about-aboutRoadmap">Roadmap</a></li> ?>
                            <?php /*<li><a href="#about-aboutVision">Vision</a></li>
                            <li><a href="#about-aboutWork">Work</a></li>
                            <li><a href="#about-aboutInvest">Invest</a></li> */ ?>
                            <li><a href="#about-aboutCredit" id="aboutCreditNavLink">Credits</a></li>
                        </ul>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div id="aboutContent">
                        <?php /*<div id="aboutFAQ">
                            <?php aboutClass::getFAQ(); ?>
                        </div> */ ?>
                        <div id="aboutFirst">
                            <?php aboutClass::getFirst(); ?>
                        </div>
                        <div id="aboutDetails">
                            <?php aboutClass::getDetails(); ?>
                        </div>
                        <div id="aboutFeatures">
                            <?php aboutClass::getFeatures(); ?>
                        </div>
                        <div id="aboutContact">
                            <?php contactClass::getTable(); ?>
                        </div>
                        <?php /*<div id="aboutVision">
                            <?php aboutClass::getVision(); ?>
                        </div>
                        <div id="aboutWork">
                            <?php aboutClass::getWork(); ?>
                        </div>
                        <div id="aboutInvest">
                            <?php aboutClass::getInvest(); ?>
                        </div> */ ?>
                        <div id="aboutCredit">
                            <?php aboutClass::getCredit(); ?>
                        </div>
                    </div>
                </div>
                
                <div id="branch" class="hidden">
                    
                    <div id="branchNav">
                        <ul class="ttnav" id="branchNavList">
                            <li><a href="#branch-branchRoadmap" id="branchRoadmapNavLink">Roadmap</a></li>
                            <li><a href="#branch-branchBasic" id="branchBasicNavLink">Basic</a></li>
                            <li><a href="#branch-branchContribute" id="branchContributeNavLink">Contribute</a></li>
                            <?php // <li><a href="#group-groupView">View</a></li> ?>
                        </ul>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div id="branchContent">
                        <div id="branchRoadmap">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    ttRoadmap('branchRoadmap');
                                });
                            </script>
                        </div>
                        <div id="branchBasic">
                            <?php Branch::getBasic(); ?>
                        </div>
                        <div id="branchContribute">
                            <?php Branch::getContribution(); ?>
                        </div>
                    </div>
                </div>
                
                <div id="demo" class="hidden">
                    
                    <div id="demoNav">
                        <ul class="ttnav">
                           <li><a href="#demo-demoAlgs" id="demoAlgsNavLink">Algorithms</a></li>
                           <li><a href="#demo-demoFiles" id="demoFilesNavLink">Files</a></li>
                           <li><a href="#demo-demoDB" id="demoDBNavLink">Database</a></li>
                           <li><a href="#demo-demoProcess" id="demoProcessNavLink">Process</a></li>
                           <?php //<li><a href="#demo-demoMessages">Messages</a></li> ?>
                        </ul>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div id="demoContent">
                        <div id="demoAlgs">
                            <?php Demo::echoAlgDemo(); ?>
                        </div>
                        <div id="demoFiles">
                            <?php Demo::echoFileDemo(); ?>
                        </div>
                        <div id="demoDB">
                            <?php Demo::demoDB(); ?>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    ttdbPublic('demoDB');
                                });
                            </script>
                        </div>
                        <div id="demoProcess">
                            <?php Demo::Process(); ?>
                        </div>
                        <?php /*<div id="demoMessages">
                            
                        </div> */ ?>
                    </div>
                </div>
                
                <?php if (!commonPHP::isloggedin()) { ?>
                
                <div id="register" class="hidden">
                    
                    <div id="registerNav"></div>
                    
                    <div class="clear"></div>
                    
                    <div id="registerContent">
                        <div id="registerForm">
                            <?php registerClass::getForm(); ?>
                        </div>
                        <div id="registerFromInvitation">
                            
                        </div>
                        <div id="deleteAccount">
                            <?php peopleClass::resetForm(); ?>
                        </div>
                        <div id="resetAccount">
                            
                        </div>
                    </div>
                </div>
                
                <?php } ?>
                
                <?php if (commonPHP::isloggedin()) {  ?>
                
                <div id="notification" class="hidden">
                    
                    <div id="notificationNav">
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div id="notificationContent">
                        <div id="notificationGeneral">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    displayNotifications('notificationGeneral');
                                });
                            </script>
                        </div>
                    </div>
                    
                </div>
                
                <?php  } ?>
                
                <div id="people" class="hidden">
                    
                    <?php if (commonPHP::isloggedin()) { 
                    
                    $s = $_SESSION['user'];
                    $u = unserialize($s);
                    
                    ?>
                    
                    <div id="peopleNav">
                        <ul class="ttnav">
                            <li><a href="#people-peopleAccount" id="peopleAccountNavLink">Account</a></li>
                            <li><a href="#people-peoplePublic-<?php echo urlencode(json_encode(array('email' => $u->email))); ?>" id="peoplePublicNavLink">Profile</a></li>
                            <li><a href="#people-peopleContacts" id="peopleContactsNavLink">Contacts</a></li>
                        </ul>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <?php } ?>
                    
                    <div id="peopleContent">
                        
                        <?php if (commonPHP::isloggedin()) { ?>
                        
                        <div id="peopleAccount">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    displaySelfProfile('peopleAccount');
                                });
                            </script>
                        </div>

                        <div id="peopleContacts">
                            <?php peopleClass::displaySearch(); ?>
                            <div class="clear"></div>
                            <div id="peopleVouches">
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        displayVouches('peopleVouches');
                                    });
                                </script>
                            </div>
                        </div>
                        <div id="peopleInvite">
                            <?php peopleClass::inviteForm(); ?>
                        </div>
                        
                        <?php } ?>
                        
                        <div id="peoplePublic">
                            
                        </div>
                    </div>
                    
                </div>
                
                
                
                <?php if (commonPHP::isloggedin()) { ?>
                
                <div id="group" class="hidden">
                    
                    <div id="groupNav">
                        <ul class="ttnav" id="groupNavList">
                            <li><a href="#group-groupNew" id="groupNewNavLink">New</a></li>
                            <?php // <li><a href="#group-groupView">View</a></li> ?>
                        </ul>
                    </div>
                    
                    <div class="clear"></div>
                    
                    <div id="groupContent">
                        <div id="groupNew">
                            <?php groupClass::getCreateForm(); ?>
                        </div>
                        <div id="groupView">
                            
                        </div>
                    </div>
                </div>
                
                <?php } ?>
                
                <div id="pmessage" class="hidden">
                    
                    <?php if (commonPHP::isloggedin()) { ?>
                    
                    <div id="pmessageNav">
                        <ul class="ttnav">
                           <li><a href="#pmessage-pmessageCompose" id="pmessageComposeNavLink">Compose</a></li> 
                           <li><a href="#pmessage-pmessageRead" id="pmessageReadNavLink">Read</a></li>
                           <li><a href="#pmessage-pmessageSent" id="pmessageSentNavLink">Sent</a></li>
                        </ul>
                    </div>
                    
                    
                    <div class="clear"></div>
                    
                    <?php } ?>
                    
                    <div id="pmessageContent">
                        
                        <?php if (commonPHP::isloggedin()) { ?>
                        
                        <div id="pmessageCompose">
                            <?php pmessage::getMsgForm(); ?>
                        </div>
                        <div id="pmessageRead">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    displayPMsgRead('pmessageRead');
                                });
                            </script>
                        </div>
                        <div id="pmessageSent">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    displayPMsgSent('pmessageSent');
                                });
                            </script>
                        </div>
                        
                        <?php } ?>
                        
                        <div id="pmessageView">
                            
                        </div>
                    </div>
                </div>
                
                
                

            
            </div>
        
        <?php
    }
    
    static function initNav()
    {
        ?>
        
            <ul id="navigation" class="ttnav">
                <li><a href="#about" id="aboutNavLink">About</a></li>
                <li><a href="#branch" id="branchNavLink">Branch</a></li>
                <li><a href="#demo" id="demoNavLink">Demo</a></li>
                
                <?php if (!commonPHP::isloggedin()) { ?>
                <li><a href="#register-registerForm" id="registerFormNavLink">Register</a></li>
                <?php } ?>
                
                <?php if (commonPHP::isloggedin()) { ?>
                <li><a href="#notification" id="notificationNavLink">Activity</a></li> 
                <li><a href="#people" id="peopleNavLink">People</a></li>
                <li><a href="#group" id="groupNavLink">Groups</a></li>
                <li><a href="#pmessage" id="pmessageNavLink">Messages</a></li>
                <?php } ?>
                
            </ul>
            
            <img id="loading" src="img/ajax_load.gif" alt="loading" />
            
        <?php
    }
    
        static function echoLocalJS()
    {
        ?>
                <?php //<script src="js/chatscript.js"></script> ?>
		<script type="text/javascript" src="js/WDC.js"></script> <?php // needed for rng.js ?>
                
                <script src="js/jquery.dropotron-1.2.js"></script>
                <script type="text/javascript" src="js/jquery.slidertron-1.2.js"></script>
		<script type="text/javascript" src="js/jquery.form.min.js"></script>
		<script src="js/jquery.mousewheel.js"></script>
		<script src="js/jScrollPane.min.js"></script>
                
                <script src="js/jquery.knob.js"></script>
                
                <script src="js/jquery.ui.widget.js"></script>
		<script src="js/jquery.iframe-transport.js"></script>
		<script src="js/jquery.fileupload.js"></script>
        
                <script type="text/javascript" src="js/orig/jsbn.js"></script>
		<script type="text/javascript" src="js/orig/jsbn2.js"></script>
		<script type="text/javascript" src="js/orig/prng4.js"></script>
		<script type="text/javascript" src="js/orig/rng.js"></script>
		<script type="text/javascript" src="js/orig/rsa.js"></script>
		<script type="text/javascript" src="js/orig/rsa2.js"></script>
		<script type="text/javascript" src="js/orig/rsasync.js"></script>
		<script type="text/javascript" src="js/orig/base64.js"></script>
		<script type="text/javascript" src="js/orig/ec.js"></script>
		<script type="text/javascript" src="js/orig/sec.js"></script>
		
		<script type="text/javascript" src="js/pidCrypt/pidcrypt_util.js"></script>
		<script type="text/javascript" src="js/pidCrypt/pidcrypt.js"></script>
		<script type="text/javascript" src="js/pidCrypt/md5.js"></script><!--needed for key and iv generation-->
		<script type="text/javascript" src="js/pidCrypt/aes_core.js"></script><!--needed block en-/decryption-->
		<script type="text/javascript" src="js/pidCrypt/aes_cbc.js"></script>
		<script type="text/javascript" src="js/pidCrypt/aes_ctr.js"></script>
		<script type="text/javascript" src="js/pidCrypt/sha512.js"></script>
		
		<script type="text/javascript" src="js/kjur/core.js"></script>
		<script type="text/javascript" src="js/kjur/rsasign-1.2.min.js"></script>
		<script type="text/javascript" src="js/kjur/crypto-1.1.min.js"></script>
		<script type="text/javascript" src="js/kjur/sha256.js"></script>
		
		<script type="text/javascript" src="js/yahoo/yahoo-min.js"></script>
		
		<script type="text/javascript" src="js/google/core-min.js"></script>
		<script type="text/javascript" src="js/google/cipher-core-min.js"></script> <?php // commented previously ?>
		<script type="text/javascript" src="js/google/enc-base64-min.js"></script>
		<script type="text/javascript" src="js/google/md5-min.js"></script>
		<script type="text/javascript" src="js/google/sha1-min.js"></script>
		<?php // <script type="text/javascript" src="js/google/tripledes-min.js"></script> ?>
		<script type="text/javascript" src="js/google/aes.js"></script>
                
                
		
		<script type="text/javascript" src="js/kjur/base64x-1.1.js"></script>
		<script type="text/javascript" src="js/kjur/asn1hex-1.1.min.js"></script>
		<script type="text/javascript" src="js/kjur/rsapem-1.1.min.js"></script>
		<script type="text/javascript" src="js/kjur/x509-1.1.min.js"></script>
		<script type="text/javascript" src="js/kjur/pkcs5pkey-1.0.min.js"></script>
		<script type="text/javascript" src="js/kjur/asn1-1.0.min.js"></script>
		<script type="text/javascript" src="js/kjur/asn1x509-1.0.min.js"></script>
		
		<script type="text/javascript" src="js/kjur/ec-patch.js"></script>
		<script type="text/javascript" src="js/kjur/ecdsa-modified-1.0.min.js"></script>
		<script type="text/javascript" src="js/kjur/ecparam-1.0.min.js"></script>
                
                <script language="javascript" src="js/jsaes/jsaes.js"></script>
                <script language="javascript" src="js/jsaes/pkb2.js"></script>
		
		
		
                <?php //<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXo5R6bDNr3FVZguAqfDWxgkm45Yh9yt8&sensor=false"></script> ?>
                
                <script type="text/javascript" src="js/ttdemo.js"></script>
                
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
                // the user-specific javascript (filled in keys, etc.)    
                
                    commonPHP::getuserjs();
                    commonPHP::getadminjs();
                
                ?>
        
        <?php
    }
    
    static function masthead()
    {
        ?>
        <table width="800">
            <tr valign="top">
                <td width="300">
                    
                </td>
                <td width="200" align="left">
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
                </td>
                <td width="300" align="right">

                    <span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=xZHg1Or9j1GOfp2XvHLs1pQAQGv4I19C59xVUmfzs4pR3WepPESr7ci"></script></span>
                    
                    <br /><a href="https://tippingtrees.com">Copyright &copy; Tipping Trees, LLC 2014.</a>
                    <br /><a href="mailto:service@tippingtrees.com">service@tippingtrees.com</a>
                    <br />Patents Pending.
                </td>
                <?php /*<td width="300" align="left">
                    <img src="img/bitcoin.png" alt="Bitcoin" /><br /><input type="text" class="rounded" size="36" value="1NprzEmJKqoWSvsLmE5nCbJyn9tgfA3jqe" />
                </td>
                <td width="200" align="left">
                    <a href="javascript:void(0)" onclick="document.getElementById('paypaldonate').submit();"><img src="img/paypal-donate-button.png" alt="Donate" /></a>
                </td> */ ?>
            </tr>
        </table>
            
            
        <?php
    }
    
    
}


?>
