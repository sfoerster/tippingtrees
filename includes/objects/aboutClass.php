
<?php

require('../includes/common/common.php');

class aboutClass
{
    static function getFirst()
    {
        ?>
        
            <h1>First Login</h1>
            <h2>Find & Sign Contacts</h2>
            
            <table style="float: right; padding-left: 10px">
                <tr>
                    <td>
                        <a href="img/RESscreenshot/SearchForContacts.png" target="_blank"><img src="img/RESscreenshot/SearchForContacts.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="img/RESscreenshot/PublicProfile_Mouseover.png" target="_blank"><img src="img/RESscreenshot/PublicProfile_Mouseover.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                </tr>
            </table>
            
            <ul  class="acklist">
                <li>Send Invitations to Friends, Family, and Others</li>
                <p>As a member of Tipping Trees you can send invitations to anyone else by email address. Simply click the "Invite" button at the top of the page. Enter the email address in the provided box and press "Send".</p>
                <li>Search for contacts by email address on the Contacts page.</li>
                <li>Sign To Add As Contact</li>
                <p>You control interaction with your contacts. You can invite them to groups. You can block them.</p>
            </ul>
            
            <p>Any public profile page is accessible with the account's email address. Mouseover the email address to see the public key. See the <a href="#branch-branchBasic">Branch</a> page for more information.</p>
            <p>Example: (URL encoded) <a href="https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D" target="_blank"><?php echo wordwrap('https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D',50,'<br />',true); ?></a></p>
            <p>Example: (JSON format) <a href="https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D" target="_blank">https://tippingtrees.com/index.php#people-peoplePublic-{"email":"service@tippingtrees.com"}</a></p>
            
            <div class="clear"></div>
            
            <h1>Groups</h1>
            <h2>An Intranet in a Secure Network</h2>
            
            <a href="img/RESscreenshot/Chat.png" target="_blank"><img src="img/RESscreenshot/Chat.png" width="350px" style="float: left; padding: 5px 10px 5px 5px;" /></a>
            
            <p>Groups are common spaces for multiple people to share. Everything is encrypted with a common key and in Tipping Trees' patent pending <a href="#demo-demoDB">Zero Knowledge</a> design so that
            the database tables are not only encrypted but also compartmentalized to prevent any recovery of who is associating with whom. Only the group owner/administrator can invite people to the group, or allow group members to invite. Anyone may leave a group at any time.
            The group does track current and former members of the group and indicates if the user is an active member or if they have left so everyone knows who has been given the group's shared key and could decrypt group posts.</p>
            
            <div class="clear"></div>
            
            <h1>Messages</h1>
            <h2>More than Encrypted & Authenticated Email</h2>
            <table>
                <tr>
                    <td>
                        <a href="img/RESscreenshot/Messages_Read.png" target="_blank"><img src="img/RESscreenshot/Messages_Read.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                    <td>
                        <a href="img/RESscreenshot/Messages_Compose.png" target="_blank"><img src="img/RESscreenshot/Messages_Compose.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                </tr>
            </table>
            <a href="img/RESscreenshot/Messages_Composition.png" target="_blank"><img src="img/RESscreenshot/Messages_Composition.png" width="350px" style="float: right; padding: 5px 15px 5px 10px" /></a>
            <p>Messages shows your Inbox and Outbox of encrypted mail. Timestamps are shown in your inbox, but are not kept for sent mail (to prevent associating inbox and sent mail in the database).</p>
            <p>Click Compose to write a message. Then begin typing the email of your recipient. Click "Select As Recipient". Type your message and send. As with all messages in Tipping Trees,
            a message key is generated and RSA encrypted with the recipient's public key. The message key is used to AES encrypt the subject, body, and signature of the message.</p>
            <p>Optionally, you can elect to send a copy of the RSA encrypted message to both the sender's and recipient's email address. This copy will also contain a link that may be opened to reveal the contents of the message if the intended user is logged-in to Tipping Trees. To anyone else, the link is useless.</p>
            <p>We are always working to improve your experience here at Tipping Trees and offer you more functionality, to see where we are headed, take a look at our <a href="#branch-branchRoadmap">Roadmap</a>, and if you want to get more involved with the future of Tipping Trees the <a href="#branch-branchContribute">Contribute</a> tab is a good place to start.</p>
        <?php
    }
    
