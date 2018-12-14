
<?php


require_once "vendor/autoload.php";

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;

use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Topic;

$apiKey = 'AAAA_k_ZUfc:APA91bH2e9MbpblAt81Kg-vYMVXaYaHtZen_Zm3XCssakrpHr5WLtWXMyYT9-PW35AriU76D_0eSRq_XX54aCot9tHsyq0wgHllisvN6RuvCD5x04XgwzIHmRkkRYtjzId5aciiisslz';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectHttpClient(new \GuzzleHttp\Client());




$message = new Message();
$message->addRecipient(new Topic('news'));

$message->setNotification(new Notification('ម៉េម៉ាយក្តៅស្រួយ', 'ចុចដើម្បីអាន'))
    ->setData(array('id' => 37825));

$response = $client->send($message);
var_dump($response->getStatusCode());