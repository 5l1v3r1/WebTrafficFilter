<?php
require_once dirname( dirname(__FILE__) ).'/classes/traffix.php';
$traffix = new traffix;
$traffix->monitor_css_file( dirname(__FILE__).'/style.css' );
?>
