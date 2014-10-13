
<?php

require('../includes/common/common.php');

class Branch
{
    static function getBasic()
    {
        ?>
        
            <h1>Public Profile Links</h1>
            <table>
                <tr>
                    <td>
                        <a href="img/RESscreenshot/People_Profile.png" target="_blank"><img src="img/RESscreenshot/People_Profile.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                    <td>
                        <a href="img/RESscreenshot/People_Profile_Mouseover.png" target="_blank"><img src="img/RESscreenshot/People_Profile_Mouseover.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                </tr>
            </table>
            
            <p>Any public profile page is accessible with the account's email address. Mouseover the email address in the profile page to see the public key.</p>
            <p>Example: (URL encoded) <a href="https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D" target="_blank">https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D</a></p>
            <p>Example: (JSON format) <a href="https://tippingtrees.com/index.php#people-peoplePublic-%7B%22email%22%3A%22service%40tippingtrees.com%22%7D" target="_blank">https://tippingtrees.com/index.php#people-peoplePublic-{"email":"service@tippingtrees.com"}</a></p>
            
            <h2>Look up an email address:</h2>
            <form name="emailLookupBranch">
            <p>Email: <input type="text" maxlength="100" name="emailLookup" value="service@tippingtrees.com" size="50" class="rounded" /><br />
                <input type="button" class="button button-medium" value="Look up" onclick="ttLookupEmail(this.form);" /></p>
            </form>

            <h1>Personal Message Links</h1>
            <table>
                <tr>
                    <td>
                        <a href="img/RESscreenshot/NoDecrypt.png" target="_blank"><img src="img/RESscreenshot/NoDecrypt.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                    <td>
                        <a href="img/RESscreenshot/FYEO_Opened.png" target="_blank"><img src="img/RESscreenshot/FYEO_Opened.png" width="350px" style="float: right; padding: 5px 5px 5px 5px;" /></a>
                    </td>
                </tr>
            </table>
            <p><em>Encrypt A Message For Your Eyes Only:</em> Each public profile page has a button allowing anyone, logged in or not, to encrypt a message for that person. A personal message can be shared as a link in Tipping Trees. All required information is contained within the link itself.
            Messages are encrypted in a hybrid format: the message is encrypted using symmetric AES encryption. The AES key is encrypted with the recipient's RSA public key.
            The message is signed with the sender's RSA private key.</p>
            <p>Mouseover the "VERIFIED" stamp in the message view page to see the signature.</p>
            
            <table width="300px" align="right">
                <tr bgcolor="#3E3E3E">
                    <td>
                        <h3>Field</h3>
                    </td>
                    <td>
                        <h3>Description</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        enc_key
                    </td>
                    <td>
                        The RSA encrypted AES key used to encrypt the message. By default the key is 512 bits long in hexadecimal format
                    </td>
                </tr>
                <tr bgcolor="#3E3E3E">
                    <td>
                        enc_mode
                    </td>
                    <td>
                        A string identifier for the AES encryption scheme. Default is 'AES-CTR-128'
                    </td>
                </tr>
                <tr>
                    <td>
                        enc_subject
                    </td>
                    <td>
                        The AES encrypted subject line
                    </td>
                </tr>
                <tr bgcolor="#3E3E3E">
                    <td>
                        enc_body
                    </td>
                    <td>
                        The AES encrypted message body
                    </td>
                </tr>
                <tr>
                    <td>
                        enc_signature
                    </td>
                    <td>
                        The AES encrypted signature of the message
                    </td>
                </tr>
                <tr bgcolor="#3E3E3E">
                    <td>
                        enc_sender
                    </td>
                    <td>
                        The AES encrypted identifier of the message author used to identify the public key to verify the signature. Only one or the other of enc_sender or enc_receiver is populated. enc_sender is usually the populated field.
                    </td>
                </tr>
                <tr>
                    <td>
                        enc_receiver
                    </td>
                    <td>
                        The AES encrypted identifier of the message recipient used to identify the public key to verify the signature. Only one or the other of enc_sender or enc_receiver is populated. enc_receiver is only used to a "sent message" copy for the sender.
                    </td>
                </tr>
                <tr bgcolor="#3E3E3E">
                    <td>
                        post_time
                    </td>
                    <td>
                        The UTC timestamp of the message creation time (unencrypted)
                    </td>
                </tr>
            </table>

            <p>Example: (URL encoded) <br /><a href="https://tippingtrees.com/index.php#pmessage-pmessageView-%7B%22enc_key%22%3A%2270983fbc7bb6a1f092a54b95524997744ef77f7b295860b7612a0da168628a46981738f840894c75ed3d75ca695003d5d1831281fa26abea04161444a137e14fcb5fcb496e9f8efcc02bfaeffc177929c8deab508662903af662b87fcfa1f55826b5e253f6bf157833b197e4d0c0e2bc737b063c704c1e221a1c68e793719e401af271355d294d57f7bc8cdc2d46b21b3fd224e06e6ecab52c9a82b16a182e7505d33de1d8695e072b2a0f790a49bdb80f660ee11dfd61d580c2b97cdc9d259dafea857497d5a656bacb4cb07515cb421560ca5fa268aa6dcc68f0d95eea8fab7bf00368ebcf7a676fc00b2d90f62800f1aeaae986de1d897b1e95b71e204723%22%2C%22enc_mode%22%3A%22AES-CTR-128%22%2C%22enc_subject%22%3A%220b8f3b531b1b1b1b%22%2C%22enc_body%22%3A%220b8f3b531414141429fbf4228bb4551c05bb9dd302e60f2b7886611ae5500f5b69da346bad4c1b8edf45f102599ad529ad542d986dbec91835fb398c70fc1a3154c0a0371c37987ffe82d86ced863404232ad822b5c0c314af19184a82c6ed6762a5da06ad28acabe68368184b7e6e5168a052e203c99e6a6dd8a5a34d2b28cccd33c67ef9fd6f9f14ae4b611bd64dc85057763c2253e057d7b108eb941ac3f8468ff4bcbd3e30eeb2bbbfed3cbbb22851d640253e55b82c7bfe336001ba991d25204e2dfeb297272725d134c62df49dbdd4135f5c7d734de69554e30d246359e5b90c680741d288deb19031fb6d3d7d060fc375dc0daf9728d83c6ff54efea46c1d27609ae0d2%22%2C%22enc_signature%22%3A%220b8f3b534c4c4c4c67f6aeee506dcdfb09875b86a09f3056f94d98eadbbbf61381301d5faa28e11b07438cfb113f685132175d40a94c5ed609aa74000977e7b2cfd7e95929f70664342b008cb0dea97beb31783357f3bb57e8011b51583541c6ff96f20b2d25fb9aadf2ce5ab04ed41d4cb0fd203e8af0268c40a9a215ca1a59aec26e274213753a2b5a2e9bef4ffb8aa658e971b603991ccda8a58f6eeb3a7c283225707ca421a3c2e66df00a33cba80a0847c3141f56f4a8c24d82502fe217b8eef75c21725093dfc35f2db3bfcd2a381e620b76fcfd45a39f4dd96953d1f8e9617c344642054553b4e5332c18f1ffa233a6422e4f400c424a91542e71b41ad41acfca93fd141584784bdeddcaf354dce2a1b0531a79e22dcc765ce9c3d88d1459a61443fa0b1f94f0a06016f4a4188b5b815b6f2c81fc1078d2f7f87a1993a1880df0b49b5bdd9b939d0a706e11314138974f1ca1c62df4a6cd5591286f85f9fdb515ca1bd9a6a4b50cd5dab08790bbcbf664711ce37f488fdc49d11cd4e26b3fafe2522a186f58115ff43312ae067e6d863e4ed2087fc72bed26270448d07735776803ff0fa6b403e05a506bfc1f97ebe5c06465662e4376e18b43d067e9ea04477ac2b86e4c4f130af3c232d66fe7d4ca1bb08069fadba37e2515508942a75d5e8a6972eb915bd611dee87a5280805d9c88add183b8c4c788707156b53e093d3542b1ed693b%22%2C%22enc_sender%22%3A%220b8f3b531b1b1b1b7ac3%22%2C%22enc_receiver%22%3A%22%22%2C%22post_time%22%3A%222014-04-02%2004%3A16%3A11%22%7D" target="_blank">
            <?php echo wordwrap('https://tippingtrees.com/index.php#pmessage-pmessageView-%7B%22enc_key%22%3A%2270983fbc7bb6a1f092a54b95524997744ef77f7b295860b7612a0da168628a46981738f840894c75ed3d75ca695003d5d1831281fa26abea04161444a137e14fcb5fcb496e9f8efcc02bfaeffc177929c8deab508662903af662b87fcfa1f55826b5e253f6bf157833b197e4d0c0e2bc737b063c704c1e221a1c68e793719e401af271355d294d57f7bc8cdc2d46b21b3fd224e06e6ecab52c9a82b16a182e7505d33de1d8695e072b2a0f790a49bdb80f660ee11dfd61d580c2b97cdc9d259dafea857497d5a656bacb4cb07515cb421560ca5fa268aa6dcc68f0d95eea8fab7bf00368ebcf7a676fc00b2d90f62800f1aeaae986de1d897b1e95b71e204723%22%2C%22enc_mode%22%3A%22AES-CTR-128%22%2C%22enc_subject%22%3A%220b8f3b531b1b1b1b%22%2C%22enc_body%22%3A%220b8f3b531414141429fbf4228bb4551c05bb9dd302e60f2b7886611ae5500f5b69da346bad4c1b8edf45f102599ad529ad542d986dbec91835fb398c70fc1a3154c0a0371c37987ffe82d86ced863404232ad822b5c0c314af19184a82c6ed6762a5da06ad28acabe68368184b7e6e5168a052e203c99e6a6dd8a5a34d2b28cccd33c67ef9fd6f9f14ae4b611bd64dc85057763c2253e057d7b108eb941ac3f8468ff4bcbd3e30eeb2bbbfed3cbbb22851d640253e55b82c7bfe336001ba991d25204e2dfeb297272725d134c62df49dbdd4135f5c7d734de69554e30d246359e5b90c680741d288deb19031fb6d3d7d060fc375dc0daf9728d83c6ff54efea46c1d27609ae0d2%22%2C%22enc_signature%22%3A%220b8f3b534c4c4c4c67f6aeee506dcdfb09875b86a09f3056f94d98eadbbbf61381301d5faa28e11b07438cfb113f685132175d40a94c5ed609aa74000977e7b2cfd7e95929f70664342b008cb0dea97beb31783357f3bb57e8011b51583541c6ff96f20b2d25fb9aadf2ce5ab04ed41d4cb0fd203e8af0268c40a9a215ca1a59aec26e274213753a2b5a2e9bef4ffb8aa658e971b603991ccda8a58f6eeb3a7c283225707ca421a3c2e66df00a33cba80a0847c3141f56f4a8c24d82502fe217b8eef75c21725093dfc35f2db3bfcd2a381e620b76fcfd45a39f4dd96953d1f8e9617c344642054553b4e5332c18f1ffa233a6422e4f400c424a91542e71b41ad41acfca93fd141584784bdeddcaf354dce2a1b0531a79e22dcc765ce9c3d88d1459a61443fa0b1f94f0a06016f4a4188b5b815b6f2c81fc1078d2f7f87a1993a1880df0b49b5bdd9b939d0a706e11314138974f1ca1c62df4a6cd5591286f85f9fdb515ca1bd9a6a4b50cd5dab08790bbcbf664711ce37f488fdc49d11cd4e26b3fafe2522a186f58115ff43312ae067e6d863e4ed2087fc72bed26270448d07735776803ff0fa6b403e05a506bfc1f97ebe5c06465662e4376e18b43d067e9ea04477ac2b86e4c4f130af3c232d66fe7d4ca1bb08069fadba37e2515508942a75d5e8a6972eb915bd611dee87a5280805d9c88add183b8c4c788707156b53e093d3542b1ed693b%22%2C%22enc_sender%22%3A%220b8f3b531b1b1b1b7ac3%22%2C%22enc_receiver%22%3A%22%22%2C%22post_time%22%3A%222014-04-02%2004%3A16%3A11%22%7D',50,'<br />',true); ?></a></p>
            
            <h1>Signature Request/Verification Links</h1>
            <p>This functionality is still in testing.</p>

        
        <?php
    }

