<?php

require 'vendor/rmccue/requests/library/Requests.php';
require_once "vendor/autoload.php";
Requests::register_autoloader();
use paragraph1\phpFCM\Client as FCMClient;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Topic;
use Stichoza\GoogleTranslate\TranslateClient;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
require('Logger.php');


$client = new Client();
$guzzleClient = new GuzzleClient(array(
	'timeout' => 250,
));
$client->setClient($guzzleClient);


//all links

//Test Firebase FCM
// $apiKey = 'AAAAV5-AEaY:APA91bHPK5NO8y3FBybFQCLsqwHiSz1dboL5zly_FQtdPAotf8wiqf22_bojCudraBdDdSZmR2hD-T73JDuleH_v2wwnhTA-Hra0SD_8ujkOsec7rIP_sVhWpeLIYobGig4H9aZr5vKY';
// $client = new FCMClient();
// $client->setApiKey($apiKey);
// $client->injectHttpClient(new \GuzzleHttp\Client());


// $message = new Message();
// $message->addRecipient(new Topic('news'));

// $message->setNotification(new Notification('Konleng - '.'property_type', 'title'))
// ->setData(array('id' => '000c5b2f-43e8-4f01-b72a-37b3e57965f2'));

// $response = $client->send($message);

// print_r($response);

// exit();

// $crawl_links = array();
// $url_types = array(
// 	'house-villa',
// 	'apartment-flat-condo',
// 	'land'
// );


// for ($j = 0; $j < count($url_types); $j ++){
// 	$property_type = '';
// 	if ($url_types[$j] == 'house-villa'){
// 		$property_type = 'house';
// 	}
// 	if ($url_types[$j] == 'apartment-flat-condo'){
// 		$property_type = 'apartment';
// 	}
// 	if ($url_types[$j] == 'land'){
// 		$property_type = 'land';
// 	}
// 	$crawl_links[] = array(
// 		'link' => 'http://www.sroulk.com/?page=1&real-estate='.$url_types[$j],
// 		'href' => '.property__list__item__title > a',
// 		'title' => '.basic-info > h2.title',
// 		'listing_type' => '.property-cate-price > .lb-cate',
// 		'price' => '.property-cate-price > .lb-price',
// 		'property_type' => $property_type,
// 		'lat' => '.more-info > .map_lat',
// 		'lng' => '.more-info > .map_lng',
// 		'province' => '.more-info > li > a > span',
// 		'name__phone__email' => '.contact-top > ul > li',
// 		'description' => '.location.comment.long-text',
// 		'image' => '.img-responsive'
// 	);
// }


// $url_types = array(
// 	'business-shop-commerce',
// 	'room-sharing'
// );


// for ($j = 0; $j < count($url_types); $j ++){
// 	$property_type = '';
	
// 	if ($url_types[$j] == 'business-shop-commerce'){
// 		$property_type = 'commercial';
// 	}
// 	if ($url_types[$j] == 'room-sharing'){
// 		$property_type = 'room';
// 	}

// 	$crawl_links[] = array(
// 		'link' => 'http://www.sroulk.com/?page=1&real-estate='.$url_types[$j],
// 		'href' => '.property__list__item__title > a',
// 		'title' => '.basic-info > h2.title',
// 		'listing_type' => '.property-cate-price > .lb-cate',
// 		'price' => '.property-cate-price > .lb-price',
// 		'property_type' => $property_type,
// 		'lat' => '.more-info > .map_lat',
// 		'lng' => '.more-info > .map_lng',
// 		'province' => '.more-info > li > a > span',
// 		'name__phone__email' => '.contact-top > ul > li',
// 		'description' => '.location.comment.long-text',
// 		'image' => '.img-responsive'
// 	);
// }

$crawl_links = array();

$GLOBALS['index_crawl'] = 0;

