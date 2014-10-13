
<?php


function echoKey($key) {
    
    $out = substr(trim(str_replace("\r\n","\\n\\\r\n",$key)),0,-3);
    
    return $out;
}

function keyToDBsafe($key) {
    
    $keydb = strtr(base64_encode(addslashes(gzcompress(serialize($key), 9))) , '+/=', '-_,');
    
    return $keydb;

}

function keyFromDBsafe($key) {
    
    $keyss = unserialize(gzuncompress(stripslashes(base64_decode(strtr($key, '-_,', '+/=')))));
    
    return $keyss;
}



?>

