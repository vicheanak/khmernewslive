<?php

define( 'WP_USE_THEMES', false ); 
require( '/var/www/khmernewslive/wp-load.php' );
require_once('/var/www/khmernewslive/wp-admin/includes/media.php');
require_once('/var/www/khmernewslive/wp-admin/includes/file.php');
require_once('/var/www/khmernewslive/wp-admin/includes/image.php');

// require 'vendor/rmccue/requests/library/Requests.php';


require_once "vendor/autoload.php";

Requests::register_autoloader();

use paragraph1\phpFCM\Client as FCMClient;
use paragraph1\phpFCM\Message;

use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Topic;




use Stichoza\GoogleTranslate\TranslateClient;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;


$client = new Client();
$guzzleClient = new GuzzleClient(array(
	'timeout' => 250,
));
$client->setClient($guzzleClient);




// print_r($GLOBALS['arr_proxies'][rand(0,9)]);
$GLOBALS['post_id'] = '';
$GLOBALS['cats'] = array(
	array('id'=>6, 'keywords' => array('ប្លែកៗ','ថ្មីៗ','ផ្សេងៗ')),
	array('id'=>2, 'keywords' => array('ព័ត៌មានសង្គម','កីឡា','ព័ត៌មាន')),
	array('id'=>1, 'keywords' => array('យល់ដឹង', 'គំនិត', 'ចំណេះដឹង','បទពិសោធន៍')),
	array('id'=>3, 'keywords' => array('សិល្បៈ និងកម្សាន្ត', 'សិល្បះ និង កម្សាន្ត', 'វីដេអូ', 'កម្សាន្ត')),
	array('id'=>4, 'keywords' => array('សុខភាព', 'ចែកចាយ','មេរៀនជីវិត','សុខភាព និងសម្រស់','កម្រងទេសភាព')),
	array('id'=>16, 'keywords' => array('កីឡា')),
	array('id'=>17, 'keywords' => array('បច្ចេកវិទ្យា'))
);



function Generate_Featured_Image( $file, $post_id ){
    // Set variables for storage, fix file filename for query strings.
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );

	if ( ! $matches ) {
		return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
	}

	$file_array = array();
	$file_array['name'] = basename( $matches[0] );
	
    // Download file to temp location.
    // echo strpos($file, 'http');
	if (strpos($file, 'http') > -1){
		$file_array['tmp_name'] = download_url( $file );	
	}
	else{
		$file_array['tmp_name'] = download_url( 'http:'.$file );
	}

    // print_r($file_array['tmp_name']);

    // If error storing temporarily, return the error.
	if ( is_wp_error( $file_array['tmp_name'] ) ) {

		return $file_array['tmp_name'];
	}


    // Do the validation and storage stuff.
	$id = media_handle_sideload( $file_array, $post_id);

    // If error storing permanently, unlink.
	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );

		return $id;
	}


	return set_post_thumbnail($post_id,$id);

}


function Generate_Image( $file ){
    // Set variables for storage, fix file filename for query strings.
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );

	if ( ! $matches ) {
		return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL' ) );
	}

	$file_array = array();
	$file_array['name'] = basename( $matches[0] );
	
    // Download file to temp location.

	if (strpos($file, 'http') > -1){
		$file_array['tmp_name'] = download_url( $file );	
	}
	else{
		$file_array['tmp_name'] = download_url( 'http:'.$file );
	}


    // If error storing temporarily, return the error.
	if ( is_wp_error( $file_array['tmp_name'] ) ) {
		return $file_array['tmp_name'];
	}

	
    // Do the validation and storage stuff.
	$id = media_handle_sideload( $file_array, 0);

    // If error storing permanently, unlink.
	if ( is_wp_error( $id ) ) {
		@unlink( $file_array['tmp_name'] );
		return $id;
	}

	$image_attributes = wp_get_attachment_image($id, Array(640, 480) );



	if ( $image_attributes ) :
		return $image_attributes;
	endif;

	return false;



}