for ($GLOBALS['index_crawl'] = 0; $GLOBALS['index_crawl'] < count($crawl_links); $GLOBALS['index_crawl'] ++){

	$GLOBALS['crawl_link'] = $crawl_links[$GLOBALS['index_crawl']];
	
	
	$crawler = $client->request('GET', $GLOBALS['crawl_link']['link']);
		// $GLOBALS['i'] = 0;
	$links = $crawler->filter($GLOBALS['crawl_link']['href'])->each(function($node_link){
		$GLOBALS['listings'] = array(
			'title' => '',
			'price' => '',
			'property_type' => $GLOBALS['crawl_link']['property_type'],
			'listing_type' => '',
			'description' => '',
			'phone1' => '',
			'phone2' => '',
			'bedrooms' => '',
			'bathrooms' => '',
			'images' => '',
			'province' => '',
			'lat' => '',
			'lng' => '',
			'displayName' => '',
			'address' => '',
			'status' => 1,
			'property_id' => '',
			'userType' => '',
			'email' => '',
			'size' => '',
			'link' => ''
		);
		$post_link = $node_link->attr('href');
		$GLOBALS['listings']['link'] = $post_link;


		$link_check_curl = 'https://konleng.com/api/v1/check_link';
		// $link_check_curl = 'http://localhost:5000/konleng-cloud/us-central1/webApi/api/v1/check_link';

		$link_field = array(
			'link' => $post_link
		);

		$ch = curl_init($link_check_curl);
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($link_field)
		));
		$is_link_exist = curl_exec($ch);
		if($is_link_exist === FALSE){
			die(curl_error($ch));
		}

		echo "Check link: ".$GLOBALS['index_crawl']."\n";
		print_r($GLOBALS['listings']['link'] . " => " . $is_link_exist);
		echo "\n";

		if ($is_link_exist == 'false'){
			print_r("Save New Listing");
			try{
				$client = new Client();
				$crawler = $client->request('GET', $post_link);
				$crawler->filter($GLOBALS['crawl_link']['title'])->each(function ($node) {
					// echo 'title ';
					$title = $node->text();
					$GLOBALS['listings']['title'] = $title;


				});
			}
			catch(Exception $e){
						//send email
				print_r('crawl error');
			}
			try{
				$crawler->filter($GLOBALS['crawl_link']['description'])->each(function ($node) {
					$html = html_entity_decode($node->html());
					if (strpos($html, '</script>') < 1){
						if ($node->text() != ''){
							$description = remove_emoji($node->text());
							$GLOBALS['listings']['description'] = remove_emoji($node->html());
							$bedrooms = 0;
							$bathrooms = 0;

							preg_match('/(\d+)\s*+bed($|s|room|rooms)/i', $GLOBALS['listings']['title'], $match_bedrooms);
							if (empty($match_bedrooms)){

								preg_match('/(\d+)\s*+bed($|s|room|rooms)/i', $description, $match_desc_bedrooms);
								if (empty($match_desc_bedrooms)){
									$bedrooms = 0;
								}
								else{
									$bedrooms = $match_desc_bedrooms[1];	
								}


							}
							else{
								$bedrooms = $match_bedrooms[1];
							}


							preg_match('/(\d+)\s*+bath($|s|room|rooms)/i', $GLOBALS['listings']['title'], $match_bathrooms);

							if (empty($match_bathrooms)){
								preg_match('/(\d+)\s*+bath($|s|room|rooms)/i', $description, $match_desc_bathrooms);
								if (empty($match_desc_bathrooms)){
									$bathrooms = 0;
								}
								else{
									$bathrooms = $match_bathrooms[1];	

								}
							}
							else{
								$bathrooms = $match_bathrooms[1];
							}

							$GLOBALS['listings']['bedrooms'] = $bedrooms;
							$GLOBALS['listings']['bathrooms'] = $bathrooms;

						}
					}
				});
			}
			catch(Exception $e){
						//send email
				print_r('crawl error');
			}
			try{
				$GLOBALS['images'] = array();

				$image = $crawler->filter($GLOBALS['crawl_link']['image'])->eq(0);
				$image = $image->attr('src');

				if (strpos($image, 'http') > -1){
					$image = $image;
				}
				else{
					$image = 'http:'.$image;
				}

				$GLOBALS['images'][] = $image;

				

				// $image = $crawler->filter($GLOBALS['crawl_link']['image'])->each(function($node){

				// 	$image = $node->attr('src');
				// 	if (strpos($image, 'http') > -1){
				// 		$image = $image;

				// 	}
				// 	else{
				// 		$image = 'http:'.$image;
				// 	}
				// 	$GLOBALS['images'][] = $image;

				// });
				$GLOBALS['listings']['images'] = $GLOBALS['images'];

			} catch (Exception $e){
				print_r('eror image');
			}

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['listing_type'])->each(function($node){
					$listing_type = $node->text();

					if (strpos($listing_type, 'Rent') !== false) {
						$listing_type = 'rent';
					}
					else{
						$listing_type = 'sale';
					}


					$GLOBALS['listings']['listing_type'] = $listing_type;


				});
			} catch (Exception $e){
				print_r('eror image');
			}

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['price'])->each(function($node){

					$price = _toInt($node->text());
					$GLOBALS['listings']['price'] = $price;

				});
			} catch (Exception $e){
				print_r('eror image');
			}

				// echo $GLOBALS['crawl_link']['property_type'];
				// echo $GLOBALS['crawl_link']['listing_type'];

			try{
				$GLOBALS['name__phone__email'] = array();
				$image = $crawler->filter($GLOBALS['crawl_link']['name__phone__email'])->each(function($node){
					$GLOBALS['name__phone__email'][] = trim($node->text());
				});

				$displayName = explode('(', $GLOBALS['name__phone__email'][0]);
				$displayName = trim($displayName[0]);
				$userType = explode('(', $GLOBALS['name__phone__email'][0]);
				if (strpos($userType[1], 'Owner') !== false) {
					$userType = 'owner';
				}
				if (strpos($userType[1], 'Agency') !== false) {
					$userType = 'agency';
				}
				if (count($GLOBALS['name__phone__email']) == 3){
					$email = $GLOBALS['name__phone__email'][2];
				}
				else{
					$email = $GLOBALS['name__phone__email'][1].'@sroulk.com';
				}
				$phone = $GLOBALS['name__phone__email'][1];


				$GLOBALS['listings']['phone1'] = $phone;
				$GLOBALS['listings']['email'] = $email;
				$GLOBALS['listings']['userType'] = $userType;
				$GLOBALS['listings']['displayName'] = $displayName;

			} catch (Exception $e){
				print_r('eror phone');
			}

			try{
				$GLOBALS['provinces'] = array();

				$image = $crawler->filter($GLOBALS['crawl_link']['province'])->each(function($node){

					$province = $node->text();

					$GLOBALS['provinces'][] = $province;

				});
				$province = $GLOBALS['provinces'][1];
				$GLOBALS['listings']['province'] = from_camel_case($province);
				$GLOBALS['listings']['address'] = $province;

			} catch (Exception $e){
				print_r('eror province');
			}
			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['lat'])->each(function($node){
					$lat = $node->attr('value');
					$GLOBALS['listings']['lat'] = $lat;
				});
			} catch (Exception $e){
				print_r('eror map');
			}
			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['lng'])->each(function($node){
					$lng = $node->attr('value');
					$GLOBALS['listings']['lng'] = $lng;

				});
			} catch (Exception $e){
				print_r('eror map');
			}

			$url = 'https://konleng.com/api/v1/listings';
			// $url = 'http://localhost:5000/konleng-cloud/us-central1/webApi/api/v1/listings';

			$fields = $GLOBALS['listings'];
			$firebase_token = 'Authorization: Bearer fea';
			$ch = curl_init($url);
			curl_setopt_array($ch, array(
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_HTTPHEADER => array(
					$firebase_token,
					'Content-Type: application/json'
				),
				CURLOPT_POSTFIELDS => json_encode($fields)
			));
			$response = curl_exec($ch);
			if($response === FALSE){
				die(curl_error($ch));
			}



			print_r($response);
		}
		else{
			print_r("Link Exist");
			// (exit)();
		}
		echo "Good";
	});
	echo "TEST";

}


