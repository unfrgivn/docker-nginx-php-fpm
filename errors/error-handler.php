<?php

$error_code = (int)$_GET['code'] ?? 500;
http_response_code($error_code);

// Example of custom error handlers
/*
switch($error_code) {
  case 404:
    print "404 Not Found";
    break;

  case 502:
    print "Bad Gateway";
    break;
}
*/

exit();

?>