$crawl_links =  array(
	array(
		'link' => 'http://www.topnews4khmer.com/',
		'href_filter' => 'h3.entry-title > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.entry-content.mh-clearfix > p',
		'image_filter' => 'div.entry-content.mh-clearfix > p > img',
		'iframe_filter' => 'div.entry-content.mh-clearfix > p > iframe',
		'tag_input' => array('TopNews4Khmer'),
		'feature_image_filter' => 'div.entry-content.mh-clearfix > figure.entry-thumbnail > img'
	),
	array(
		'link' => 'http://cambopost.net/',
		'href_filter' => 'h2.post-box-title > a',
		'title_filter' => 'h1.name.post-title.entry-title > span',
		'post_category' => 2,
		'content_filter' => 'div.entry > p',
		'image_filter' => 'div.entry > p > img',
		'iframe_filter' => 'div.entry > p > iframe',
		'tag_input' => array('Cambopost'),
		'feature_image_filter' => 'div.entry > p > img'
	),
	array(
		'link' => 'https://camnews.asia/archives/category/news/local-news',
		'href_filter' => 'h2.post-title.entry-title > a',
		'title_filter' => 'h1.single-title.post-title.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.single-post-body > div.single-entry > p',
		'image_filter' => 'div.single-post-body > div.single-entry > p > img',
		'iframe_filter' => 'div.single-post-body > div.single-entry > div.fb-video',
		'tag_input' => array('Camnews'),
		'feature_image_filter' => 'div.post-thumb > img'
	),
	// array(
	// 	'link' => 'kamsantoday.com',
	// 	'href_filter' => 'h3.entry-title.td-module-title > a',
	// 	'title_filter' => 'h1.entry-title',
	// 	'post_category' => 2,
	// 	'content_filter' => 'div.td-post-content.td-pb-padding-side > p',
	// 	'image_filter' => 'div.td-post-content.td-pb-padding-side > p > img',
	// 	'iframe_filter' => 'div.td-post-content.td-pb-padding-side > p > iframe',
	// 	'tag_input' => array('Kamsantoday'),
	// 	'feature_image_filter' => 'div.td-post-featured-image > a > img'
	// ),
	array(
		'link' => 'https://www.poromean.com/archives/author/dara',
		'href_filter' => 'div.td-module-thumb > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.td-post-content > p',
		'image_filter' => 'div.td-post-content > p > img',
		'iframe_filter' => 'div.td-post-content > div.fb-video',
		'tag_input' => array('Poromean'),
		'feature_image_filter' => 'div.td-post-featured-image > a > img'
	),
	array(
		'link' => 'https://www.poromean.com/archives/author/sinuon',
		'href_filter' => 'div.td-module-thumb > a',
		'title_filter' => 'h1.entry-title',
		'post_category' => 2,
		'content_filter' => 'div.td-post-content > p',
		'image_filter' => 'div.td-post-content > p > img',
		'iframe_filter' => 'div.td-post-content > div.fb-video',
		'tag_input' => array('Poromean'),
		'feature_image_filter' => 'div.td-post-featured-image > a > img'
	),
	array(
		'link' => 'http://postkhnews.com/archives/category/ពត៏មានសង្គម',
		'href_filter' => 'div.summary > h4.news-title > a',
		'title_filter' => '.page-title > h1',
		'post_category' => 2,
		'content_filter' => 'div.post-content > article > p',
		'image_filter' => 'div.post-content > article > p > img',
		'iframe_filter' => 'div.post-content > article > p > iframe',
		'tag_input' => array('Postkhnews'),
		'feature_image_filter' => 'div.post-content > figure.feature-image > img'
	),
	array(
		'link' => 'https://khmernews.news/',
		'href_filter' => 'a[href*=article]',
		'title_filter' => '.post-content > .title',
		'post_category' => 2,
		'content_filter' => 'div.description',
		'image_filter' => 'div.description img',
		'iframe_filter' => 'div.description iframe',
		'tag_input' => array('Khmer News'),
		'feature_image_filter' => 'div.description img'
	),
	array(
		'link' => 'http://news.sabay.com.kh/topics/life',
		'href_filter' => '#posts_list a',
		'title_filter' => '.title.detail > p',
		'post_category' => 4,
		'content_filter' => 'div.post_content > .detail > p',
		'image_filter' => 'div.post_content > .detail > .content-grp-img > img',
		'iframe_filter' => 'div.post_content > .detail > p > iframe',
		'tag_input' => array('Sabay News'),
		'feature_image_filter' => 'div.post_content > .detail > .content-grp-img > img'
	),
	array(
		'link' => 'http://vayofm.com/health/',
		'href_filter' => 'media-heading.font-feature.cl-black > a',
		'title_filter' => 'detail-title > h2.cl-web',
		'post_category' => 4,
		'content_filter' => 'detail-text.font-content > p',
		'image_filter' => '.detail-thumbnail > img',
		'iframe_filter' => '#whole_sound_news',
		'tag_input' => array('Vayo'),
		'feature_image_filter' => '.detail-thumbnail > img'
	),
	array(
		'link' => 'http://www.rasmeinews.com/category/health/',
		'href_filter' => '.td-block-row .entry-title.td-module-title > h3 > a',
		'title_filter' => '.td-post-title > h1.entry-title',
		'post_category' => 4,
		'content_filter' => '.td-post-content > p',
		'image_filter' => '.td-post-content > p > img',
		'iframe_filter' => '.fb-video iframe',
		'tag_input' => array('រស្មីកម្ពុជា'),
		'feature_image_filter' => '.td-post-content > p > img'
	),
	array(
		'link' => 'http://www.cen.com.kh/archives/category/heal-beauty/',
		'href_filter' => 'h2.grid-title > a',
		'title_filter' => '.post-title.single-post-title',
		'post_category' => 4,
		'content_filter' => '.inner-post-entry > p',
		'image_filter' => '.inner-post-entry > p > img',
		'iframe_filter' => '.inner-post-entry > p iframe',
		'tag_input' => array(''),
		'feature_image_filter' => '.inner-post-entry > p > img'
	),
	array(
		'link' => 'https://www.khmerload.com/category/health',
		'href_filter' => 'div.homepage-zone-4 article.article-small > div.content > a',
		'title_filter' => 'div.article-header > h1',
		'post_category' => 4,
		'content_filter' => 'div.article-content > div > p',
		'image_filter' => 'div.article-content > div > figure > img',
		'iframe_filter' => 'div.article-content > div > figure > iframe',
		'tag_input' => array('Khmerload'),
		'feature_image_filter' => 'div.article-content > div > figure > img'
	),
	array(
		'link' => 'https://camnews.asia/archives/category/tips-health',
		'href_filter' => '.blog-listing-el a',
		'title_filter' => 'h1.single-title.post-title.entry-title',
		'post_category' => 4,
		'content_filter' => 'div.single-post-body > div.single-entry > p',
		'image_filter' => 'div.single-post-body > div.single-entry > p > img',
		'iframe_filter' => 'div.single-post-body > div.single-entry > div.fb-video',
		'tag_input' => array('Camnews'),
		'feature_image_filter' => 'div.post-thumb > img'
	),
	
);



