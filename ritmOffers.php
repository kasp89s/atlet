<?php
define('RITM_USERNAME', 'Ritm-Z');
define('RITM_PASSWORD', 'RitM-cAtaLOg-469');

if ($_SERVER['PHP_AUTH_USER'] != RITM_USERNAME && $_SERVER['PHP_AUTH_PW'] != RITM_PASSWORD) {
    header("HTTP/1.0 401 Unauthorized");
    header("WWW-authenticate: basic realm=\"Offers\"");
    print ("Access denied. User name and password required.");
    exit;
}

if (is_file('ritmOffers.xml')) {
    $xml = file_get_contents('ritmOffers.xml');

    header('Content-Type: text/xml; charset=utf-8');
    echo $xml;
}