    static function getContribution()
    {
        ?>
        <table width="740px">
            <tr valign="top" bgcolor="#3E3E3E">
                <td>
                    <h2>INVEST</h2>
                </td>
                <td>
                    If you agree with Tipping Trees' philosophy and the direction we are heading,
                    now is the time to jump on board with an investment. 
                    Please contact us for opportunities and 
                    inquiries. 
                </td>
                <td>
                    <a href="#about-aboutContact" class="button button-medium" target="_blank">Contact Us</a>
                </td>
            </tr>
            <tr valign="top">
                <td>
                    <h2>CODE</h2>
                </td>
                <td>
                    We at Tipping Trees support and believe in 
                    the open-source movement and welcome 
                    voluntary contributions to this project. 
                    Please feel free to submit code fixes, updates, suggestions, etc
                </td>
                <td>
                    <a href="mailto:service@tippingtrees.com" class="button button-medium" target="_blank">service@tippingtrees.com</a>
                </td>
            </tr>
            <tr valign="top" bgcolor="#3E3E3E">
                <td>
                    <h2>DONATE via PayPal</h2>
                </td>
                <td>
                    Tipping Trees is a service provided free of 
                    charge, but is not free to operate. If you 
                    find our service valuable and would like to 
                    see it continue and expand, your 
                    contribution will help that happen.
                </td>
                <td>
                    <a href="javascript:void(0)" onclick="document.getElementById('paypaldonate').submit();"><img src="img/paypal-donate-button.png" alt="Donate" /></a><br />
                </td>
            </tr>
            <tr valign="top">
                <td>
                    <h2>DONATE via Bitcoin</h2>
                </td>
                <td>
                    We are excited about the cryptocurrency 
                    movement and hope to contribute to its 
                    growth. If you are interested in donating 
                    with cryptocurrencies besides bitcoin 
                    (litecoin, namecoin, dogecoin, etc.), please 
                    let us know. 
                </td>
                <td>
                    <img src="img/bitcoin.png" alt="Bitcoin" /><br /><input type="text" class="rounded" size="36" value="1NprzEmJKqoWSvsLmE5nCbJyn9tgfA3jqe" />
                </td>
            </tr>
        </table>
        
        
        <?php
    }
    