foreach ($crawl_links as $crawl_link){
	$GLOBALS['crawl_link'] = $crawl_link;
	// TopNews4Khmer.com 

	try{
	
		$crawler = $client->request('GET', $GLOBALS['crawl_link']['link']);

		// $GLOBALS['i'] = 0;

		$links = $crawler->filter($GLOBALS['crawl_link']['href_filter'])->each(function($node_link){
			

			$GLOBALS['my_post'] = array();
			$GLOBALS['my_post']['post_category'] = array();
			$GLOBALS['my_post']['post_title'] = '';

			$post_link = $node_link->attr('href');


			if ($GLOBALS['crawl_link']['tag_input'][0] == 'Khmerload'){
				$post_link = 'https://www.khmerload.com'.$node_link->attr('href');
			}

			if ($GLOBALS['crawl_link']['tag_input'][0] == 'Khmer News'){
				$post_link = 'https://khmernews.news'.$node_link->attr('href');
			}

			$args = array("post_status" => array('publish'), "meta_key" => "source_link", "meta_value" =>$post_link);
			$posts = get_posts($args);
			
			
			if (count($posts) < 1){
				
				try{

					
					$client = new Client();
					$crawler = $client->request('GET', $post_link);
					
					$crawler->filter($GLOBALS['crawl_link']['title_filter'])->each(function ($node) {
						$GLOBALS['my_post']['post_title'] = remove_emoji($node->text());

					});

					
					if (count($GLOBALS['my_post']['post_category']) < 1){
						$GLOBALS['my_post']['post_category'][] = $GLOBALS['crawl_link']['post_category'];
					}


					$GLOBALS['post_content'] = array();
					
					
				
					try{
						$crawler->filter($GLOBALS['crawl_link']['content_filter'])->each(function ($node) {
							$html = html_entity_decode($node->html());
							
							if (strpos($html, '</script>') < 1){
								if ($node->text() != ''){
									$text = remove_emoji($node->text());
									
									$GLOBALS['post_content'][] = $text;	
								}
							}
							
						});
					}
					catch(Exception $e){
						$data = array(
						    'subject' => "Crawl Error Content",
						    'type' => "Error",
						    'crawl_link' => $GLOBALS['crawl_link']['link'],
						    'post_link' => $post_link,
						    'title' => $GLOBALS['my_post']['post_title'],
						    'content' => '',
						    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'app_link' => '',
						    'notification' => '',
						    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'detail_message' => $e
						);

						$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);


					}
					


					try{
						$image = $crawler->filter($GLOBALS['crawl_link']['image_filter'])->each(function($node){
							$image = $node->attr('src');

							

							if ($GLOBALS['crawl_link']['tag_input'][0] == 'Khmer News'){
								$image = 'https://khmernews.news'.$image;
							}

							if (strpos($image, 'http') > -1){
								$image = $image;	
							}
							else{
								$image = 'http:'.$image;
							}

							$GLOBALS['post_content'][] = '<img src="'.$image.'">';

							
							// $image = Generate_Image($image);
							// if (is_string($image)){
							// 	$GLOBALS['post_content'][] = $image;
								
							// }	
						});
						
					} catch (Exception $e){
						$data = array(
						    'subject' => "Crawl Error Image",
						    'type' => "Error",
						    'crawl_link' => $GLOBALS['crawl_link']['link'],
						    'post_link' => $post_link,
						    'title' => $GLOBALS['my_post']['post_title'],
						    'content' => '',
						    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'app_link' => '',
						    'notification' => '',
						    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'detail_message' => $e
						);

						$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);


					}
					

					try{
						$fb_video = $crawler->filter($GLOBALS['crawl_link']['iframe_filter'])->each(function($node){

							if ($GLOBALS['crawl_link']['tag_input'][0] == 'Camnews'){
								$fb_video = $node->attr('data-href');

								$str = '<iframe src="https://www.facebook.com/plugins/video.php?href='.urlencode($fb_video).'&width=360&show_text=false&appId=1905620589497116&height=360" width="360" height="360" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';
							}
							else{
								$fb_video = $node->attr('src');

								$str = '<iframe src="'.$fb_video.'" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';
							}
							
							$GLOBALS['post_content'][] = $str;
							
							
						});
						

					}catch(Exception $e){
						$data = array(
						    'subject' => "Crawl Error iFrame",
						    'type' => "Error",
						    'crawl_link' => $GLOBALS['crawl_link']['link'],
						    'post_link' => $post_link,
						    'title' => $GLOBALS['my_post']['post_title'],
						    'content' => '',
						    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'app_link' => '',
						    'notification' => '',
						    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
						    'detail_message' => $e
						);

						$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);


					}

					$post_content = "<p>".implode('</p><p>', $GLOBALS['post_content'])."</p>";
					
					
					
					

					if (strlen($post_content) > 200 && strlen($GLOBALS['my_post']['post_title']) > 100){

						$GLOBALS['my_post']['post_content'] = $post_content;

						$GLOBALS['my_post']['post_status'] = 'publish';
						$GLOBALS['my_post']['post_author'] = 1;
						
						$GLOBALS['my_post']['tags_input'] = $GLOBALS['crawl_link']['tag_input'];

						remove_all_filters("content_save_pre");
						$GLOBALS['post_id'] =  wp_insert_post( $GLOBALS['my_post'] );
						update_post_meta($GLOBALS['post_id'],'source_link',$post_link);


						try{
							$image = $crawler->filter($GLOBALS['crawl_link']['feature_image_filter'])->eq(0);
							$image = $image->attr('src');

							if ($GLOBALS['crawl_link']['tag_input'][0] == 'Khmer News'){
								$image = 'https://khmernews.news'.$image;
							}
							
							Generate_Featured_Image($image, $GLOBALS['post_id']);
						} catch (Exception $e){

							$data = array(
							    'subject' => "Crawl Error Featured Image",
							    'type' => "Error",
							    'crawl_link' => $GLOBALS['crawl_link']['link'],
							    'post_link' => $post_link,
							    'title' => $GLOBALS['my_post']['post_title'],
							    'content' => '',
							    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
							    'app_link' => '',
							    'notification' => '',
							    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
							    'detail_message' => $e
							);

							$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);


						}


						


						
						$apiKey = 'AAAA_k_ZUfc:APA91bH2e9MbpblAt81Kg-vYMVXaYaHtZen_Zm3XCssakrpHr5WLtWXMyYT9-PW35AriU76D_0eSRq_XX54aCot9tHsyq0wgHllisvN6RuvCD5x04XgwzIHmRkkRYtjzId5aciiisslz';
						$client = new FCMClient();
						$client->setApiKey($apiKey);
						$client->injectHttpClient(new \GuzzleHttp\Client());


						$message = new Message();
						$message->addRecipient(new Topic('news'));

						$message->setNotification(new Notification($GLOBALS['my_post']['post_title'], mb_substr($post_content, 3, 70, "UTF-8")))
						    ->setData(array('id' => $GLOBALS['post_id']));

						$response = $client->send($message);
						


						// $branch_key = 'key_live_leKpjCu7Ry6CklliGho9qnpdryljE4k5'; // your branch key.
						// $branch_secret = 'secret_live_7zwH7PiVZSV51CYU2MMdkq76SiNDk0fo';
						// $ch = curl_init('https://api.branch.io/v1/url');

						// $url = get_permalink($GLOBALS['post_id'], true);
						// $android_url = 'https://play.google.com/store/apps/details?id=com.khmernewslive24.app';


						// $payload = [
						// 'branch_key' => $branch_key,
					 //    'branch_secret' => $branch_secret,
					 //    'campaign' => 'Khmer News Live',
					 //    'channel' => "Facebook",
					 //    'type' => 2,
					 //    'alias' => $GLOBALS['post_id'],
					 //    'data' => [
					 //    	'$canonical_identifier' => 'article/'.$GLOBALS['post_id'],
					 //    	'$og_description' => mb_substr($post_content, 3, 70, "UTF-8"),
					 //        '$desktop_url' => $url,
					 //        '$ios_url' => $url,
					 //        '$ipad_url' => $url,
					 //        '$android_url' =>  $android_url,
					 //    	'$og_image_url' => get_the_post_thumbnail_url($GLOBALS['post_id']),
					 //    	'$og_title' => $GLOBALS['my_post']['post_title'],
					 //    	'photo_id' => get_post_thumbnail_id($GLOBALS['post_id']),
					 //    	'$og_app_id' => '583396836417491667',
					 //     	'$og_title' => $GLOBALS['my_post']['post_title'],
					 //     	'$marketing_title' => $GLOBALS['my_post']['post_title'],
					 //     	'$android_uri_scheme'=> 'khmernewslive://',
						// 	'$android_package_name'=> "com.khmernewslive24.app",
						// 	'$android_app_links_enabled'=> 1,
						// 	'$uri_redirect_mode' => 2
					 //    	]
					 //    ];

					    

						// curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
						// curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
						// # Return response instead of printing.
						// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
						// # Send request.
						// $result = curl_exec($ch);
						
						// $app_link = json_decode($result);

						// update_post_meta($GLOBALS['post_id'], 'app_link',  $app_link->url);

					}

				}
				catch(Exception $e){
					$data = array(
					    'subject' => "Crawl Error Post Link",
					    'type' => "Error",
					    'crawl_link' => $GLOBALS['crawl_link']['link'],
					    'post_link' => $post_link,
					    'title' => $GLOBALS['my_post']['post_title'],
					    'content' => '',
					    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
					    'app_link' => '',
					    'notification' => '',
					    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
					    'detail_message' => $e
					);

					$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);
				}
			}

			
		});
	}
	catch(Exception $e){
		$data = array(
		    'subject' => "Crawl Error Crawl",
		    'type' => "Error",
		    'crawl_link' => $GLOBALS['crawl_link']['link'],
		    'post_link' => '',
		    'title' => $GLOBALS['my_post']['post_title'],
		    'content' => '',
		    'iframe' => $GLOBALS['crawl_link']['feature_image_filter'],
		    'app_link' => '',
		    'notification' => '',
		    'featured_image' => $GLOBALS['crawl_link']['feature_image_filter'],
		    'detail_message' => $e
		);

		$request = Requests::post('https://www.khmernewslive24.com/webhook/send_email.php', array(), $data);


	}
}
// curl_close($ch);



function remove_emoji($text){
	return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
}