    static function getFeatures()
    {
        ?>
        
        <table style="float: right; margin-left: 10px">
            <tr>
                <td>
                    <h3>Setting</h3>
                </td>
                <td>
                    <h3>Default</h3>
                </td>
            </tr>
            <tr bgcolor="#3E3E3E">
                <td>
                    Symmetric Cipher Algorithm and Mode
                </td>
                <td>
                    <?php echo prefs::getSymCipher(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Maximum Group Chat Entries
                </td>
                <td>
                    <?php echo ConfigSettings::getMaxChatLines(); ?>
                </td>
            </tr>
            <tr bgcolor="#3E3E3E">
                <td>
                    Maximum Groups a User May Create
                </td>
                <td>
                    <?php echo ConfigSettings::maxGroups(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Maximum Groups a User May Join
                </td>
                <td>
                    Unlimited
                </td>
            </tr>
            <tr bgcolor="#3E3E3E">
                <td>
                    Maximum Inbox Messages Retained
                </td>
                <td>
                    <?php echo ConfigSettings::maxInboxMsgs(); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Maximum Sent Messages Retained
                </td>
                <td>
                    <?php echo ConfigSettings::maxSentMsgs(); ?>
                </td>
            </tr>
            <tr bgcolor="#3E3E3E">
                <td>
                    Maximum Activity Notifications Retained
                </td>
                <td>
                    <?php echo ConfigSettings::maxNotifications(); ?>
                </td>
            </tr>
        </table>
        
        <h1>Brief Overview of Selected Features</h1>
        <ul class="acklist">
            <li><h2>Personal Messages</h2></li>
                <p>Tipping Trees messages are secure communications between two people. The sender encrypts and signs each message. The recipient decrypts and verifies each message.</p>
            <li><h2>Groups</h2></li>
                <p>Groups have a shared key that encrypts everything posted by each member of the group. All group data is stored encrypted on Tipping Trees' server using Tipping Trees' patent pending Zero Knowledge design. See <a href="#demo-demoDB">Database Demo</a> for more details.</p>
            <li><h2>Web of Trust</h2></li>
                <p>Every Tipping Trees account has at least a 2048-bit RSA public/private key pair associated with it. Any member can sign any other member's key. Anyone can view each user's public profile which verifies each signature made on that account. These signatures cannot be forged. This "web of trust" design provides an extremely high level of confidence that the person you're communicating with at a particular email account is the same person you have always communicated with at that account.</p>
            <li><h2>Blocking</h2></li>
                <p>Any user can block and unblock any other user at any time. Blocking prevents receiving invitations to groups or receiving personal messages. Blocking is transparent to both parties.</p>
            <li><h2>For Your Eyes Only</h2></li>
                <p>Each public profile page includes the capability to encrypt a message that can only be viewed by that person (including yourself on your own page). If you are logged in while viewing the profile the message will also be signed. The message is processed as a link that may be posted anywhere then opened by the intended recipient at any time. See the <a href="#branch-branchBasic">Personal Message Links</a> for more information.</p>
        </ul>
        
        <?php
    }

    static function getVision()
    {
        ?>
        
        <h1>Vision Statement:</h1> 
        <p>Tipping Trees strives to ensure liberty through technology. Liberty to communicate ideas securely, exchange value freely, and control personal data. The genius of innovation has opened possibilities unimaginable to past generations, but antiquated institutions and archetypes have prevented the realization of this potential.  It is time society caught up.</p>
         
        <p>Verify, then trust. We do not want your trust, unearned. Our encrypted communication system does not and will not rely on blind trust at any point, it relies on innovation, openness, and verification. Check our code. Check our methods. Check our results. Tipping Trees is secure not because the owners are trustworthy or the method is secret but because the technology is good. We believe in the right to exclusive communication as the default, not the exception.</p>
         
        <p>Value for value in a voluntary exchange is the fundamental principle of prosperity, fair to both parties. Any barrier between producer and consumer does a disservice to either one or both parties, whether that barrier be bureaucracy, inefficiency,  extortion, promotion, artificial scarcity or anything else parasitically exploiting the transaction. Tipping Trees is the vehicle to eliminate any distance between a content producer and consumer, optimized for a digital world. We will empower the creators, inventors, analysts, innovators, and contributors to earn their living from their good works at the hand of those appreciative to their efforts.</p>
         
        <p>Our ambitions are expansive , advancing every aspect of modern interaction. But our methods remain personal. You will never see a paid advertisement on Tipping Trees. You will never need to share personal data to use our service. We are not looking to sell your information to the highest bidder. You are our partner, not our product.</p>
         
        <p>Tipping Trees is about building the world we want from the one we have. It's about beating back the abuses of Big Data. It's about eliminating the annoyance and manipulation of propaganda. It's about precluding  extortion. But mostly, it's about rewarding good work. Tipping Trees is a community in it's fullest sense. Contribute, appreciate, and exchange - we are all better off together.</p>
         
        <?php //<p>Our vision is revolution.</p> ?>
        
        
        <?php
    }
    
    static function getWork()
    {
        ?>
        
        <h1>Work Opportunities:</h1>
        
        <p>We are looking for partners. We don't care about your credentials, we care about your ability. If you share the vision and can do the job, you can join the team. Peruse the code already used in the site. Send us updates or modifications (either functional or cosmetic). Be sure to attach your Tipping Trees credentials so that we can tip you if we use your code. Depending on quality, we may offer you more regular work. At this early stage the position comes with stock, not pay. Work may be full time or part time, as long as it's productive.</p>
 
                
        <?php
    }
    
    static function getInvest()
    {
        ?>
        
        <h1>Investing:</h1>
        
        <p>We are also open to investors but will not be offering majority control. If you support our ambitions you are welcome aboard, but we will be steering the ship. If you are interested contact us here to discuss terms.</p>
        
        <?php
    }
    
    static function getCredit()
    {
        ?>
        
        <h2>We at Tipping Trees would like to gratefully acknowledge the contributions, services, and 
information provided by all of the following: </h2>
        
        <p><h2>Code:</h2></p>
        <ul class="acklist">
            <li><h3><a href="http://www-cs-students.stanford.edu/~tjw/jsbn/" target="_blank">Tom Wu/Stanford Javascript Cryptography</a></h3></li>
            <li><h3><a href="https://www.pidder.com/pidcrypt/" target="_blank">Pidder Crypto Library</a></h3></li>
            <li><h3><a href="https://github.com/kjur/jsrsasign" target="_blank">KJUR jsrsasign</a></h3></li>
            <li><h3><a href="https://code.google.com/p/crypto-js/" target="_blank">Crypto JS</a></h3></li>
            <li><h3><a href="http://www.tutorialzine.com/" target="_blank">Tutorialzine</a></h3></li>
            <li><h3><a href="https://www.random.org/" target="_blank">Random.org</a></h3></li>
        </ul>
        
        <p><h2>Software used by the founders:</h2></p>
        <ul class="acklist">
            <li><h3><a href="https://www.lastpass.com" target="_blank">LastPass</a></h3></li>
            <li><h3><a href="https://www.ghostery.com" target="_blank">Ghostery</a></h3></li>
            <li><h3><a href="http://www.ubuntu.com" target="_blank">Ubuntu</a></h3></li>
        </ul>
        
        <p><h2>External Links:</h2></p>
        <ul class="acklist">
            <li><h3><a href="https://www.schneier.com/" target="_blank">Bruce Schneier</a></h3></li>
            <li><h3><a href="https://www.eff.org/issues/bloggers/legal/join" target="_blank">Electronic Frontier Foundation</a></h3></li>
            <li><h3><a href="https://epic.org/" target="_blank">Electronic Privacy Information Center</a></h3></li>
            <li><h3><a href="https://www.openssl.org/about/" target="_blank">OpenSSL</a></h3></li>
            <li><h3><a href="http://people.csail.mit.edu/rivest/" target="_blank">Ron Rivest</a></h3></li>
            <li><h3><a href="https://blogs.rsa.com/" target="_blank">RSA: Speaking of Security</a></h3></li>    
        </ul>
        
        
        
        <?php
    }
    
    static function getDetails()
    {
        ?>
        <img src="img/info/EncryptionSpecs.png" width="350px" style="float: left; padding-right: 10px" />
        <table width="380px">
            <tr bgcolor="#3E3E3E">
                <td>
                    <h2>Tipping Trees, LLC Policy and Terms</h2>
                </td>
                <td width="250px">
                    <ul class="acklist">
                        <li><a href="https://docs.google.com/presentation/d/1MnqDIG5myOvicBbFwQb2I1zgWn5v15mx5YX6p1mk55c/pub?start=false&loop=false&delayms=3000" target="_blank">Vision Statement</a></li>
                        <li><a href="https://docs.google.com/document/d/1HzOiUB-yn3ls0k8_aOcMGxuSjqBusW8DKafpb6ABCHc/pub" target="_blank">Frequently Asked Questions</a></li>
                        <li><a href="https://docs.google.com/document/d/1RrXqhrPlAkqKPPbQ3L5fzvOCBHyFQM62sM6F5vy0Bwo/pub" target="_blank">Terms and Conditions</a></li>
                        <li><a href="https://docs.google.com/document/d/103jWbdjsS08Z5gNf4Z7w1OEQownmM9xwkKTLiU0kM8w/pub" target="_blank">Privacy Policy</a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <h2>RSA Documentation</h2>
                </td>
                <td>
                    <ul class="acklist">
                        <li><a href="https://docs.google.com/presentation/d/1WbIPWVVF-C1PhOD6wOVhehklrZZX_1DVFFQ_yFOpXzg/pub?start=false&loop=false&delayms=30000" target="_blank">Beginner: RSA Analogy</a></li>
                        <li><a href="https://docs.google.com/presentation/d/1Do-FPoSJgB267f-aW8wZBFi-v6fyWA92NfremqU-yoI/pub?start=false&loop=false&delayms=60000" target="_blank">Intermediate: RSA Algorithm</a></li>
                        <li><a href="https://docs.google.com/presentation/d/1_QBUEoiVfigGXF1zdGICAI4oXBjTkvlv0ManqZyGHFQ/pub?start=false&loop=false&delayms=60000" target="_blank">Advanced: Detailed RSA Implementation</a></li>
                    </ul>
                </td>
            </tr>
        
            
            
            
            
            <?php /*<h1>Bitcoin:</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/Um63OQz3bjo?list=PL5VtcFR87hhqxAuaDLEWObpqhan-Kovin" frameborder="0" allowfullscreen></iframe>
            */ ?>
            
        </table>
        
        <?php
    }
    
    static function getFAQ()
    {
        
            $text = "<h1>1. Overview</h1>
            <h2>1.1 What is Tipping Trees?</h2>
            Tipping Trees is a free, strongly encrypted communication network with bitcoin integration and minimal-fee micro-transfer capability. The system is designed to encourage the voluntary supply of valuable content and reciprocating voluntary bitcoin tips.
             
            <h2>1.2 Why use Tipping Trees?</h2>
            Tipping Trees treats your privacy as the default, not the exception. We are creating an online world to mimic the offline one, where your personal information is in your control, your cash can be securely transferred in any amount, and your reputation is based on reliable networking. As a member of Tipping Trees, you can communicate freely, reward others for their good work, and be rewarded yourself.
             
            <h2>1.3 Why should I trust Tipping Trees?</h2>
            You shouldn't. This endeavor does not require your trust. Frankly, we don't even trust ourselves. So, in designing the low-level fundamentals of Tipping Trees, we have not left anything to trust. The code we use is open source, you can check it yourself. The methods we've chosen are the most widely tested and scientifically acknowledged, you can read the papers. All encryption happens client-side, on your local machine. All keys are generated in your browser and your private keys never exist anywhere else unencrypted. Your password too, we never have access to it and cannot even reset it for you. Only you have access to your bitcoin wallet private key. We require no personal information outside of an email address. But don't take our word for it, try it out, test our methods. Verify first, then trust.
             
            <h2>1.4 How does Tipping Trees work?</h2>
            To join Tipping Trees, you must be invited by a current member. When you receive an invitation you may register on the site, selecting a strong password with the understanding that if it is lost we cannot recover it for you.
             
            Upon registration, you generate a 2048-bit RSA key pair entirely in your browser. The public key is made available for sending you encrypted information, but the private key is kept entirely to yourself. You also generate a bitcoin wallet private key and the associated wallet address entirely in your browser. The address is available so bitcoins may be transferred to you but the wallet private key necessary to obtain coins from your wallet is kept entirely to yourself.
             
            Equipped with these keys you may interact with other members. You may search for contacts, invite new members, vouch for people you know, and acquire your own verifications from people who know you. When you communicate on Tipping Trees, either in chat or by personal message, your cryptographically strong 2048-bit RSA keys are used exchange OAEP padded 256-bit AES keys and messages are encrypted in AES-CTR mode. You may start group chats and control who participates or you may join other members' chats.
             
            You may also participate in the Tipping Trees bitcoin micro-transfer market by voluntarily contributing at least 0.02 BTC, you will then be able to distribute minicoins (you may distribute 1 minicoin for every 0.001BTC you contribute) to anyone on the Tipping Trees network however you see fit. Other users will also be able to give minicoins to you and once you reach a pre-selected payout level of at least 0.05BTC (higher levels mean lower fees) you will receive an accumulated donation in your bitcoin wallet.
             
            <h2>1.5 Who are the founders/owners?</h2>
            Tipping Trees, LLC was founded by Steven Foerster and Calvin Gardner in March, 2013. Anyone is welcome to contribute to the Tipping Trees project, submit your contributions here [hyperlink].
             
            <h1>2. Signing Up</h1>
            <h2>2.1 How can I sign up?</h2>
            To sign up for Tipping Trees you must be invited by a member. Registration for Tipping Trees is free of charge. Only an email address is required. You will select  a username freely and set a password. Once you have confirmed receipt of the Terms and Conditions of Tipping Trees membership and our Privacy Policy your account will be activated.
             
            <h2>2.2 How do I invite others to join?</h2>
            Once you have an account, you may invite your contacts to join by clicking on the people button and then the invite button and providing email addresses for invitations. In the future it will also be possible to post to Facebook so your friends can request an invite from you.
             
            <h2>2.3 I'm not in the US, can I join?</h2>
            Yes. Provided doing so does not break any laws in your country.
             
            <h2>2.4 What information does Tipping Trees require?</h2>
            An email address owned by you.
             
            <h2>2.5 Can I change my email later?</h2>
            No. Not without losing any data associated with your account. Though you can always start a new account if you wish.
             
            <h2>2.6 Can organizations (businesses, charities, etc.) join?</h2>
            Yes. Please do. Tipping Trees can be an excellent tool to fund charities or support businesses.
             
            <h2>2.7 What should I use for a password?</h2>
            We force you to pick a password at least 8 characters in length containing at least one uppercase letter, lowercase letter, number, and special character. However, if you really care about security you should pick something significantly longer, random, and containing all character types. We recommend LastPass as a password manager and generator.
             
            <h2>2.8 Can I change my password?</h2>
            Yes. You can reset your password on your profile page.
             
            <h2>2.9 What if I lose my password?</h2>
            Your password is never sent to the server unencrypted. We don't know it. We cannot recover it. Without the password your account is lost. You will need to make another account. It's a tough line but that is the cost of true security.
             
            <h2>2.10 Can I delete my account?</h2>
            Of course. Contact us to delete your account.
             
            <h2>2.11 If I delete my account does Tipping Trees retain any data?</h2>
            No.
             
            <h1>3. Security</h1>
            <h2>3.1 Is Tipping Trees secure?</h2>
            Yes. Messages are encrypted with 256-bit AES keys in CTR mode. Keys are exchanged by 2048-bit RSA public key cryptography and signed/verified according to the RSA-PSS signature scheme. All keys are generated client side and private keys are never sent to the server unencrypted. We don't have your password, cannot get it, cannot obtain your private keys. Chats have minimal persistence after which all messages are obliterated. Personal messages also are not stored permanently, but are obliterated after a set time. We cannot even tell who is chatting with who as encrypted database tables are kept separately on the server. Also, Tipping Trees sessions require session keys client-side and server-side to match. And best of all, the code is open source so you can check it yourself.
             
            <h2>3.2 So I need to have Javascript activated?</h2>
            Yes, to use Tipping Trees your browser must allow JavaScript. Tipping Trees performs encryption using JavaScript prior to sending anything to the server.
             
            <h2>3.3 And Cookies?</h2>
            Yes, you need to enable cookies within your browser to use Tipping Trees. Cookies store information temporarily. To be sure your data is secure always log out before exiting Tipping Trees.
             
            <h2>3.4 Are there backdoors?</h2>
            No. Tipping Trees is designed so backdoor access to your information is not possible. All encryption is performed on your local machine. Keys are seeded with random data pulled from random.org and transported directly to your browser via SSL/TSL  (HTTPS) tunnel. Your password is never transported to the server unencrypted and  can not be reset. Industry standard encryption techniques ensure your information is unobtainable by us.
             
            <h2>3.5 What is RSA encryption?</h2>
            RSA is a public-key cryptosystem widely used for secure data transmission and based in large prime numbers. The RSA public key is made available to anyone and is used to encrypt a message. The mathematically related private key is kept secret and used to decrypt the message.  The security of RSA encryption relies on the difficulty of factoring the product of two large prime numbers. Tipping Trees uses industry standard 2048-bit RSA keys, estimated to be sufficient encryption until the year 2030.
             
            <h2>3.6 What is AES encryption?</h2>
            Advanced Encryption Standard (AES) was established by the U.S. National Institute of Technology (NIST) and is used worldwide. For AES the same key is used to encrypt and decrypt data. Tipping Trees uses RSA keys for the exchange of OAEP (Optimal Asymmetric Encryption Padding) padded 256-bit AES keys which are in turn used to encrypt and decrypt messages. NIST suggests that 256-bit symmetric keys are equivalent in strength to 15360-bit RSA keys.
             
            <h2>3.7 What is CTR mode?</h2>
            CTR (Counter) mode turns a block cipher into a stream cipher by incorporating a counter into the encryption. CTR is widely accepted and is one of two block cipher modes recommended by Bruce Schneier and Niels Ferguson.
             
            <h2>3.8 What is the RSA-PSS signature scheme?</h2>
            RSA-PSS is the current industry standard PKCS #1v2.1 based on the Probabilistic Signature Scheme by Mihir Bellare and Phillip Rogaway .
             
            <h2>3.9 How are keys generated?</h2>
            All keys are generated client side by JavaScript using a random number generator seeded with randomness.
             
            <h2>3.10 How are keys seeded?</h2>
            Keys are seeded with random data via SSL/TSL (HTTPS) directly from Random.org which uses radio receivers to  generate random numbers of discrete uniform distribution from atmospheric noise. Studies by eCOGRA, TST Global, and Gaming Labs International of the generated numbers confirm that their randomness is sound.
             
            <h2>3.11 Can I change my RSA keys?</h2>
            Yes, you may change your RSA keys. But you will lose all vouching signatures and will have to recollect. Also, any data collected with your old keys will be irrecoverable.
             
            <h2>3.12 What if I want longer keys?</h2>
            At this time there is no option for longer keys. In the future, premier features like longer keys, additional chat groups, and more persistence on chat and personal messages will be implemented.
             
            <h2>3.13 What are the risks?</h2>
            Security and convenience are always a trade off. Tipping Trees has chosen to emphasize security, often at the expense of comfort - for instance lost passwords are irrecoverable and there are some inconvenient consequences to changing keys.  Still, even if everything is done right, security is never perfect. Tipping Trees strives for total security but can't always prevent password theft, man in the middle attacks, spoofing, etc. So be cautious. Verify, then trust.
             
            <h1>4. Vouching</h1>
            <h2>4.1 Why should I care about vouching?</h2>
            Vouching (accomplished by contacts signing RSA keys) is designed to facilitate a web of trust environment in which users accumulate vouch scores for reliable interactions with other users. High vouch scores provide one avenue for determining a users reliability in communication and tip receipt.
             
            <h2>4.2 Must I be vouched to participate?</h2>
            No, you may have an account with a null vouch score. However, if you are unvouched and try to vouch for another user it will not count towards their score, this is to prevent inflated scores from fake accounts. It may also be difficult to gain access to any groups or to receive many tips without a vouch score.
             
            <h2>4.3 How is a vouch score calculated?</h2>
            Vouch score is based on the number of contacts willing to sign your RSA keys, the vouch scores of those contacts, and their removal from the initial level users (the founders).
             
            <h2>4.4 Is there an upper limit to vouch scores?</h2>
            No, a vouch score can always increase. The lowest possible vouch score is zero.
             
            <h2>4.5 How can I up my vouch score?</h2>
            Invite more contacts, interact with groups reliably, become involved with the Tipping Trees project.
             
            <h2>4.6 Who should I vouch for?</h2>
            Vouch for people you know, people you have reliable interactions with, and contacts you wish to maintain.
             
            <h2>4.7 Can I report unreliable users?</h2>
            Should it be necessary, feel free to contact our support staff from the contact us page.
             
            <h2>4.8 Can I change accounts but keep my vouch score?</h2>
            No, your score is tied to your set of keys. If you change accounts you will have new keys and will need to re-accumulate your score.
             
            <h1>5. Chat</h1>
            <h2>5.1 What are groups?</h2>
            Groups are the basic community unit of Tipping Trees. They consist of a single administrating user and the set of users with whom the administrator shares the encryption key. A group's administer is the sole authority for who is in or out of a group. In the future groups will further be distinguished into private (only administer can share the key), semi-private (any group member can share the key), and public (key freely available).
             
            Groups may be small, perhaps only a chat between two friends, or very large, with 100s or 1000s of members.
             
            <h2>5.2 Are groups protected by encryption?</h2>
            Yes, all chats within a group are encrypted. Only the users who can access the group can read the chats. Tipping Trees cannot access the chat messages. Further, chat messages are not persistent, they are deleted from the Tipping Tree servers after a specific time or accumulation of messages.
             
            <h2>5.3 Won't tipping trees know who is chatting with who?</h2>
            No, Tipping Trees uses a multi-table database system (patent pending) to separate users, chats, and messages. Tipping Trees does not know who is chatting with who.
             
            <h2>5.4 How many groups can I start?</h2>
            10.
             
            <h2>5.5 How many groups can I belong to?</h2>
            As many as you'd like.
             
            <h2>5.6 Who is in charge of groups?</h2>
            The user that starts the group is completely in charge of the group, including the ability to invite members and disinvite them.
             
            <h2>5.7 Is there a limit to the number of members allowed in a group?</h2>
            No.
             
            <h2>5.8 How do I join a group?</h2>
            Either receive an invitation (you may need to contact the group administrator to ask for an invite) or start your own group and invite the people you'd like to talk to.
             
            <h2>5.9 What else can I do with a group?</h2>
            Currently, you can chat with the members of the group. In the future, the group will be able to host content, separate into public and private portions, accept tips, and operate comment threads surrounding content. If you would like to assist in the development of this functionality, go here [hyperlink].
             
            <h1>6. Messaging</h1>
            <h2>6.1 Are personal messages encrypted?</h2>
            Yes. Personal messages are always encrypted on Tipping Trees.
             
            <h2>6.2 What is the difference between a signed and unsigned message?</h2>
            By signing a message you are verifying your identity to the recipient. See the encryption documentation for an explanation of how signing and verification works.
             
            <h2>6.3 Can I send to multiple recipients?</h2>
            Not at present, though we may enable this functionality in the future.
             
            <h2>6.4 How long are messages available?</h2>
            By default messages are deleted 30 days after receipt. Also, all but your most recent 25 messages will be deleted from your sent folder. Additionally, messages may be deleted sooner anytime you wish. Deletes are permanent and cannot be recovered. Note: deleting messages only removes them on your end, the associated sender/recipient's copy is not deleted at the same time as the two are not connected in any way in our database.
             
            <h2>6.5 Won't you know who is sending messages to who?</h2>
            Nope. As in chat, Tipping Trees will not be able to tell what users are sending messages to each other.
             
            <h2>6.6 Who can send me messages?</h2>
            You can either choose to only accept signed messages from your contacts or you can choose to accept unsigned messages as well which could come from any user.
             
            <h2>6.7 Can I send encrypted messages to a an email address?</h2>
            Yes. There is an option to send the encrypted message to a users email. They will be provided with the encrypted text and a link to Tipping Trees where they can decrypt and read your message with their private key. This encryption is still good but obviously if you send the encrypted message to their email then it is not possible for Tipping Trees to be sure the encrypted text is not persistent. We will still delete the version on our servers but the email provider likely will not. The cost of the convenience of receiving the encrypted message at an email address is slightly less security but the choice is yours.
             
            <h2>6.8 What about attachments?</h2>
            At present we do not support attachments to personal messages. However, you are free to use the demo file encrypter [hyperlink] to encrypt a file, send it by regular email, and communicate the encryption key by Tipping Trees personal message. They could then decrypt the file themselves using the demo file encrypter.
             
            In the future, we will add attachment functionality to the personal messaging feature.
             
            <h2>6.9 What if I want to keep my messages longer?</h2>
            At present this is not possible, however in the future we plan to provide extended message storage for premium users. If you would like to be involved with the development of this feature, go here [hyperlink].
            Additionally, by copying personal messages to email the message may be retained indefinitely and recovered by clicking the link and logging in to Tipping Trees.
             
            <h1>7. Bitcoin</h1>
            <h2>7.1 What is Bitcoin?</h2>
            Bitcoin is a distributed peer-to-peer digital currency that can be transferred instantly and securely between any two people in the world. It's like electronic cash.
             
            <h2>7.2 What are bitcoins?</h2>
            Bitcoins are the unit of currency of the Bitcoin system. A commonly used shorthand for this is “BTC” to refer to a price or amount (e.g. “100 BTC”). There are such things as physical bitcoins, but ultimately, a bitcoin is just a number associated with a Bitcoin Address. A physical bitcoin is simply an object, such as a coin, with the number carefully embedded inside.
             
            <h2>7.3 How can I get bitcoins?</h2>
            There are a variety of ways to acquire bitcoins:
            Receive tips on Tipping Trees.
            Buy bitcoins on Bitcoin Exchanges such as MtGox.
            Trade giftcards for bitcoins at GiftCardDrainer.com. 
            Link your bank account to a service like Coinbase.
            Find someone to trade cash for bitcoins in-person through a local directory.
            Participate in a mining pool or set up your
            Visit sites that provide free samples and offers.
            PayPal exposes the seller to too much risk of claimed non-receipt so it is typically difficult to purchase bitcoins through Paypal.
             
            <h2>7.4 What if I already have a bitcoin address?</h2>
            That's OK, now you have two. It is not uncommon to use multiple bitcoin addresses, and of course you can transfer between the two if needed (though there may be a fee).
             
            <h2>7.5 Can I change my bitcoin address?</h2>
            At present your bitcoin address associated with your Tipping Trees account is not changeable. If you want a different address you will need to create another account.
             
            <h2>7.6 Do I have to participate in bitcoin tipping?</h2>
            No, you don't have to tip anyone. But you may still receive tips into your wallet, they are yours to do whatever you want with.
             
            <h2>7.7 How are new bitcoins created?</h2>
            New bitcoins are generated by the network through mining. Mining nodes on the network are awarded bitcoins each time they find the solution to a certain mathematical problem  (thereby creating a new block). The reward for solving a block is automatically adjusted so that roughly every four years of operation of the Bitcoin network, half the amount of bitcoins created in the prior 4 years are created. 10,500,000 bitcoins were created in the first 4 (approx.) years from January 2009 to November 2012. Every four years thereafter this amount halves, so it will be 5,250,000 over years 4-8, 2,625,000 over years 8-12, and so on. The total number of bitcoins in existence will never exceed 21,000,000. The last block that will generate coins will be block #6,929,999 which should be generated at or near the year 2140. The total number of coins in circulation will then remain static at 20,999,999.9769 BTC. Even if the allowed precision is expanded from the current 8 decimals, the total BTC in circulation will always be slightly below 21 million (assuming everything else stays the same). For example, with 16 decimals of precision, the end total would be 20,999,999.999999999496 BTC.
             
            Blocks are mined every 10 minutes, on average and for the first four years (210,000 blocks) each block included 50 new bitcoins. As the amount of processing power directed at mining changes, the difficulty of creating new bitcoins changes. This difficulty factor is calculated every 2016 blocks and is based upon the time taken to generate the previous 2016 blocks. The number of blocks times the coin value of a block is the number of coins in existence. The coin value of a block is 50 BTC for each of the first 210,000 blocks, 25 BTC for the next 210,000 blocks, then 12.5 BTC, 6.25 BTC and so on.
             
            <h2>7.8 What do I call the various denominations of bitcoin?</h2>
            1 BTC = 1 bitcoin
            0.001 BTC = 1 mBTC = 1 millibitcoin (also referred to as mbit (pronounced em-bit) or millibit or even bitmill)
            0.000 001 BTC = 1 μBTC = 1 microbitcoin (also referred to as ubit (pronounced yu-bit) or microbit)
            0.000 000 01 BTC = 1 satoshi (pronounced sa-toh-shee)
             
            <h2>7.9 Where does the value of Bitcoin stem from? What backs up Bitcoin?</h2>
            Bitcoins have value because they are useful and because they are scarce. As they are accepted by more merchants, their value will stabilize. Bitcoins, like dollars and euros, are not backed up by anything except the variety of merchants that accept them.
             
            <h2>7.10 Won't loss of wallets and the finite amount of Bitcoins create excessive deflation, destroying Bitcoin?</h2>
            If Bitcoin users lose their wallet, that money is gone forever, unless they find it again, it's gone completely out of circulation, rendered utterly inaccessible to anyone. As people will lose their wallets, the total number of Bitcoins will slowly decrease and Bitcoin will likely experience gradual deflation. However, Bitcoin offers a simple solution: infinite divisibility. Bitcoins can be divided up and trade into as small of pieces as one wants, so no matter how valuable Bitcoins become, one can trade them in practical quantities. Even if, in the far future, so many people have lost their wallets that only a single Bitcoin, or a fraction of one, remains, Bitcoin should continue to function just fine.
             
            <h2>7.11 I was sent some bitcoins and they haven't arrived yet! Where are they?</h2>
            Don't panic! There are a number of reasons why your bitcoins might not show up yet, and a number of ways to diagnose them. You can check pending transactions in the network by going here and then searching for your address. If the transaction is listed here then it's a matter of waiting until it gets included in a block before it will show in your client. If the transaction is based on a coin that was in a recent transaction then it could be considered a low priority transaction. Transfers can take longer if the transaction fee paid was not high enough. If there is no fee at all the transfer can get a very low priority and take hours or even days to be included in a block.
             
            <h2>7.12 What happens when someone sends me a bitcoin but my computer is powered off?</h2>
            Bitcoins are not actually \"sent\" to your wallet; the software only uses that term so that we can use the currency without having to learn new concepts. Your wallet is only needed when you wish to spend coins that you've received. If you are sent coins when your wallet client program is not running, and you later launch the wallet client program, the coins will eventually appear as if they were just received in the wallet. That is to say, when the client program is started it must download blocks and catch up with any transactions it did not already know about.
             
            <h2>7.13 What is mining?</h2>
            Mining is the process of spending computation power to secure Bitcoin transactions against reversal and introducing new Bitcoins to the system.
            Technically speaking, mining is the calculation of a hash of the a block header, which includes among other things a reference to the previous block, a hash of a set of transactions and a nonce. If the hash value is found to be less than the current target (which is inversely proportional to the difficulty), a new block is formed and the miner gets the newly generated Bitcoins (25 per block at current levels). If the hash is not less than the current target, a new nonce is tried, and a new hash is calculated. This is done millions of times per second by each miner.
             
            <h2>7.14 Is Bitcoin safe?</h2>
            Bitcoin isn't infallible. It can be cheated, but doing so is extremely difficult. Bitcoin was designed to evade some of the central problems with modern currencies – namely, that their trustworthiness hinges upon that of people who might not have users' best interests in mind. Every currency in the world (other than Bitcoin) is controlled by large institutions who keep track of what's done with it, and who can manipulate its value. And every other currency has value because people trust the institutions that control them.
             
            Bitcoin doesn't ask that its users trust any institution. Its security is based on the cryptography that is an integral part of its structure, and that is readily available for any and all to see. Instead of one entity keeping track of transactions, the entire network does, so Bitcoins are astoundingly difficult to steal, or double-spend. Bitcoins are created in a regular and predictable fashion, and by many different users, so no one can decide to make a whole lot more and lessen their value. In short, Bitcoin is designed to be inflation-proof, double-spend-proof and completely distributed.
             
            Nonetheless, there are ways that one can acquire Bitcoins dishonestly. One can steal private keys. Key theft isn't something that Bitcoin security has been designed to prevent: it's up to users to keep theirs safe. But the cryptography is designed so that it is completely impossible to deduce someone's private key from their public one. As long as you keep your private key to yourself, you don't have much to worry about.  Bitcoin can be ripped off – but doing so would be extremely hard and require considerable expertise and a staggering amount of processing power. And it's only going to get harder with time. Bitcoin isn't impenetrable, but it's close enough to put any real worries in the peripherals.
             
            <h2>7.15 How can I be sure I don't lose my wallet?</h2>
            Back it up! Keep your password somewhere safe and confidential.
             
            <h1>8. Tips</h1>
            <h2>8.1 How does the tip system work?</h2>
            By accumulating smaller tips into aggregate transfers we can minimize the loss to transfer fees. Users voluntarily transfer BTC to Tipping Trees and are then able to distribute minicoins across the Tipping Trees network however they wish. When recipients reach preselected levels of minicoin donations Tipping Trees will transfer them BTC in aggregate, less fees.
             
            <h2>8.2 How do I tip someone?</h2>
            Simply click on the tip buttons for that user and enter the tip amount in minicoins, divisible to 3 decimal places.
             
            <h2>8.3 Can I pay for a service with Tips on Tipping Trees?</h2>
            No, all transfers on Tipping Trees are tips in appreciation for a service already rendered or content already provided freely. You may not pay for anything in the traditional sense. Tips are voluntary, never necessary and all content must be provided freely.
            
            <h2>8.4 Is there a minimum or maximum tip?</h2>
            The minimum tip at present is 0.001 minicoins. There is no maximum tip.
             
            <h2>8.5 What percentage of my tip goes to transfer fees?</h2>
            The percentage of your tip that goes to transfer fees will depend on the tipped users payout level. Higher levels have lower fees. The base payout level of 0.05BTC has an associated 7% transfer fee with fees decreasing as payout level increases (for instance a payout level of 0.1BTC has a fee of 5%, a payout of 0.2BTC a fee of 3.5% etc.).
             
            <h2>8.6 Can I tip someone who doesn't have a Tipping Trees account?</h2>
            No, not on the Tipping Trees network you can't. However, you can transfer money from your Bitcoin wallet to theirs if they provide the address.
             
            <h2>8.7 Can I accept tips on other sites?</h2>
            Not at present, you can however link them to your Tipping Trees profile to accept tips. Or you can provide your bitcoin wallet address and they can send you money there.
             
            <h2>8.8 When do I get my money?</h2>
            Once you reach your payout level we will transfer bitcoin into your wallet.
             
            <h2>8.9 How do I sell the bitcoins in my wallet?</h2>
            You can sell bitcoins on one of the many exchanges.
             
            <h2>8.10 Can I use other payment methods?</h2>
            No, to participate in the Tipping Trees market you must use bitcoins. You can donate to us with PayPal but for participation in tipping you must use bitcoins.
             
            <h2>8.11 So what am I - a contributor or a creator?</h2>
            Both! You can tip other users for information, content, actions, etc. you appreciate or you can provide those yourself and accept tips from other users.
             
            <h2>8.12 Is there a way to automatically tip someone?</h2>
            Not yet, but we do recognize that some users may want to institute recurring tipping for ongoing content they appreciate. We will support that functionality in the future. If you would like to help getting this feature up and running, go here.
             
            <h1>9. Legal</h1>
            <h2>9.1 What about taxes?</h2>
            We can't offer blanket legal advice on that, sorry. You'll have to talk to a lawyer.
             
            <h2>9.2 Are tips tax-deductible?</h2>
            We aren't accountants either so can't offer much help there. It will probably depend on the recipient of your tips. Donations to us (Tipping Trees) are not tax deductible.
             
            <h2>9.3 What activities are prohibited on Tipping Trees?</h2>
            All illegal activities are strictly prohibited. Any selling of any kind is prohibited. Content must be provided freely without any kind of binding sale, all tips must be voluntary. This means that even if the tip didn't happen the user would still receive the content, item, information, etc.
             
            <h2>9.4 Are tips or donations recoverable?</h2>
            No. All transfers are final. They may not be rescinded.
             
            <h2>9.5 Can you guarantee security?</h2>
            No, you use Tipping Trees at your own risk. However, as our code is open source, it is possible for you to verify our methods. Our intent is to do everything possible to keep you secure, even from us. Still we acknowledge that however unlikely, it is possible that someone someday may crack RSA encryption. Going forward we will adjust to keep Tipping Trees secure.
             
            <h1>10.  Development</h1>
            <h2>10.1 What browsers are supported?</h2>
            Tipping Trees has been tested with the following browsers:
            Firefox, Version 3 or above
            Safari, Version 4 or above
            Opera, Version 9 or above
            Chrome, Version 3 or above
            Tipping Trees may work with other browsers than those listed but we do not recommend it.
             
            </h2>10.2 Is there a Tipping Trees mobile app?</h2>
            Not yet. Want to make one? Go here. We're always looking for talent for our team.
             
            <h2>10.3 Does Tipping Trees have an API?</h2>
            Not yet. Want to put one together? Go here. We're always looking for talent for our team.
             
            <h2>10.4 Is the Tipping Trees code available?</h2>
            Yes, site is based on open-source code. Available here.
             
            <h2>10.5 Why are there no ads on Tipping Trees?</h2>
            Because we hate ads. Especially paid ads. Companies that pay for advertisements necessarily pass that cost on to their consumers without actually improving their product materially at all. They are paying to make you believe the product is better rather than actually making the product better or selling it cheaper. It is annoying and it will not happen here.
             
            <h2>10.6 Is this profitable?</h2>
            Not yet. Our business model is based on voluntary contributions from you and small fees of the bitcoin transfers associated with the Tipping Trees market. If you believe in our aims and want to keep Tipping Trees going, please either donate or better yet tip each other. We believe Tipping Trees can be a profitable or valuable addition to the lives of all our users. Let's make it happen.
             
            <h2>10.7 How can I contribute to Tipping Trees?</h2>
            Tip us. Invite your contacts. Participate in the site. If you see something you can improve go for it. We're always looking for new ideas and will reward good work.";
            
            $text = nl2br($text,"<br />\n");
            
            echo $text;
    
        
    }

}


?>
