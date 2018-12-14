<?php

require_once 'Mobile_Detect.php';

$detect = new Mobile_Detect;

function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);

    exit();
}

$ios = 'https://itunes.apple.com/us/app/khmer-news-live-24/id1440587029';
$android = 'https://play.google.com/store/apps/details?id=com.khmernewslive24.app';
$home = 'https://www.khmernewslive24.com';

if( $detect->isiOS() ){
 	Redirect($ios, false);
}
 
if( $detect->isAndroidOS() ){ 	
 	Redirect($android, false);
}

// Redirect($home, false);


         

?>