// $url_types = array(
// 	'house-for-sale',
// 	'house-for-rent',
// 	'landed-properties-for-sale',
// 	'landed-properties-for-rent',
// 	'apartment-for-sale',
// 	'commercial-properties-for-sale',
// 	'commercial-properties-for-rent',
// 	'room-for-rent'
// );
$crawl_links = array();
// for ($i = 1000; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/house-for-sale.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name',
// 		'property_id' => 'ul.list-unstyled.item-info > li > span'
// 	);
// }

// for ($i = 2000; $i > 600; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/house-for-rent.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }
for ($i = 2000; $i > 0; $i = $i - 50){
	$crawl_links[] = array(
		'link' => 'https://www.khmer24.com/en/property/landed-properties-for-sale.html?location=&per_page='.$i,
		'href' => 'li.item > a.border.post',
		'title' => '.item-short-description > h1',
		'price' => '.item-short-description > p.price > b.price',
		'property_type' => '',
		'listing_type' => '',
		'description' => 'p.post-description',
		'phone' => 'li.number>a>.num',
		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
		'image' => '.img-contain',
		'province' => 'ul.list-unstyled.item-info > li > span',
		'map' => 'a.map_box.btn_showMap',
		'email' => '.profile > a.header',
		'displayName' => '.profile > a.header > .detail > .name',
		'property_id' => 'ul.list-unstyled.item-info > li > span'
	);
}
// for ($i = 450; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/landed-properties-for-rent.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }
// for ($i = 450; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/apartment-for-sale.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }
for ($i = 2000; $i > 0; $i = $i - 50){
	$crawl_links[] = array(
		'link' => 'https://www.khmer24.com/en/property/apartment-for-rent.html?location=&per_page='.$i,
		'href' => 'li.item > a.border.post',
		'title' => '.item-short-description > h1',
		'price' => '.item-short-description > p.price > b.price',
		'property_type' => '',
		'listing_type' => '',
		'description' => 'p.post-description',
		'phone' => 'li.number>a>.num',
		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
		'image' => '.img-contain',
		'province' => 'ul.list-unstyled.item-info > li > span',
		'map' => 'a.map_box.btn_showMap',
		'email' => '.profile > a.header',
		'displayName' => '.profile > a.header > .detail > .name',
		'property_id' => 'ul.list-unstyled.item-info > li > span'
	);
}
// for ($i = 300; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/commercial-properties-for-sale.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }
// for ($i = 650; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/commercial-properties-for-rent.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }
// for ($i = 100; $i > 0; $i = $i - 50){
// 	$crawl_links[] = array(
// 		'link' => 'https://www.khmer24.com/en/property/room-for-rent.html?location=&per_page='.$i,
// 		'href' => 'li.item > a.border.post',
// 		'title' => '.item-short-description > h1',
// 		'price' => '.item-short-description > p.price > b.price',
// 		'property_type' => '',
// 		'listing_type' => '',
// 		'description' => 'p.post-description',
// 		'phone' => 'li.number>a>.num',
// 		'bedroom_bathroom' => '.list-unstyled.item-fields > li > div > span',
// 		'image' => '.img-contain',
// 		'province' => 'ul.list-unstyled.item-info > li > span',
// 		'map' => 'a.map_box.btn_showMap',
// 		'email' => '.profile > a.header',
// 		'displayName' => '.profile > a.header > .detail > .name'
// 	);
// }