    static function getRoadmap()
    {
        
        $header = "THE FUTURE OF TIPPING TREES: A ROADMAP";
        $intro  = 'All of the following enhancements will be implemented as part of the Tipping Trees service. Is there one in particular you\'d like to see? Check out the <a href="#branch-branchContribute">Contribute</a> tab to see ways to get more involved with Tipping Trees.';

        $features = array();
        
        $features[] = array('name' => 'Premium Features',
                            'desc' => 'A premium set of features for advanced users including the ability to form and join more groups, extended memory retention of chat and personal messages, and stronger AES keys.');
        
        $features[] = array('name' => 'Aesthetic Improvements',
                            'desc' => 'As Tipping Trees unique encryption structure and zero-knowledge approach had to be built into the low-level code, site appearance has to this point been a secondary priority. Our approach has been function before form. However, with the basic foundational construction in good working order, we will be improving site aesthetics and responsiveness.');
        
        $features[] = array('name' => 'Crypter Optimization',
                            'desc' => 'With both our file crypter and personal message email integration in place, we will streamline the process for sending an encrypted file to your contacts. Currently much of the process has to be done manually, with passwords and files sent separately. In future implementations, our crypter will accommodate one-click encrypt and send functionality.');
        
        $features[] = array('name' => 'Vouch Scores',
                            'desc' => 'We will provide numeric representations of a signed key\'s reliability based on all its previous interactions. This indicator will be extended to all user keys, and will act as a simple indication of a user\'s verified identity.');
        
        $features[] = array('name' => 'Connection Scores',
                            'desc' => 'We will implement a simple score to indicate the amount of connection between users based upon mutual contacts and the number of steps between the users, another indicator of an account\'s reliability prior to interaction.');
        
        $features[] = array('name' => 'Cryptocurrency Integration',
                            'desc' => 'In addition to encrypted communication channels, Tipping Trees will enable cryptocurrency transfers between contacts.');
        
        $features[] = array('name' => 'Quicksilver Tips',
                            'desc' => 'Tipping Trees will be implementing an in-site economy based on voluntary contributions or tips to other users and allowing for microdonations with an unprecedented tiny fee structure. We believe in rewarding one another for good work and want to make that simple and possible for everyone.');
        
        $features[] = array('name' => 'API',
                            'desc' => 'At a future date, Tipping Trees will provide an API to developers looking to power their own ventures according to the Tipping Trees "simply more than trust" model of security and privacy.');
        
        $features[] = array('name' => 'Business Applications',
                            'desc' => 'Tipping Trees will, with user permission, provide vouched RSA keys and associated email addresses in response to approved requests, allowing reliable encryption standards for routine business contact (invoices, reports, confidential information, etc.). We will also provide a cryptographic verification service for such communications.');
        
        $features[] = array('name' => 'Failsafe Release',
                            'desc' => 'We will set up a customizable failsafe recovery in which your account could be retrieved in the case of death or accidental lockout based on your determined conditions, including time inactive and approval from specified trusted contacts in some user-determined rule.');
        
        $features[] = array('name' => 'Android App',
                            'desc' => 'We need an app! And we will build one, starting with Android.');
        
        $features[] = array('name' => 'File Encryption',
                            'desc' => 'In addition to chat and messaging, Tipping Trees will support a similar process for local image (or other file) encryption and decryption as a part of user interactions in the Tipping Trees network.');
        
        $features[] = array('name' => 'Zero-Knowledge Social Network',
                            'desc' => 'Imagine a social network where your data, your images, your posts, and all of your activity is only ever available to those you permit, all encrypted on your local machine and not accessible on the server or to anyone who you have not specified. We will make this zero-knowledge social network a reality.');
        
        $features[] = array('name' => 'Threaded Collapsible Navigation',
                            'desc' => 'As we grow, we will implement a branched and threaded collapsible navigation structures for groups and content based on a value-based sorting method and user supplied find-similar connections.');
        
        return array('header' => $header,
                     'intro' => $intro,
                     'features' => $features);
    }

}


?>
