<?php

define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslive/wp-load.php' );
require_once('/var/www/khmernewslive/wp-admin/includes/media.php');
require_once('/var/www/khmernewslive/wp-admin/includes/file.php');
require_once('/var/www/khmernewslive/wp-admin/includes/image.php');

require_once "vendor/autoload.php";


use Stichoza\GoogleTranslate\TranslateClient;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

$client = new Client();
$guzzleClient = new GuzzleClient(array(
    'timeout' => 250,
));
$client->setClient($guzzleClient);



$proxies = explode("\n", file_get_contents('/var/www/khmernewslive/webhook/proxies.txt'));
$GLOBALS['arr_proxies'] = array();
foreach ($proxies as $p){
	if ( preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\:?([0-9]{1,5})?/', $p, $match) ) {
	   $GLOBALS['arr_proxies'][] = $match['1'] . (isset($match['2']) ? ':' . $match['2'] : '');
	}
}


// print_r($GLOBALS['arr_proxies'][rand(0,9)]);
$GLOBALS['post_id'] = '';
$GLOBALS['cats'] = array(
	array('id'=>6, 'keywords' => array('ប្លែកៗ','ថ្មីៗ','ផ្សេងៗ')),
	array('id'=>2, 'keywords' => array('ព័ត៌មានសង្គម','កីឡា','ព័ត៌មាន')),
	array('id'=>1, 'keywords' => array('យល់ដឹង', 'គំនិត', 'ចំណេះដឹង','បទពិសោធន៍')),
	array('id'=>3, 'keywords' => array('សិល្បៈ និងកម្សាន្ត', 'សិល្បះ និង កម្សាន្ត', 'វីដេអូ', 'កម្សាន្ត')),
	array('id'=>4, 'keywords' => array('សុខភាព', 'ចែកចាយ','មេរៀនជីវិត','សុខភាព និងសម្រស់','កម្រងទេសភាព'))
);




$appId = '1905620589497116';
$appSecret = '9f319465286a71cf0e4286e639962bc7';
$pageId = '161062997686788';
// $userAccessToken = 'EAAbFJt5QSxwBAGZCZAO6P8VshdS3Tiq3eZCs9rPrKRpy9yqRAezxiqZBws0oziLeGJFSMv7AZCZBGif6sKdsfOdhWDcTTJOXugZCfxEA9ULZCXxbP4uS6zsDIZBoc7NhzCuVNde4uMJTXIUrhf7B64Hoik7hLc2ifjkl91SGiKIZAbLUxqdlmMCIu8ZBZC2JuhgS9oUwzMF2HdWZBoQZDZD';

$fb = new \Facebook\Facebook([
  'app_id' => $appId,
  'app_secret' => $appSecret,
  'default_graph_version' => 'v2.10',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
$loginUrl = $helper->getLoginUrl('https://www.khmernews-live.com/webhook/facebook-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

// try {
//   $userAccessToken = $helper->getAccessToken();
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
//   // There was an error communicating with Graph
//   echo $e->getMessage();
//   exit;
// }
// echo "SHIT";
// echo "<br>";

	  
// $client = $fb->getOAuth2Client();

// try {
//   $userAccessToken = $client->getLongLivedAccessToken($userAccessToken);
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
//   echo $e->getMessage();
//   exit;
// }

// try {
//   // Returns a `Facebook\FacebookResponse` object
//   $response = $fb->get('/me?fields=id,name', $userAccessToken);
// } catch(Facebook\Exceptions\FacebookResponseException $e) {
//   echo 'Graph returned an error: ' . $e->getMessage();
//   exit;
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
//   echo 'Facebook SDK returned an error: ' . $e->getMessage();
//   exit;
// }

// $user = $response->getGraphUser();

// echo 'Name: ' . $user['name'];





