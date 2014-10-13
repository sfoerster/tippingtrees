<?php

//require('./common/common.php');

// ensure a session is started
$a = session_id();
if(empty($a)){
    session_start();
    $a = session_id();
}
    


// if already logged in, show a common title bar
echo "<div id=\"titlebar\">\n";
if (commonPHP::isloggedin()) {

    // Open, valid session
    $s = $_SESSION['user'];
    $u = unserialize($s);
    
    $rsakey = new RSA($u->user_id);

    //echo "Welcome, {$u->first_name} {$u->last_name} ({$u->username})! ";
    //echo "<a href=\"#invite\" class=\"button button-small\">Invite</a>";
    //echo "<a href=\"logout.php\" class=\"button button-small\">Logout</a>";
    
    echo "<table>\n";
    echo "<tr valign=\"top\">\n";
    echo "<td align=\"left\">\n";
    
    ?>
        <div id="titleBarInfo">
            <script type="text/javascript">
                $(document).ready(function() {
                   displayTitleBar('titleBarInfo',function() {}); 
                });
            </script>    
        </div>
    <?php
    
    
    /*echo "<span title=\"" . $rsakey->pubkey . "\">\n";
    echo "<h1>" . $u->last_name . ", " . $u->first_name . "</h1>\n";
    echo "<h2>" . $u->email . " (" . $u->username . ")</h2>\n";
    //echo "<div class=\"htmlkey\">" . nl2br($rsakey->pubkey) . "</div>\n";
    echo "</span>\n";*/
    
    echo "</td>\n";
    echo "<td>\n";
    
    echo "<a href=\"logout.php\" class=\"button button-small\">Logout</a>\n";
    echo "<a href=\"#people-peopleInvite\" class=\"button button-small\">Invite</a>\n";
    
    if (isset($_SESSION['TT_MSG']) && ($_SESSION['TT_MSG'] != '') ) {
        echo "<p>{$_SESSION['TT_MSG']}</p>";
        $_SESSION['TT_MSG'] = '';
    }
    
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    
    //echo "</div>";
    

} else { // If no session value is present, show login screen:
    
    // No session
    include ('../includes/scripts/loginform.php');
    
    echo "<a href=\"#register-deleteAccount\">Lost Password</a>\n";
    
    
}

/*
if (commonPHP::checkisadmin()) {
    
    echo "\n<div id=\"adminbar\">\n";
    
    echo "Admin:";
    echo "<a href=\"#\" class=\"button button-small\" onClick=\"navBlogEntry();\">Write Blog</a>";
    
    echo "\n</div>\n";
}
//*/

//echo "\n<div id=\"chatbar\"></div>\n";


echo "\n</div>\n"; // titlebar

?>

