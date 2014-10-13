
<?php

require('../includes/common/common.php');

class Home
{

    static function getHomeContent()
    {
        $header = "Welcome Back to Tipping Trees!";
        $intro  = "What can you do now? <br /><a href=\"#people-peopleInvite\" class=\"button button-small\">Invite</a> your friends and associates to connect reliably and interact securely.";
        $features = array();
        
        $features[] = array('name' => 'Contacts',
                            'desc' => 'Your contacts are the people you can interact with on Tipping Trees. Contacts are made by clicking \'Sign\', which cryptographically signs that person\'s account. A signature does not indicate the quality of an association between people (not the same as "friending", rather). Signatures verify an account\'s identity, rendering it impossible to counterfeit. By relying on keys that have been signed many times, you may have an extremely high confidence that the person you are interacting with now is that same person with whom you have always interacted. Any attempt by a third party to change a user\'s cryptographic credentials immediately triggers a cascade of UNVERIFIED signatures throughout the user base. You can prevent unwanted interaction by blocking a contact, which is transparent to both parties and can be undone at any time.');
        
        $features[] = array('name' => 'Messaging',
                            'desc' => 'Tipping Trees messaging resembles secure email. The security is baked in under-the-hood with each user\'s 2048-bit RSA key and 128-bit AES encryption. Every communication is encrypted and signed before leaving your computer and can only be decypted and verified on your recipient\'s computer so that: <ol><li>Only you and your recipient can read your message; it is never accessible anywhere else.</li><li>The message could only have originated with you, the verified sender.</li></ol><p>When you receive messages the same is true in reverse: only you and the message sender ever have access, and you can be sure the message actually came from the sender\'s account.</p><p>Encrypted messages can be copied to email. This is a backup and a convenience feature. All messages are stored as URL links containing all encrypted information. When the associated user (sender or recipient) is logged in to Tipping Trees the link will decrypt and show the message. To everyone else, the link is indecipherable.</p><p>You may notice that timestamps are missing from \'Sent\' messages. This is one of many features to prevent association between messages in the database. In this case between the message copy in your recipient\'s inbox and the copy in your sent box.</p>');
        
        $features[] = array('name' => 'Groups',
                            'desc' => 'The only way to join a group is by receiving an invitation from the group owner or, if the owner permits, from a member of the group . You can create your own groups to which you may invite others. Groups may be created for families, clubs, business associates, and so on. Each group has an associated shared cryptographic key (128-bit AES) shared to group members by 2048-bit RSA encryption . Each member of the group can see everything any member of the group has posted to the group. Just like personal messages, every post is both encrypted and signed to ensure only group members may read the post and to verify the poster\'s identity.');
        
        $features[] = array('name' => 'Zero Knowledge',
                            'desc' => 'The Tipping Trees service performs all encryption locally on your machine. Everyone is welcome to examine the code. Tipping Trees employs a unique four database table design that not only encrypts your group data, but obscures the relationships between encrypted the data. A third party examining the contents of the database tables cannot determine who is talking with whom, much less what is being shared. To prove our point you may view live, real-time samples from our database <a href="#demo-demoDB">here</a>.');
        
        $steps = array();
        $steps[] = array('Connect','Message','Chat');
        $steps[] = array('<ul class="acklist"><li><a class="button" href="#people-peopleContacts">SEARCH</a><br /> for people by their email address.</li><li><a class="button" href="#people-peopleInvite">INVITE</a><br /> your contacts to join Tipping Trees.</li><li><a class="button" href="#people-peopleContacts">SIGN</a><br /> keys to add people to your contacts page.</li></ul>',
                         '<ul class="acklist"><li><a class="button" href="#pmessage-pmessageCompose">SEND</a><br /> encrypted messages to your contacts.</li><li><a class="button" href="#pmessage-pmessageRead">Decrypt and READ</a><br /> messages from your contacts.</li><li><a class="button" href="#pmessage-pmessageSent">REVIEW</a><br /> the encrypted messages you have sent to your contacts</li></ul>',
                         '<ul class="acklist"><li><a class="button" href="#group-groupNew">CREATE</a><br /> new groups to chat securely with your contacts.</li><li><a class="button" href="#group">INVITE</a><br /> contacts into your group.</li><li><a class="button" href="#group">CHAT</a><br /> with your group securely in real time.</li></ul>');
        
        return array('header' => $header,
                     'intro' => $intro,
                     'features' => $features,
                     'steps' => $steps);
    }


}

?>
