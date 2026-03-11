<?php
// Fetch currenet URL/
$currentUrl = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// Fetch current Domain name from current URL.
$domain = parse_url($currentUrl, PHP_URL_HOST);

?>