for ($i = 0; $i < count($crawl_links); $i ++){
	Logger::info('Crawl Links '. $i . ' > '. $crawl_links[$i]['link']);
}


$GLOBALS['index_crawl'] = 0;

for ($GLOBALS['index_crawl'] = 0; $GLOBALS['index_crawl'] < count($crawl_links); $GLOBALS['index_crawl'] ++){
	$GLOBALS['crawl_link'] = $crawl_links[$GLOBALS['index_crawl']];

	
	$crawler = $client->request('GET', $GLOBALS['crawl_link']['link']);

	$links = $crawler->filter($GLOBALS['crawl_link']['href'])->each(function($node_link){
			
		preg_match("/landed|house|apartment|commercial|room/",$GLOBALS['crawl_link']['link'], $property_types_matches);
		$property_type = $property_types_matches[0];


		preg_match("/sale|rent/",$GLOBALS['crawl_link']['link'], $listing_types_matches);
		$listing_type = $listing_types_matches[0];

		if ($property_type == 'landed'){
			$property_type = 'land';
		}
		$GLOBALS['listings'] = array(
			'title' => '',
			'price' => '',
			'property_type' => $property_type,
			'listing_type' => $listing_type,
			'description' => '',
			'phone1' => '',
			'phone2' => '',
			'bedrooms' => '',
			'bathrooms' => '',
			'images' => '',
			'province' => '',
			'lat' => '',
			'lng' => '',
			'displayName' => '',
			'address' => '',
			'status' => 1,
			'property_id' => '',
			'userType' => '',
			'email' => '',
			'size' => '',
			'link' => ''
		);
		$post_link = $node_link->attr('href');
		$GLOBALS['listings']['link'] = $post_link;


		$link_check_curl = 'https://konleng.com/api/v1/check_link';
		// $link_check_curl = 'http://localhost:5000/konleng-cloud/us-central1/webApi/api/v1/check_link';

		$link_field = array(
			'link' => $post_link
		);

		$ch = curl_init($link_check_curl);
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($link_field)
		));
		$is_link_exist = curl_exec($ch);
		if($is_link_exist === FALSE){
			die(curl_error($ch));
		}
		Logger::info('Check link '. $GLOBALS['index_crawl'] . ' > '. $GLOBALS['listings']['link']);
		
		// print_r();
		// echo "\n";
			// print_r($is_link_exist);
		
		if ($is_link_exist == 'false'){
			print_r('Save New Listing');
			echo "\n";
			try{
				$client = new Client();
				$crawler = $client->request('GET', $post_link);
				$crawler->filter($GLOBALS['crawl_link']['title'])->each(function ($node) {
					// echo 'title ';
					$title = $node->text();
					$GLOBALS['listings']['title'] = $title;

				});
			}
			catch(Exception $e){
				print_r('error crawl 1');
			}
			try{
				$crawler->filter($GLOBALS['crawl_link']['description'])->each(function ($node) {
					$html = html_entity_decode($node->html());
					if (strpos($html, '</script>') < 1){
						if ($node->text() != ''){
								// $description = remove_emoji($node->text());

								// $description = strpos($description, "សំរាប់ពត៌មានបន្ថែមសូមទូរសព្ទ័មកលេខ") ? substr($description, 0, strpos($description, "សំរាប់ពត៌មានបន្ថែមសូមទូរសព្ទ័មកលេខ")) : $description; 

								// $description = strpos($description, "ទំនាក់ទំនងលេខទូសព្ទ័") ? substr($description, 0, strpos($description, "ទំនាក់ទំនងលេខទូសព្ទ័")) : $description;

							$description = remove_emoji($node->html());
							$description = strpos($description, "<span") ? substr($description, 0, strpos($description, "<span")) : $description; 

							$GLOBALS['listings']['description'] = $description;
								// print_r($GLOBALS['listings']['description'] );
								// exit();
						}
					}
				});
			}
			catch(Exception $e){
						//send email
				print_r('crawl error');
			}
			
			// print_r($image->attr('src'));
			
			try{
				$GLOBALS['images'] = array();



				if (!empty($image = $crawler->filter($GLOBALS['crawl_link']['image']))){
					if (!empty($image->eq(0))){
						
						$image = $crawler->filter($GLOBALS['crawl_link']['image'])->each(function($node){
							$image = $node->attr('src');
							if (strpos($image, 'http') > -1){
								$image = $image;

							}
							else{
								$image = 'http:'.$image;
							}

							$GLOBALS['images'][] = $image;

						});

						$GLOBALS['listings']['images'] = array_splice($GLOBALS['images'], 0, 4);
						// $image = $image->attr('src');

						// if (strpos($image, 'http') > -1){
						// 	$image = $image;
						// }
						// else{
						// 	$image = 'http:'.$image;
						// }
						
						// $GLOBALS['images'][] = $image;

						// $GLOBALS['listings']['images'] = $GLOBALS['images'];
					}
					else{
						$GLOBALS['listings']['images'] = array();
						print_r("image not exists");
					}
				}

				
				
				

			} catch (Exception $e){
				print_r('error image');
			}

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['price'])->each(function($node){
					$price = $node->text();
					$price = _toInt($node->text());
					$GLOBALS['listings']['price'] = $price;

				});
			} catch (Exception $e){
				print_r('error price');
			}

				// echo $GLOBALS['crawl_link']['property_type'];
				// echo $GLOBALS['crawl_link']['listing_type'];

			try{
				$GLOBALS['phones'] = array();
				$image = $crawler->filter($GLOBALS['crawl_link']['phone'])->each(function($node){
					$GLOBALS['phones'][] = $node->text();
				});

				$GLOBALS['phones'] = array_unique($GLOBALS['phones']);
				$GLOBALS['listings']['phone1'] = $GLOBALS['phones'][0];
				if (count($GLOBALS['phones']) == 2){
					$GLOBALS['listings']['phone2'] = $GLOBALS['phones'][1];	
				}
			} catch (Exception $e){
				print_r('eror phone');
			}

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['email'])->each(function($node){
					$GLOBALS['listings']['email'] = explode('/', $node->attr('href'))[4].'@khmer24.com';
				});
			} catch (Exception $e){
				print_r('ERROR Email');
			}

			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['displayName'])->each(function($node){
					$GLOBALS['listings']['displayName'] = $node->text();
				});
			} catch(Exception $e){
				print_r('Error Display Name');
			}

			try{
				$GLOBALS['bedrooms_bathrooms'] = array();
				$image = $crawler->filter($GLOBALS['crawl_link']['bedroom_bathroom'])->each(function($node){
					$GLOBALS['bedrooms_bathrooms'][] = $node->text();
				});

				if (strpos($GLOBALS['bedrooms_bathrooms'][0], 'Size') !== false) {
					$GLOBALS['listings']['size'] = $GLOBALS['bedrooms_bathrooms'][1];
				}
				else{
						// $bedrooms = explode(':', $GLOBALS['bedrooms_bathrooms'][1]);
						// $bathrooms = explode(':', $GLOBALS['bedrooms_bathrooms'][3]);
					$GLOBALS['listings']['bedrooms'] = $GLOBALS['bedrooms_bathrooms'][1];
					$GLOBALS['listings']['bathrooms'] = $GLOBALS['bedrooms_bathrooms'][3];
				}

			} catch (Exception $e){
				print_r('eror bedroom_bathroom');
			}
			try{
				$GLOBALS['provinces'] = array();
				$image = $crawler->filter($GLOBALS['crawl_link']['province'])->each(function($node){
					$province = $node->text();
					$GLOBALS['provinces'][] = $province;

				});
				$province = $GLOBALS['provinces'][3];
				$GLOBALS['listings']['province'] = from_camel_case($province);
				$GLOBALS['listings']['address'] = $province;

					// print_r($province);
			} catch (Exception $e){
				print_r('eror province');
			}

			try{
				$GLOBALS['property_id'] = array();
				$image = $crawler->filter($GLOBALS['crawl_link']['property_id'])->each(function($node){
					$property_id = $node->text();
					$GLOBALS['property_id'][] = $property_id;
				});

				$property_id = $GLOBALS['property_id'][1];
				$GLOBALS['listings']['property_id'] = $property_id;

					// print_r($province);
			} catch (Exception $e){
				print_r('eror province');
			}

			


			try{
				$image = $crawler->filter($GLOBALS['crawl_link']['map'])->each(function($node){
					$map = $node->attr('href');
					$map = explode('q=', $map)[1];
					$map = explode('&', $map)[0];
					$lat = explode(',', $map)[0];
					$lng = explode(',', $map)[1];
					$GLOBALS['listings']['lat'] = $lat;
					$GLOBALS['listings']['lng'] = $lng;
				});
			} catch (Exception $e){
				print_r('eror map');
			}


			try{
				$url = 'https://konleng.com/api/v1/listings';
				// $url = 'http://localhost:5000/konleng-cloud/us-central1/webApi/api/v1/listings';

				$fields = $GLOBALS['listings'];
				$firebase_token = 'Authorization: Bearer fea';
				$ch = curl_init($url);
				curl_setopt_array($ch, array(
					CURLOPT_POST => TRUE,
					CURLOPT_RETURNTRANSFER => TRUE,
					CURLOPT_HTTPHEADER => array(
						$firebase_token,
						'Content-Type: application/json'
					),
					CURLOPT_POSTFIELDS => json_encode($fields)
				));
				$response = curl_exec($ch);
				if($response === FALSE){
					die(curl_error($ch));
				}

				echo "Response: ";
				print_r($response);
				echo "\n";
			}catch(Exception $e){
				echo "Not Valid - Maybe No Image - Skip";
				// $image = $crawler->filter($GLOBALS['crawl_link']['image'])->eq(0);
			}

			

				// if ($GLOBALS['total_count'] == count($crawl_links) - 2){
				// 	$apiKey = 'AAAAV5-AEaY:APA91bHPK5NO8y3FBybFQCLsqwHiSz1dboL5zly_FQtdPAotf8wiqf22_bojCudraBdDdSZmR2hD-T73JDuleH_v2wwnhTA-Hra0SD_8ujkOsec7rIP_sVhWpeLIYobGig4H9aZr5vKY';
				// 	$client = new FCMClient();
				// 	$client->setApiKey($apiKey);
				// 	$client->injectHttpClient(new \GuzzleHttp\Client());


				// 	$message = new Message();
				// 	$message->addRecipient(new Topic('news'));

				// 	$message->setNotification(new Notification('Konleng - '.$GLOBALS['listings']['property_type'], $GLOBALS['listings']['title']))
				// 	->setData(array('id' => $GLOBALS['listings']['id']));

				// 	$response = $client->send($message);
				// }

				// $GLOBALS['total_count'] = $GLOBALS['total_count'] + 1;



		}
		else{
			print_r('Listing Exists! ');
			echo "\n";
		}

	});



}

function gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

function _toInt($str)
{
	return (int)preg_replace("/([^0-9\\.])/i", "", $str);
}
function from_camel_case($input) {
	preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
	$ret = $matches[0];
	foreach ($ret as &$match) {
		$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
	}
	$province = implode('-', $ret);
	if ($province == 'kampong-som'){
		$province = 'preah-sihanouk';
	}
	return $province;
}
// curl_close($ch);
function remove_emoji($text){
	return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
}