

<?php

function __autoload($class_name) {
    include '../includes/objects/' . $class_name . '.php';
}

?>