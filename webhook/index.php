<?php

echo "Hello World";

// Create post object


//Facebook
// $fb = new \Facebook\Facebook([
// 	'app_id' => '1905620589497116',
// 	'app_secret' => '9f319465286a71cf0e4286e639962bc7',
// 	'default_graph_version' => 'v2.10',
// 	'default_access_token' => 'EAAbFJt5QSxwBAPbLdxiCNGhaWASx8IpOhmN4vi7P8YTw7CiZBqxZAheC8zokKNqtjv4PVe04wT9nZAITEWZCKWFLZCgX9IZCxB130txNi5MwiWo3XEzdoRCq1jvbHFwvj4VE2VXB9QE9E0wyZAQ47zB1wVwOWKdUrAZD',
// ]);

// $access_token = 'EAAbFJt5QSxwBAPbLdxiCNGhaWASx8IpOhmN4vi7P8YTw7CiZBqxZAheC8zokKNqtjv4PVe04wT9nZAITEWZCKWFLZCgX9IZCxB130txNi5MwiWo3XEzdoRCq1jvbHFwvj4VE2VXB9QE9E0wyZAQ47zB1wVwOWKdUrAZD';


// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
//$helper = $fb->getRedirectLoginHelper();
//$helper = $fb->getJavaScriptHelper();
//$helper = $fb->getCanvasHelper();
// $helper = $fb->getPageTabHelper();


// print_r($_REQUEST['hub_challenge']);
//exit();

// try {
// 	$response = $fb->get('/me');
// } catch(\Facebook\Exceptions\FacebookResponseException $e) {
	
// 	echo 'Graph returned an error: ' . $e->getMessage();
// 	exit;
// } catch(\Facebook\Exceptions\FacebookSDKException $e) {
	
// 	echo 'Facebook SDK returned an error: ' . $e->getMessage();
// 	exit;
// }

// $me = $response->getGraphUser();

// $linkData = [
// 	'link' => 'https://www.khmernewslive.com/?p=60',
// 	'message' => 'ពិតជាកូនល្អកម្ររកបានមែន មានកូនណាខ្លះអាចធ្វើបានដូចគាត់(មានវីដេអូ)'
// ];

// try {
// 	$response = $fb->post('/me/feed', $linkData, $access_token);
// } catch(Facebook\Exceptions\FacebookResponseException $e) {
// 	echo 'Graph returned an error: '.$e->getMessage();
// 	exit;
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
// 	echo 'Facebook SDK returned an error: '.$e->getMessage();
// 	exit;
// }
// $graphNode = $response->getGraphNode();

